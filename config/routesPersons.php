<?php

/**
 * config/routesPersons.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

use Slim\App;
use TDW\ACiencia\Controller\Person\PersonCommandController;
use TDW\ACiencia\Controller\Person\PersonQueryController;
use TDW\ACiencia\Controller\Person\PersonRelationsController;
use TDW\ACiencia\Middleware\JwtMiddleware;

/**
 * ############################################################
 * routes /api/v1/products
 * ############################################################
 * @param App $app
 */
return function (App $app) {

    $REGEX_PERSON_ID = '/{personId:[0-9]+}';
    $REGEX_ELEMENT_ID = '/{elementId:[0-9]+}';
    $REGEX_PERSON_NAME = '[a-zA-Z0-9()áéíóúÁÉÍÓÚñÑ %$\.+-]+';

    // CGET: Returns all persons
    $app->get(
        $_ENV['RUTA_API'] . PersonQueryController::PATH_PERSONS,
        PersonQueryController::class . ':cget'
    )->setName('readPersons');
    //    ->add(JwtMiddleware::class);

    // GET: Returns a person based on a single ID
    $app->get(
        $_ENV['RUTA_API'] . PersonQueryController::PATH_PERSONS . $REGEX_PERSON_ID,
        PersonQueryController::class . ':get'
    )->setName('readPerson');
    //    ->add(JwtMiddleware::class);

    // GET: Returns status code 204 if personname exists
    $app->get(
        $_ENV['RUTA_API'] . PersonQueryController::PATH_PERSONS . '/personname/{personname:' . $REGEX_PERSON_NAME . '}',
        PersonQueryController::class . ':getPersonname'
    )->setName('existsPerson');

    // OPTIONS: Provides the list of HTTP supported methods
    $app->options(
        $_ENV['RUTA_API'] . PersonQueryController::PATH_PERSONS . '[' . $REGEX_PERSON_ID . ']',
        PersonQueryController::class . ':options'
    )->setName('optionsPerson');

    // DELETE: Deletes a person
    $app->delete(
        $_ENV['RUTA_API'] . PersonCommandController::PATH_PERSONS . $REGEX_PERSON_ID,
        PersonCommandController::class . ':delete'
    )->setName('deletePerson')
        ->add(JwtMiddleware::class);

    // POST: Creates a new person
    $app->post(
        $_ENV['RUTA_API'] . PersonCommandController::PATH_PERSONS,
        PersonCommandController::class . ':post'
    )->setName('createPerson')
        ->add(JwtMiddleware::class);

    // PUT: Updates a person
    $app->put(
        $_ENV['RUTA_API'] . PersonCommandController::PATH_PERSONS . $REGEX_PERSON_ID,
        PersonCommandController::class . ':put'
    )->setName('updatePerson')
        ->add(JwtMiddleware::class);

    // RELATIONSHIPS

    // GET /persons/{personId}/entities
    $app->get(
        $_ENV['RUTA_API'] . PersonQueryController::PATH_PERSONS . $REGEX_PERSON_ID . '/entities',
        PersonRelationsController::class . ':getEntities'
    )->setName('readPersonEntities');
    //    ->add(JwtMiddleware::class);

    // PUT /persons/{personId}/entities/add/{elementId}
    $app->put(
        $_ENV['RUTA_API'] . PersonCommandController::PATH_PERSONS . $REGEX_PERSON_ID
            . '/entities/add' . $REGEX_ELEMENT_ID,
        PersonRelationsController::class . ':operationEntity'
    )->setName('tdw_persons_add_entity')
        ->add(JwtMiddleware::class);

    // PUT /persons/{personId}/entities/rem/{elementId}
    $app->put(
        $_ENV['RUTA_API'] . PersonCommandController::PATH_PERSONS . $REGEX_PERSON_ID
            . '/entities/rem' . $REGEX_ELEMENT_ID,
        PersonRelationsController::class . ':operationEntity'
    )->setName('tdw_persons_rem_entity')
        ->add(JwtMiddleware::class);

    // GET /persons/{personId}/products
    $app->get(
        $_ENV['RUTA_API'] . PersonQueryController::PATH_PERSONS . $REGEX_PERSON_ID . '/products',
        PersonRelationsController::class . ':getProducts'
    )->setName('readPersonProducts');
    //    ->add(JwtMiddleware::class);

    // PUT /persons/{personId}/products/add/{elementId}
    $app->put(
        $_ENV['RUTA_API'] . PersonCommandController::PATH_PERSONS . $REGEX_PERSON_ID
            . '/products/add' . $REGEX_ELEMENT_ID,
        PersonRelationsController::class . ':operationProduct'
    )->setName('tdw_persons_add_product')
        ->add(JwtMiddleware::class);

    // PUT /persons/{personId}/products/rem/{elementId}
    $app->put(
        $_ENV['RUTA_API'] . PersonCommandController::PATH_PERSONS . $REGEX_PERSON_ID
        . '/products/rem' . $REGEX_ELEMENT_ID,
        PersonRelationsController::class . ':operationProduct'
    )->setName('tdw_persons_rem_product')
        ->add(JwtMiddleware::class);
};
