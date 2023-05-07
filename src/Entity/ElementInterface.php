<?php

/**
 * src/Entity/ElementInterface.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Entity;

use DateTime;
use JsonSerializable;
use Stringable;

interface ElementInterface extends JsonSerializable, Stringable
{
    /**
     * @return int Element id
     */
    public function getId(): int;

    /**
     * @return string Element name
     */
    public function getName(): string;

    /**
     * @param string $name new Element name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @return DateTime|null Element birthdate
     */
    public function getBirthDate(): ?DateTime;

    /**
     * @param DateTime|null $birthDate Element birthdate
     * @return void
     */
    public function setBirthDate(?DateTime $birthDate): void;

    /**
     * @return DateTime|null Element deathdate
     */
    public function getDeathDate(): ?DateTime;

    /**
     * @param DateTime|null $deathDate Element deathdate
     * @return void
     */
    public function setDeathDate(?DateTime $deathDate): void;

    /**
     * @return string|null Element Image Url
     */
    public function getImageUrl(): ?string;

    /**
     * @param string|null $imageUrl Element Image Url
     * @return void
     */
    public function setImageUrl(?string $imageUrl): void;

    /**
     * @return string|null Element Wiki Url
     */
    public function getWikiUrl(): ?string;

    /**
     * @param string|null $wikiUrl Element Wiki Url
     * @return void
     */
    public function setWikiUrl(?string $wikiUrl): void;

    /**
     * @see Stringable
     */
    public function __toString(): string;

    /**
     * @see JsonSerializable
     */
    public function jsonSerialize(): mixed;
}
