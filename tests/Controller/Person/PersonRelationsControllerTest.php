<?php

/**
 * tests/Controller/Person/PersonRelationsControllerTest.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\Test\ACiencia\Controller\Person;

use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use TDW\ACiencia\Controller\Person\PersonQueryController;
use TDW\ACiencia\Controller\Person\PersonRelationsController;
use TDW\ACiencia\Entity\ElementInterface;
use TDW\ACiencia\Entity\Entity;
use TDW\ACiencia\Entity\Person;
use TDW\ACiencia\Entity\Product;
use TDW\ACiencia\Factory\EntityFactory;
use TDW\ACiencia\Factory\PersonFactory;
use TDW\ACiencia\Factory\ProductFactory;
use TDW\ACiencia\Utility\DoctrineConnector;
use TDW\ACiencia\Utility\Utils;
use TDW\Test\ACiencia\Controller\BaseTestCase;

/**
 * Class PersonRelationsControllerTest
 */
final class PersonRelationsControllerTest extends BaseTestCase
{
    /** @var string Path para la gestión de personas */
    protected const RUTA_API = '/api/v1/persons';

    /** @var array<string,mixed> Admin data */
    protected static array $writer;

    /** @var array<string,mixed> reader user data */
    protected static array $reader;

    protected static EntityManagerInterface $entityManager;

    private static Person|ElementInterface $person;
    private static Entity|ElementInterface $entity;
    private static Product|ElementInterface $product;

    /**
     * Se ejecuta una vez al inicio de las pruebas de la clase UserControllerTest
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$writer = [
            'username' => (string) getenv('ADMIN_USER_NAME'),
            'email'    => (string) getenv('ADMIN_USER_EMAIL'),
            'password' => (string) getenv('ADMIN_USER_PASSWD'),
        ];

        // load user admin fixtures
        self::$writer['id'] = Utils::loadUserData(
            self::$writer['username'],
            self::$writer['email'],
            self::$writer['password'],
            true
        );

        // load user reader fixtures
        self::$reader = [
            'username' => self::$faker->userName(),
            'email'    => self::$faker->email(),
            'password' => self::$faker->password(),
        ];
        self::$reader['id'] = Utils::loadUserData(
            (string) self::$reader['username'],
            (string) self::$reader['email'],
            (string) self::$reader['password'],
            false
        );

        // create and insert fixtures
        self::$person  = PersonFactory::createElement(self::$faker->name());
        self::$entity  = EntityFactory::createElement(self::$faker->company());
        self::$product = ProductFactory::createElement(self::$faker->word());

        self::$entityManager = DoctrineConnector::getEntityManager();
        self::$entityManager->persist(self::$person);
        self::$entityManager->persist(self::$entity);
        self::$entityManager->persist(self::$product);
        self::$entityManager->flush();
    }

    public function testGetEntitiesTag(): void
    {
        self::assertSame(
            PersonQueryController::getEntitiesTag(),
            PersonRelationsController::getEntitiesTag()
        );
    }

    // *******************
    // Person -> Entities
    // *******************
    /**
     * PUT /persons/{personId}/entities/add/{stuffId}
     */
    public function testAddEntity209(): void
    {
        self::$writer['authHeader'] = $this->getTokenHeaders(self::$writer['username'], self::$writer['password']);
        $response = $this->runApp(
            'PUT',
            self::RUTA_API . '/' . self::$person->getId()
                . '/entities/add/' . self::$entity->getId(),
            null,
            self::$writer['authHeader']
        );
        self::assertSame(209, $response->getStatusCode());
        self::assertJson($response->getBody()->getContents());
    }

