<?php

/**
 * tests/Entity/PersonTest.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\Test\ACiencia\Entity;

use PHPUnit\Framework\TestCase;
use TDW\ACiencia\Entity\Entity;
use TDW\ACiencia\Entity\Person;
use TDW\ACiencia\Entity\Product;
use TDW\ACiencia\Factory;

/**
 * Class PersonTest
 *
 * @group   persons
 * @coversDefaultClass \TDW\ACiencia\Entity\Person
 */
class PersonTest extends TestCase
{
    protected static Person $person;

    private static \Faker\Generator $faker;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    public static function setUpBeforeClass(): void
    {
        self::$faker = \Faker\Factory::create('es_ES');
        self::$person  = Factory\PersonFactory::createElement('');
    }

    /**
     * @return void
     * @covers \TDW\ACiencia\Factory\PersonFactory::createElement
     */
    public function testConstructor(): void
    {
        $name = self::$faker->name();
        self::$person = Factory\PersonFactory::createElement($name);
        self::assertSame(0, self::$person->getId());
        self::assertSame(
            $name,
            self::$person->getName()
        );
        self::assertEmpty(self::$person->getProducts());
        self::assertEmpty(self::$person->getEntities());
    }

    public function testGetId(): void
    {
        self::assertSame(0, self::$person->getId());
    }

    public function testGetSetPersonName(): void
    {
        $personname = self::$faker->name();
        self::$person->setName($personname);
        static::assertSame(
            $personname,
            self::$person->getName()
        );
    }

    public function testGetSetBirthDate(): void
    {
        $birthDate = self::$faker->dateTime();
        self::$person->setBirthDate($birthDate);
        static::assertSame(
            $birthDate,
            self::$person->getBirthDate()
        );
    }

    public function testGetSetDeathDate(): void
    {
        $deathDate = self::$faker->dateTime();
        self::$person->setDeathDate($deathDate);
        static::assertSame(
            $deathDate,
            self::$person->getDeathDate()
        );
    }

    public function testGetSetImageUrl(): void
    {
        $imageUrl = self::$faker->url();
        self::$person->setImageUrl($imageUrl);
        static::assertSame(
            $imageUrl,
            self::$person->getImageUrl()
        );
    }

    public function testGetSetWikiUrl(): void
    {
        $wikiUrl = self::$faker->url();
        self::$person->setWikiUrl($wikiUrl);
        static::assertSame(
            $wikiUrl,
            self::$person->getWikiUrl()
        );
    }

    public function testGetAddContainsRemoveEntities(): void
    {
        self::assertEmpty(self::$person->getEntities());
        /** @var Entity $entity */
        $entity = Factory\EntityFactory::createElement(self::$faker->slug());

        self::$person->addEntity($entity);
        self::assertNotEmpty(self::$person->getEntities());
        self::assertTrue(self::$person->containsEntity($entity));

        self::$person->removeEntity($entity);
        self::assertFalse(self::$person->containsEntity($entity));
        self::assertEmpty(self::$person->getEntities());
        self::assertFalse(self::$person->removeEntity($entity));
    }

    public function testGetAddContainsRemoveProducts(): void
    {
        self::assertEmpty(self::$person->getProducts());
        /** @var Product $product */
        $product = Factory\ProductFactory::createElement(self::$faker->slug());

        self::$person->addProduct($product);
        self::assertNotEmpty(self::$person->getProducts());
        self::assertTrue(self::$person->containsProduct($product));

        self::$person->removeProduct($product);
        self::assertFalse(self::$person->containsProduct($product));
        self::assertEmpty(self::$person->getProducts());
        self::assertFalse(self::$person->removeProduct($product));
    }

    public function testToString(): void
    {
        $personName = self::$faker->name();
        $birthDate = self::$faker->dateTime();
        $deathDate = self::$faker->dateTime();
        self::$person->setBirthDate($birthDate);
        self::$person->setDeathDate($deathDate);
        self::$person->setName($personName);
        self::assertStringContainsString(
            $personName,
            self::$person->__toString()
        );
        self::assertStringContainsString(
            $birthDate->format('Y-m-d'),
            self::$person->__toString()
        );
        self::assertStringContainsString(
            $deathDate->format('Y-m-d'),
            self::$person->__toString()
        );
    }

    public function testJsonSerialize(): void
    {
        $jsonStr = (string) json_encode(self::$person, JSON_PARTIAL_OUTPUT_ON_ERROR);
        self::assertJson($jsonStr);
    }
}
