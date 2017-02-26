<?php

use App\Services\FileService;
use PHPUnit\Framework\TestCase;
use Slim\Container;

class FileServiceTest extends TestCase
{
    /** @var FileService $container */
    protected static $fs;

    public static function setUpBeforeClass()
    {
        /** @var Container $container */
        global $container;
        self::$fs = $container->get('file_service');
    }

    public function testGetMetadataFields()
    {
        $result = self::$fs->getMetadata('google.png');
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('mime_type', $result);
        $this->assertArrayHasKey('date_created', $result);
        $this->assertArrayHasKey('date_updated', $result);
    }
}