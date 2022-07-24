<?php

namespace Dev\RestApi\Test\Integration;

class FixtureLoader
{
    public static function apiFixtures(): void
    {
        echo "Applying Fixtures For the API" . PHP_EOL;
        require_once __DIR__ . '/_files/products_simple.php';
    }

    public static function apiFixturesRollback()
    {
        echo "Rolling Back Fixtures For the API" . PHP_EOL;
        require_once __DIR__ . '/_files/products_simple_rollback.php';
    }
}
