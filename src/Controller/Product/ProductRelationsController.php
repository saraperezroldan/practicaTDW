<?php

/**
 * src/Controller/Product/ProductRelationsController.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Controller\Product;

use Doctrine\ORM;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use TDW\ACiencia\Controller\Element\ElementRelationsBaseController;
use TDW\ACiencia\Controller\Entity\EntityQueryController;
use TDW\ACiencia\Controller\Person\PersonQueryController;
use TDW\ACiencia\Entity\Product;

/**
 * Class ProductRelationsController
 */
final class ProductRelationsController extends ElementRelationsBaseController
{
    public static function getEntityClassName(): string
    {
        return ProductQueryController::getEntityClassName();
    }

    public static function getEntitiesTag(): string
    {
        return ProductQueryController::getEntitiesTag();
    }

    public static function getEntityIdName(): string
    {
        return ProductQueryController::getEntityIdName();
    }

    /**
     * Summary: GET /products/{productId}/entities
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     *
     * @return Response
     */
    public function getEntities(Request $request, Response $response, array $args): Response
    {
        /** @var Product|null $product */
        $product = $this->entityManager
            ->getRepository(ProductQueryController::getEntityClassName())
            ->find($args[ProductQueryController::getEntityIdName()]);

        $entities = $product?->getEntities() ?? [];

        return $this->getElements($response, $product, EntityQueryController::getEntitiesTag(), $entities);
    }

    /**
     * PUT /products/{productId}/entities/add/{stuffId}
     * PUT /products/{productId}/entities/rem/{stuffId}
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     *
     * @return Response
     * @throws ORM\Exception\ORMException
     */
    public function operationEntity(Request $request, Response $response, array $args): Response
    {
        return $this->operationRelatedElements(
            $request,
            $response,
            $args,
            EntityQueryController::getEntityClassName()
        );
    }

    /**
     * Summary: GET /products/{productId}/persons
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     *
     * @return Response
     */
    public function getPersons(Request $request, Response $response, array $args): Response
    {
        /** @var Product|null $product */
        $product = $this->entityManager
            ->getRepository(ProductQueryController::getEntityClassName())
            ->find($args[ProductQueryController::getEntityIdName()]);

        $persons = $product?->getPersons() ?? [];

        return $this->getElements($response, $product, PersonQueryController::getEntitiesTag(), $persons);
    }

    /**
     * PUT /products/{productId}/persons/add/{stuffId}
     * PUT /products/{productId}/persons/rem/{stuffId}
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     *
     * @return Response
     * @throws ORM\Exception\ORMException
     */
    public function operationPerson(Request $request, Response $response, array $args): Response
    {
        return $this->operationRelatedElements(
            $request,
            $response,
            $args,
            PersonQueryController::getEntityClassName()
        );
    }
}
