<?php
/**
 * @package Dev_RestApi
 * @author rubenromao@gmail.com
 */
declare(strict_types=1);

namespace Dev\RestApi\Api;

/**
 * Interface RequestItemInterface
 * @api
 * @since 1.0.0
 */
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
