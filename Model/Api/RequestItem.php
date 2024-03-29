<?php
declare(strict_types=1);

namespace Dev\RestApi\Model\Api;

use Dev\RestApi\Api\RequestItemInterface;
use Magento\Framework\DataObject;

/**
 * Class RequestItem
 */
class RequestItem extends DataObject implements RequestItemInterface
{
    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->getData(self::DATA_ID);
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->getData(self::DATA_DESCRIPTION);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id) : RequestItemInterface
    {
        return $this->setData(self::DATA_ID, $id);
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description) : RequestItemInterface
    {
        return $this->setData(self::DATA_DESCRIPTION, $description);
    }
}
