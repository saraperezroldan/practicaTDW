<?php

/**
 * src/Controller/Entity/EntityQueryController.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Controller\Entity;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use TDW\ACiencia\Controller\Element\ElementBaseQueryController;
use TDW\ACiencia\Entity\Entity;

/**
 * Class EntityQueryController
 */
class EntityQueryController extends ElementBaseQueryController
{
    /** @var string ruta api gestión entidades  */
    public const PATH_ENTITIES = '/entities';

    public static function getEntitiesTag(): string
    {
        return 'entities';
    }

    public static function getEntityClassName(): string
    {
        return Entity::class;
    }

    public static function getEntityIdName(): string
    {
        return 'entityId';
    }

    /**
     * Summary: Returns status code 204 if entityname exists
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     * @return Response
     */
    public function getEntityname(Request $request, Response $response, array $args): Response
    {
        // @TODO
    }
}
