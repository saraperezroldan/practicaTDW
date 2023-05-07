<?php

/**
 * src/Controller/User/UpdateCommand.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Controller\User;

use Doctrine\ORM;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use TDW\ACiencia\Controller\TraitController;
use TDW\ACiencia\Entity\User;
use TDW\ACiencia\Utility\Error;
use Throwable;

/**
 * Class UpdateCommand
 */
class UpdateCommand
{
    use TraitController;

    /** @var string ruta api gestión usuarios  */
    public const PATH_USERS = '/users';

    // constructor receives container instance
    public function __construct(protected ORM\EntityManager $entityManager)
    {
    }

    /**
     * Summary: Updates a user
     * - A READER user can only modify their own properties
     * - A READER user cannot modify his ROLE
     *
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $args
     *
     * @return Response
     * @throws ORMException|OptimisticLockException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $isWriter = $this->checkWriterScope($request);
        if (!$isWriter && intval($args['userId']) !== $this->getUserId($request)) {
            return Error::createResponse($response, StatusCode::STATUS_NOT_FOUND); // 403 => 404 por seguridad
        }

        $req_data = $request->getParsedBody() ?? [];
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->find($args['userId']);

        if (!$user instanceof User) {    // 404
            return Error::createResponse($response, StatusCode::STATUS_NOT_FOUND);
        }

        // Optimistic Locking (strong validation)
        $etag = md5((string) json_encode($user));
        if (!in_array($etag, $request->getHeader('If-Match'))) {
            return Error::createResponse($response, StatusCode::STATUS_PRECONDITION_FAILED); // 412
        }

        if (isset($req_data['username'])) {
            $usuarioId = $this->findIdBy('username', $req_data['username']);
            if ($usuarioId && intval($args['userId']) !== $usuarioId) {
                // 400 BAD_REQUEST: username already exists
                return Error::createResponse($response, StatusCode::STATUS_BAD_REQUEST);
            }
            $user->setUsername($req_data['username']);
        }

        if (isset($req_data['email'])) {
            $usuarioId = $this->findIdBy('email', $req_data['email']);
            if ($usuarioId && intval($args['userId']) !== $usuarioId) {
                // 400 BAD_REQUEST: e-mail already exists
                return Error::createResponse($response, StatusCode::STATUS_BAD_REQUEST);
            }
            $user->setEmail($req_data['email']);
        }

        // password
        if (isset($req_data['password'])) {
            $user->setPassword($req_data['password']);
        }

        // role
        if ($isWriter && isset($req_data['role'])) {
            try {
                $user->setRole($req_data['role']);
            } catch (Throwable) {    // 400 BAD_REQUEST: unexpected role
                return Error::createResponse($response, StatusCode::STATUS_BAD_REQUEST);
            }
        }

        $this->entityManager->flush();

        return $response
            ->withStatus(209, 'Content Returned')
            ->withJson($user);
    }

    /**
     * Determines if a value exists for an attribute
     */
    private function findIdBy(string $attr, string $value): int
    {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy([ $attr => $value ]);
        return $user?->getId() ?? 0;
    }
}
