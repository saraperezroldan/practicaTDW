<?php

/**
 * src/Entity/Role.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Entity;

/**
 * @Enum({ "reader", "writer" })
 */
enum Role: string {
    // scope names (roles)
    case READER = 'reader';
    case WRITER = 'writer';

    public const ALL_VALUES = [ 'reader', 'writer' ];
}
