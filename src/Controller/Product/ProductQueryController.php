<?php

/**
 * src/Controller/Product/ProductQueryController.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Controller\Product;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use TDW\ACiencia\Controller\Element\ElementBaseQueryController;
use TDW\ACiencia\Entity\Product;

/**
 * Class ProductQueryController
 */
class ProductQueryController extends ElementBaseQueryController
{
    /** @var string ruta api gestión productos  */
    public const PATH_PRODUCTS = '/products';

    public static function getEntitiesTag(): string
    {
        return 'products';
    }

    public static function getEntityClassName(): string
    {
        return Product::class;
    }

    public static function getEntityIdName(): string
    {
        return 'productId';
    }

    /**
     * Summary: Returns status code 204 if productname exists
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     *
     * @return Response
     */
    public function getProductname(Request $request, Response $response, array $args): Response
    {
        return $this->getElementByName($response, $args['productname']);
    }
}
