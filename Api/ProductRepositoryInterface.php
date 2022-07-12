<?php
declare(strict_types=1);

namespace Dev\RestApi\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface ProductRepositoryInterface
{
    /**
     * Return a filtered product.
     *
     * @param int $id
     * @return ResponseItemInterface
     * @throws NoSuchEntityException
     */
    public function getItem(int $id): ResponseItemInterface;

    /**
     * Set description for the product.
     *
     * @param RequestItemInterface[] $products
     * @return void
     */
    public function setDescription(array $products): void;
}
