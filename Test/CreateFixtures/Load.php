<?php

namespace Dev\RestApi\Test\CreateProducts;

class Load extends \PHPUnit\Framework\TestCase
{

    public static function loadProductData()
    {
        require __DIR__ . '/_files/products_simple.php';
    }

    /**
     * @magentoDbIsolation enable
     * @magentoDataFixture loadProductData
     */
    public function testLoadProducts()
    {
        $this->assertTrue(true);
    }
}
