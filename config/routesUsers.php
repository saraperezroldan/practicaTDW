<?php

/**
 * config/routesUsers.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

use Slim\App;
use TDW\ACiencia\Controller\User\DeleteCommand;
use TDW\ACiencia\Controller\User\OptionsQuery;
use TDW\ACiencia\Controller\User\ReadQuery;
use TDW\ACiencia\Controller\User\ReadUsernameQuery;
use TDW\ACiencia\Controller\User\UpdateCommand;
use TDW\ACiencia\Controller\User\CreateCommand;
use TDW\ACiencia\Controller\User\ReadAllQuery;
use TDW\ACiencia\Middleware\JwtMiddleware;

/**
 * ############################################################
 * routes /api/v1/users
 * ############################################################
 * @param App $app
 */
return function (App $app) {

    $REGEX_USER_ID = '/{userId:[0-9]+}';
    $REGEX_USERNAME = '/{username:[a-zA-Z0-9()áéíóúÁÉÍÓÚñÑ %$\.+-]+}';

    // CGET: Returns all users
    $app->get(
        $_ENV['RUTA_API'] . ReadAllQuery::PATH_USERS,
        ReadAllQuery::class
    )->setName('tdw_users_cget')
        ->add(JwtMiddleware::class);

    // GET: Returns a user based on a single ID
    $app->get(
        $_ENV['RUTA_API'] . ReadAllQuery::PATH_USERS . $REGEX_USER_ID,
        ReadQuery::class
    )->setName('tdw_users_read')
        ->add(JwtMiddleware::class);

    // GET: Returns status code 204 if username exists
    $app->get(
        $_ENV['RUTA_API'] . ReadAllQuery::PATH_USERS . '/username' . $REGEX_USERNAME,
        ReadUsernameQuery::class
    )->setName('tdw_users_get_username');

    // OPTIONS: Provides the list of HTTP supported methods
    $app->options(
        $_ENV['RUTA_API'] . ReadAllQuery::PATH_USERS . '[' . $REGEX_USER_ID . ']',
        OptionsQuery::class
    )->setName('tdw_users_options');

    // DELETE: Deletes a user
    $app->delete(
        $_ENV['RUTA_API'] . UpdateCommand::PATH_USERS . $REGEX_USER_ID,
        DeleteCommand::class
    )->setName('tdw_users_delete')
        ->add(JwtMiddleware::class);

    // POST: Creates a new user
    $app->post(
        $_ENV['RUTA_API'] . UpdateCommand::PATH_USERS,
        CreateCommand::class
    )->setName('tdw_users_create')
        ->add(JwtMiddleware::class);

    // PUT: Updates a user
    $app->put(
        $_ENV['RUTA_API'] . UpdateCommand::PATH_USERS . $REGEX_USER_ID,
        UpdateCommand::class
    )->setName('tdw_users_update')
        ->add(JwtMiddleware::class);
};
