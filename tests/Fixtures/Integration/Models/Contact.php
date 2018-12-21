<?php


namespace TheCodingMachine\GraphQL\Controllers\Fixtures\Integration\Models;


class Contact
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Contact|null
     */
    private $manager;
    /**
     * @var Contact[]
     */
    private $relations = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getManager(): ?Contact
    {
        return $this->manager;
    }

    /**
     * @param Contact|null $manager
     */
    public function setManager(?Contact $manager): void
    {
        $this->manager = $manager;
    }

    /**
     * @return Contact[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @param Contact[] $relations
     */
    public function setRelations(array $relations): void
    {
        $this->relations = $relations;
    }
}
