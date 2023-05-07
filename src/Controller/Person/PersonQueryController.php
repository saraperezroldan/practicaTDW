<?php

/**
 * src/Controller/Person/PersonQueryController.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Controller\Person;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use TDW\ACiencia\Controller\Element\ElementBaseQueryController;
use TDW\ACiencia\Entity\Person;

/**
 * Class PersonQueryController
 */
class PersonQueryController extends ElementBaseQueryController
{
    /** @var string ruta api gestión personas  */
    public const PATH_PERSONS = '/persons';

    public static function getEntitiesTag(): string
    {
        return 'persons';
    }

    public static function getEntityClassName(): string
    {
        return Person::class;
    }

    public static function getEntityIdName(): string
    {
        return 'personId';
    }

    /**
     * Summary: Returns status code 204 if personname exists
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     *
     * @return Response
     */
    public function getPersonname(Request $request, Response $response, array $args): Response
    {
        // @TODO
    }
}
