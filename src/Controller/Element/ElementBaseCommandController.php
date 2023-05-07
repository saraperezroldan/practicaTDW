<?php

/**
 * src/Controller/ElementBaseCommandController.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Controller\Element;

use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use TDW\ACiencia\Controller\TraitController;
use TDW\ACiencia\Entity\Element;
use TDW\ACiencia\Utility\Error;

/**
 * Class ElementBaseCommandController
 */
abstract class ElementBaseCommandController
{
    use TraitController;

    // constructor receives the EntityManager from container instance
    public function __construct(protected ORM\EntityManager $entityManager)
    {
    }

    /**
     * @return class-string Name of the controlled class
     */
    abstract public static function getEntityClassName(): string;

    /**
     * Entity Id name parameter [ 'entityId' | 'productId' | 'personId' ]
     */
    abstract public static function getEntityIdName(): string;

    /**
     * Summary: Deletes a element
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     * @return Response
     * @throws ORM\Exception\ORMException|ORM\OptimisticLockException
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        if (!$this->checkWriterScope($request)) { // 403 => 404 por seguridad
            return Error::createResponse($response, StatusCode::STATUS_NOT_FOUND);
        }

        $idName = static::getEntityIdName();
        $element = $this->entityManager->getRepository(static::getEntityClassName())->find($args[$idName]);

        if (!$element instanceof Element) {    // 404
            return Error::createResponse($response, StatusCode::STATUS_NOT_FOUND);
        }

        $this->entityManager->remove($element);
        $this->entityManager->flush();

        return $response
            ->withStatus(StatusCode::STATUS_NO_CONTENT);  // 204
    }

    /**
     * Summary: Creates a new element
     *
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ORM\Exception\ORMException
     */
    public function post(Request $request, Response $response): Response
    {
        if (!$this->checkWriterScope($request)) { // 403
            return Error::createResponse($response, StatusCode::STATUS_FORBIDDEN);
        }

        $req_data = (array) $request->getParsedBody();

        if (!isset($req_data['name'])) { // 422 - Faltan datos
            return Error::createResponse($response, StatusCode::STATUS_UNPROCESSABLE_ENTITY);
        }

        // hay datos -> procesarlos
        $criteria = new Criteria(Criteria::expr()->eq('name', $req_data['name']));
        // STATUS_BAD_REQUEST 400: element name already exists
        if ($this->entityManager->getRepository(static::getEntityClassName())->matching($criteria)->count()) {
            return Error::createResponse($response, StatusCode::STATUS_BAD_REQUEST);
        }

        // 201
        $entityName = static::getEntityClassName();
        /** @var Element $element */
        $element = new $entityName($req_data['name']);
        $this->updateElement($element, $req_data);
        $this->entityManager->persist($element);
        $this->entityManager->flush();

        return $response
            ->withAddedHeader(
                'Location',
                $request->getUri() . '/' . $element->getId()
            )
            ->withJson($element, StatusCode::STATUS_CREATED);
    }

    /**
     * Summary: Updates a element
     *
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     * @return Response
     * @throws ORM\Exception\ORMException
     */
    public function put(Request $request, Response $response, array $args): Response
    {
        if (!$this->checkWriterScope($request)) { // 403 => 404 por seguridad
            return Error::createResponse($response, StatusCode::STATUS_NOT_FOUND);
        }

        $req_data = (array) $request->getParsedBody();
        // recuperar el elemento
        $idName = static::getEntityIdName();
        /** @var Element|null $element */
        $element = $this->entityManager->getRepository(static::getEntityClassName())->find($args[$idName]);

        if (!$element instanceof Element) {    // 404
            return Error::createResponse($response, StatusCode::STATUS_NOT_FOUND);
        }

        // Optimistic Locking (strong validation)
        $etag = md5((string) json_encode($element));
        if (!in_array($etag, $request->getHeader('If-Match'))) {
            return Error::createResponse($response, StatusCode::STATUS_PRECONDITION_FAILED); // 412
        }

        if (isset($req_data['name'])) { // 400
            $elementId = $this->findIdByName(static::getEntityClassName(), $req_data['name']);
            if ($elementId && (intval($args[$idName]) !== $elementId)) {
                // 400 BAD_REQUEST: elementname already exists
                return Error::createResponse($response, StatusCode::STATUS_BAD_REQUEST);
            }
            $element->setName($req_data['name']);
        }

        $this->updateElement($element, $req_data);
        $this->entityManager->flush();

        return $response
            ->withStatus(209, 'Content Returned')
            ->withJson($element);
    }

    /**
     * Determines if a value exists for an attribute
     *
     * @param class-string $entityName
     * @param string $value
     * @return int
     */
    private function findIdByName(string $entityName, string $value): int
    {
        /** @var ?Element $element */
        $element = $this->entityManager->getRepository($entityName)->findOneBy([ 'name' => $value ]);
        return (int) $element?->getId();
    }

    /**
     * Update $element with $data attributes
     *
     * @param Element $element
     * @param array<string,string> $data
     */
    private function updateElement(Element $element, array $data): void
    {
        foreach ($data as $attr => $datum) {
            switch ($attr) {
                case 'birthDate':
                    if ($date = DateTime::createFromFormat('!Y-m-d', $datum)) {
                        $element->setBirthDate($date);
                    }
                    break;
                case 'deathDate':
                    if ($date = DateTime::createFromFormat('!Y-m-d', $datum)) {
                        $element->setDeathDate($date);
                    }
                    break;
                case 'imageUrl':
                    $element->setImageUrl($datum);
                    break;
                case 'wikiUrl':
                    $element->setWikiUrl($datum);
                    break;
            }
        }
    }
}
