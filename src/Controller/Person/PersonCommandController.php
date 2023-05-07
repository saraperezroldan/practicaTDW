<?php

/**
 * src/Controller/Person/PersonCommandController.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Controller\Person;

use TDW\ACiencia\Controller\Element\ElementBaseCommandController;
use TDW\ACiencia\Entity\Person;

/**
 * Class PersonCommandController
 */
class PersonCommandController extends ElementBaseCommandController
{
    /** @var string ruta api gestión personas  */
    public const PATH_PERSONS = '/persons';

    public static function getEntityClassName(): string
    {
        return Person::class;
    }

    public static function getEntityIdName(): string
    {
        return 'personId';
    }
}
