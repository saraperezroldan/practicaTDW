<?php

/**
 * src/Controller/Element/ElementBaseQueryController.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Controller\Element;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use Slim\Routing\RouteContext;
use TDW\ACiencia\Entity\Element;
use TDW\ACiencia\Utility\Error;

/**
 * Class ElementBaseQueryController
 */
abstract class ElementBaseQueryController
{
    // constructor receives the EntityManager from container instance
    public function __construct(protected ORM\EntityManager $entityManager)
    {
    }

    /**
     * Tag name
     */
    abstract public static function getEntitiesTag(): string;

    /**
     * @return class-string Name of the controlled class
     */
    abstract public static function getEntityClassName(): string;

    /**
     * Entity Id name parameter [ 'entityId' | 'productId' | 'personId' ]
     */
    abstract public static function getEntityIdName(): string;

    /**
     * Summary: Returns all elements
     *
     * @todo add pagination
     * @todo add filtering
     */
    public function cget(Request $request, Response $response): Response
    {
        /** @var array<string,string> $params */
        $params = $request->getQueryParams();
        $criteria = new Criteria();
        if (array_key_exists('order', $params)) {
            $order = (in_array($params['order'], ['id', 'name'])) ? $params['order'] : null;
        }
        if (array_key_exists('ordering', $params)) {
            $ordering = ('DESC' === $params['ordering']) ? 'DESC' : null;
        }
        $criteria->orderBy([$order ?? 'id' => $ordering ?? 'ASC']);

        $elements = $this->entityManager
            ->getRepository(static::getEntityClassName())
            ->matching($criteria)
            ->getValues();

        if (0 === count($elements)) {    // 404
            return Error::createResponse($response, StatusCode::STATUS_NOT_FOUND);
        }

        // Caching with ETag
        $etag = md5((string) json_encode($elements));
        if ($request->hasHeader('If-None-Match') && in_array($etag, $request->getHeader('If-None-Match'))) {
            return $response->withStatus(StatusCode::STATUS_NOT_MODIFIED); // 304
        }

        return $response
            ->withAddedHeader('ETag', $etag)
            ->withAddedHeader('Cache-Control', 'private')
            ->withJson([ static::getEntitiesTag() => $elements ]);
    }

    /**
     * Summary: Returns a element based on a single id
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     * @return Response
     */
    public function get(Request $request, Response $response, array $args): Response
    {
        $idName = static::getEntityIdName();
        $element = $this->entityManager->getRepository(static::getEntityClassName())
            ->find($args[$idName]);
        if (!$element instanceof Element) {
            return Error::createResponse($response, StatusCode::STATUS_NOT_FOUND);
        }

        // Caching with ETag
        $etag = md5((string) json_encode($element));
        if (in_array($etag, $request->getHeader('If-None-Match'))) {
            return $response->withStatus(StatusCode::STATUS_NOT_MODIFIED); // 304
        }

        return $response
            ->withAddedHeader('ETag', $etag)
            ->withAddedHeader('Cache-Control', 'private')
            ->withJson($element);
    }

    /**
     * Summary: Returns status code 204 if _elementName_ exists
     */
    public function getElementByName(Response $response, string $elementName): Response
    {
        $element = $this->entityManager
            ->getRepository(static::getEntityClassName())
            ->findOneBy([ 'name' => $elementName ]);

        return ($element instanceof Element)
            ? $response->withStatus(StatusCode::STATUS_NO_CONTENT)       // 204
            : Error::createResponse($response, StatusCode::STATUS_NOT_FOUND); // 404
    }

    /**
     * Summary: Provides the list of HTTP supported methods
     */
    public function options(Request $request, Response $response): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $routingResults = $routeContext->getRoutingResults();
        $methods = $routingResults->getAllowedMethods();

        return $response
            ->withStatus(204)
            ->withAddedHeader('Cache-Control', 'private')
            ->withAddedHeader(
                'Allow',
                implode(',', $methods)
            );
    }
}
