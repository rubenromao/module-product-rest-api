<?php
declare(strict_types=1);

namespace Dev\RestApi\Api;

interface RequestItemInterface
{
    const DATA_ID = 'id';
    const DATA_DESCRIPTION = 'description';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getDescription(): mixed;

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self;

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self;
}
