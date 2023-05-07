<?php

/**
 * src/Entity/Element.php
 *
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    https://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace TDW\ACiencia\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Element
 */
class Element implements ElementInterface
{
    #[ORM\Column(
        name: "id",
        type: "integer",
        nullable: false
    )]
    #[ORM\Id(), ORM\GeneratedValue(strategy: "IDENTITY")]
    protected int $id;

    #[ORM\Column(
        name: "name",
        type: "string",
        length: 80,
        unique: true,
        nullable: false
    )]
    protected string $name;

    #[ORM\Column(
        name: "birthdate",
        type: "datetime",
        nullable: true
    )]
    protected DateTime | null $birthDate = null;

    #[ORM\Column(
        name: "deathdate",
        type: "datetime",
        nullable: true
    )]
    protected DateTime | null $deathDate = null;

    #[ORM\Column(
        name: "image_url",
        type: "string",
        length: 2047,
        nullable: true
    )]
    protected string | null $imageUrl = null;

    #[ORM\Column(
        name: "wiki_url",
        type: "string",
        length: 2047,
        nullable: true
    )]
    protected string | null $wikiUrl = null;

    protected function __construct(
        string $name,
        ?DateTime $birthDate = null,
        ?DateTime $deathDate = null,
        ?string $imageUrl = null,
        ?string $wikiUrl = null
    ) {
        $this->id = 0;
        $this->name = $name;
        $this->birthDate = $birthDate;
        $this->deathDate = $deathDate;
        $this->imageUrl = $imageUrl;
        $this->wikiUrl = $wikiUrl;
    }

    final public function getId(): int
    {
        return $this->id;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function setName(string $name): void
    {
        $this->name = $name;
    }

    final public function getBirthDate(): ?DateTime
    {
        return $this->birthDate;
    }

    final public function setBirthDate(?DateTime $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    final public function getDeathDate(): ?DateTime
    {
        return $this->deathDate;
    }

    final public function setDeathDate(?DateTime $deathDate): void
    {
        $this->deathDate = $deathDate;
    }

    final public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    final public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    final public function getWikiUrl(): ?string
    {
        return $this->wikiUrl;
    }

    final public function setWikiUrl(?string $wikiUrl): void
    {
        $this->wikiUrl = $wikiUrl;
    }

    /**
     * @param Element[] $collection
     *
     * @return int[] sorted Ids in collection
     */
    final protected function getCodes(array $collection): array
    {
        $arrayIds = array_map(
            fn(Element $element) => $element->getId(),
            $collection
        );
        sort($arrayIds);
        return $arrayIds;
    }

    /**
     * @param Element[] $collection
     *
     * @return string String representation of Collection Ids
     */
    final protected function getCodesTxt(array $collection): string
    {
        $codes = $this->getCodes($collection);
        return sprintf('[%s]', implode(', ', $codes));
    }

    public function __toString(): string
    {
        $birthdate = $this->getBirthDate()?->format('Y-m-d') ?? 'null';
        $deathdate = $this->getDeathDate()?->format('Y-m-d') ?? 'null';
        return sprintf(
            '[%s: (id=%04d, name="%s", birthDate="%s", deathDate="%s", imageUrl="%s", wikiUrl="%s"',
            basename(get_class($this)),
            $this->getId(),
            $this->getName(),
            $this->getBirthDate()?->format('Y-m-d'),
            $this->getDeathDate()?->format('Y-m-d'),
            $this->getImageUrl(),
            $this->getWikiUrl(),
        );
    }

    #[ArrayShape([
        'id' => "int",
        'name' => "string",
        'birthDate' => "null|string",
        'deathDate' => "null|string",
        'imageUrl' => "null|string",
        'wikiUrl' => "null|string"
    ])]
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'birthDate' => $this->getBirthDate()?->format('Y-m-d'),
            'deathDate' => $this->getDeathDate()?->format('Y-m-d'),
            'imageUrl'  => $this->getImageUrl(),
            'wikiUrl'  => $this->getWikiUrl(),
        ];
    }
}
