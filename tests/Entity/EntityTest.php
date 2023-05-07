<?php

/**
 * tests/Entity/EntityTest.php
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
 * Class EntityTest
 *
 * @group   entities
 * @coversDefaultClass \TDW\ACiencia\Entity\Entity
 */
class EntityTest extends TestCase
{
    protected static Entity $entity;

    private static \Faker\Generator $faker;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    public static function setUpBeforeClass(): void
    {
        self::$faker = \Faker\Factory::create('es_ES');
        self::$entity = Factory\EntityFactory::createElement('');
    }

    /**
     * @return void
     * @covers \TDW\ACiencia\Factory\EntityFactory::createElement
     */
    public function testConstructor(): void
    {
        $name = self::$faker->name();
        self::$entity = Factory\EntityFactory::createElement($name);
        self::assertSame(0, self::$entity->getId());
        self::assertSame(
            $name,
            self::$entity->getName()
        );
        self::assertEmpty(self::$entity->getProducts());
        self::assertEmpty(self::$entity->getPersons());
    }

    public function testGetId(): void
    {
        self::assertSame(0, self::$entity->getId());
    }

    public function testGetSetEntityName(): void
    {
        $entityname = self::$faker->name();
        self::$entity->setName($entityname);
        static::assertSame(
            $entityname,
            self::$entity->getName()
        );
    }

    public function testGetSetBirthDate(): void
    {
        $birthDate = self::$faker->dateTime();
        self::$entity->setBirthDate($birthDate);
        static::assertSame(
            $birthDate,
            self::$entity->getBirthDate()
        );
    }

    public function testGetSetDeathDate(): void
    {
        $deathDate = self::$faker->dateTime();
        self::$entity->setDeathDate($deathDate);
        static::assertSame(
            $deathDate,
            self::$entity->getDeathDate()
        );
    }

    public function testGetSetImageUrl(): void
    {
        $imageUrl = self::$faker->url();
        self::$entity->setImageUrl($imageUrl);
        static::assertSame(
            $imageUrl,
            self::$entity->getImageUrl()
        );
    }

    public function testGetSetWikiUrl(): void
    {
        $wikiUrl = self::$faker->url();
        self::$entity->setWikiUrl($wikiUrl);
        static::assertSame(
            $wikiUrl,
            self::$entity->getWikiUrl()
        );
    }

    public function testGetAddContainsRemovePersons(): void
    {
        self::assertEmpty(self::$entity->getPersons());
        /** @var Person $person */
        $person = Factory\PersonFactory::createElement(self::$faker->slug());

        self::$entity->addPerson($person);
        self::$entity->addPerson($person);  // CC
        self::assertNotEmpty(self::$entity->getPersons());
        self::assertTrue(self::$entity->containsPerson($person));

        self::$entity->removePerson($person);
        self::assertFalse(self::$entity->containsPerson($person));
        self::assertEmpty(self::$entity->getPersons());
        self::assertFalse(self::$entity->removePerson($person));
    }

    public function testGetAddContainsRemoveProducts(): void
    {
        self::assertEmpty(self::$entity->getProducts());

        /** @var Product $product */
        $product = Factory\ProductFactory::createElement(self::$faker->slug());

        self::$entity->addProduct($product);
        self::assertNotEmpty(self::$entity->getProducts());
        self::assertTrue(self::$entity->containsProduct($product));

        self::$entity->removeProduct($product);
        self::assertFalse(self::$entity->containsProduct($product));
        self::assertEmpty(self::$entity->getProducts());
        self::assertFalse(self::$entity->removeProduct($product));
    }

    public function testToString(): void
    {
        $entityName = self::$faker->company();
        $birthDate = self::$faker->dateTime();
        $deathDate = self::$faker->dateTime();
        self::$entity->setBirthDate($birthDate);
        self::$entity->setDeathDate($deathDate);
        self::$entity->setName($entityName);
        self::assertStringContainsString(
            $entityName,
            self::$entity->__toString()
        );
        self::assertStringContainsString(
            $birthDate->format('Y-m-d'),
            self::$entity->__toString()
        );
        self::assertStringContainsString(
            $deathDate->format('Y-m-d'),
            self::$entity->__toString()
        );
    }

    public function testJsonSerialize(): void
    {
        $jsonStr = (string) json_encode(self::$entity, JSON_PARTIAL_OUTPUT_ON_ERROR);
        self::assertJson($jsonStr);
    }
}
