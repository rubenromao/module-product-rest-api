<?php
/**
 * @module Dev_RestApi
 * @author rubenromao@gmail.com
 */
declare(strict_types=1);

namespace Dev\RestApi\Api;

/**
 * Interface ResponseItemInterface
 * @api
 * @since 1.0.0
 */
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
    public function setId(int $id): self;

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku): self;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self;
}
