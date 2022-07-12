<?php
declare(strict_types=1);

namespace Dev\RestApi\Api;

interface ResponseItemInterface
{
    const DATA_ID = 'id';
    const DATA_SKU = 'sku';
    const DATA_NAME = 'name';
    const DATA_DESCRIPTION = 'description';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getSku(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): ResponseItemInterface;

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku): ResponseItemInterface;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): ResponseItemInterface;

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): ResponseItemInterface;
}