    /**
     * GET /persons/{personId}/entities 200 Ok
     *
     * @depends testAddEntity209
     * @throws JsonException
     */
    public function testGetEntities200OkWithElements(): void
    {
        self::$reader['authHeader'] = $this->getTokenHeaders(self::$reader['username'], self::$reader['password']);
        $response = $this->runApp(
            'GET',
            self::RUTA_API . '/' . self::$person->getId() . '/entities',
            null,
            self::$reader['authHeader']
        );
        self::assertSame(200, $response->getStatusCode());
        $r_body = $response->getBody()->getContents();
        self::assertJson($r_body);
        $responseEntities = json_decode($r_body, true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('entities', $responseEntities);
        self::assertSame(
            self::$entity->getName(),
            $responseEntities['entities'][0]['entity']['name']
        );
    }

    /**
     * PUT /persons/{personId}/entities/rem/{stuffId}
     *
     * @depends testGetEntities200OkWithElements
     * @throws JsonException
     */
    public function testRemoveEntity209(): void
    {
        $response = $this->runApp(
            'PUT',
            self::RUTA_API . '/' . self::$person->getId()
            . '/entities/rem/' . self::$entity->getId(),
            null,
            self::$writer['authHeader']
        );
        self::assertSame(209, $response->getStatusCode());
        $r_body = $response->getBody()->getContents();
        self::assertJson($r_body);
        $responsePerson = json_decode($r_body, true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('entities', $responsePerson['person']);
        self::assertEmpty($responsePerson['person']['entities']);
    }

    /**
     * GET /persons/{personId}/entities 200 Ok - Empty
     *
     * @depends testRemoveEntity209
     * @throws JsonException
     */
    public function testGetEntities200OkEmpty(): void
    {
        $response = $this->runApp(
            'GET',
            self::RUTA_API . '/' . self::$person->getId() . '/entities',
            null,
            self::$reader['authHeader']
        );
        self::assertSame(200, $response->getStatusCode());
        $r_body = $response->getBody()->getContents();
        self::assertJson($r_body);
        $responseEntities = json_decode($r_body, true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('entities', $responseEntities);
        self::assertEmpty($responseEntities['entities']);
    }

    // ******************
    // Person -> Products
    // ******************
    /**
     * PUT /persons/{personId}/products/add/{stuffId}
     */
    public function testAddProduct209(): void
    {
        self::$writer['authHeader'] = $this->getTokenHeaders(self::$writer['username'], self::$writer['password']);
        $response = $this->runApp(
            'PUT',
            self::RUTA_API . '/' . self::$person->getId()
            . '/products/add/' . self::$product->getId(),
            null,
            self::$writer['authHeader']
        );
        self::assertSame(209, $response->getStatusCode());
        self::assertJson($response->getBody()->getContents());
    }

    /**
     * GET /persons/{personId}/products 200 Ok
     *
     * @depends testAddProduct209
     * @throws JsonException
     */
    public function testGetProducts200OkWithElements(): void
    {
        self::$reader['authHeader'] = $this->getTokenHeaders(self::$reader['username'], self::$reader['password']);
        $response = $this->runApp(
            'GET',
            self::RUTA_API . '/' . self::$person->getId() . '/products',
            null,
            self::$reader['authHeader']
        );
        self::assertSame(200, $response->getStatusCode());
        $r_body = $response->getBody()->getContents();
        self::assertJson($r_body);
        $responseProducts = json_decode($r_body, true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('products', $responseProducts);
        self::assertSame(
            self::$product->getName(),
            $responseProducts['products'][0]['product']['name']
        );
    }

    /**
     * PUT /persons/{personId}/products/rem/{stuffId}
     *
     * @depends testGetProducts200OkWithElements
     * @throws JsonException
     */
    public function testRemoveProduct209(): void
    {
        $response = $this->runApp(
            'PUT',
            self::RUTA_API . '/' . self::$person->getId()
            . '/products/rem/' . self::$product->getId(),
            null,
            self::$writer['authHeader']
        );
        self::assertSame(209, $response->getStatusCode());
        $r_body = $response->getBody()->getContents();
        self::assertJson($r_body);
        $responsePerson = json_decode($r_body, true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('products', $responsePerson['person']);
        self::assertEmpty($responsePerson['person']['products']);
    }

    /**
     * GET /persons/{personId}/products 200 Ok - Empty
     *
     * @depends testRemoveProduct209
     * @throws JsonException
     */
    public function testGetProducts200OkEmpty(): void
    {
        $response = $this->runApp(
            'GET',
            self::RUTA_API . '/' . self::$person->getId() . '/products',
            null,
            self::$reader['authHeader']
        );
        self::assertSame(200, $response->getStatusCode());
        $r_body = $response->getBody()->getContents();
        self::assertJson($r_body);
        $responseProducts = json_decode($r_body, true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('products', $responseProducts);
        self::assertEmpty($responseProducts['products']);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param int $status
     * @param string $user
     * @return void
     *
     * @dataProvider routeExceptionProvider
     */
    public function testPersonRelationshipErrors(string $method, string $uri, int $status, string $user = ''): void
    {
        $requestingUser = match ($user) {
            'admin'  => self::$writer,
            'reader' => self::$reader,
            default  => ['username' => '', 'password' => '']
        };

        $response = $this->runApp(
            $method,
            $uri,
            null,
            $this->getTokenHeaders($requestingUser['username'], $requestingUser['password'])
        );
        $this->internalTestError($response, $status);
    }

    // --------------
    // DATA PROVIDERS
    // --------------

    /**
     * Route provider (expected status: 404 NOT FOUND)
     *
     * @return array<string,mixed> [ method, url, status, user ]
     */
    public static function routeExceptionProvider(): array
    {
        return [
            // 401
            // 'getEntities401'     => [ 'GET', self::RUTA_API . '/1/entities',       401],
            'putAddEntity401'    => [ 'PUT', self::RUTA_API . '/1/entities/add/1', 401],
            'putRemoveEntity401' => [ 'PUT', self::RUTA_API . '/1/entities/rem/1', 401],
            // 'getProducts401'      => [ 'GET', self::RUTA_API . '/1/products',        401],
            'putAddProduct401'    => [ 'PUT', self::RUTA_API . '/1/products/add/1',  401],
            'putRemoveProduct401' => [ 'PUT', self::RUTA_API . '/1/products/rem/1',  401],

            // 403
            'putAddEntity403'    => [ 'PUT', self::RUTA_API . '/1/entities/add/1', 403, 'reader'],
            'putRemoveEntity403' => [ 'PUT', self::RUTA_API . '/1/entities/rem/1', 403, 'reader'],
            'putAddProduct403'    => [ 'PUT', self::RUTA_API . '/1/products/add/1',  403, 'reader'],
            'putRemoveProduct403' => [ 'PUT', self::RUTA_API . '/1/products/rem/1',  403, 'reader'],

            // 404
            'getEntities404'     => [ 'GET', self::RUTA_API . '/0/entities',       404, 'admin'],
            'putAddEntity404'    => [ 'PUT', self::RUTA_API . '/0/entities/add/1', 404, 'admin'],
            'putRemoveEntity404' => [ 'PUT', self::RUTA_API . '/0/entities/rem/1', 404, 'admin'],
            'getProducts404'      => [ 'GET', self::RUTA_API . '/0/products',        404, 'admin'],
            'putAddProduct404'    => [ 'PUT', self::RUTA_API . '/0/products/add/1',  404, 'admin'],
            'putRemoveProduct404' => [ 'PUT', self::RUTA_API . '/0/products/rem/1',  404, 'admin'],

            // 406
            'putAddEntity406'    => [ 'PUT', self::RUTA_API . '/1/entities/add/100', 406, 'admin'],
            'putRemoveEntity406' => [ 'PUT', self::RUTA_API . '/1/entities/rem/100', 406, 'admin'],
            'putAddProduct406'    => [ 'PUT', self::RUTA_API . '/1/products/add/100',  406, 'admin'],
            'putRemoveProduct406' => [ 'PUT', self::RUTA_API . '/1/products/rem/100',  406, 'admin'],
        ];
    }
}
