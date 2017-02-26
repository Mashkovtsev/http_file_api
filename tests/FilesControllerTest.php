<?php

use App\Controllers\FilesController;
use App\Services\FileService;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Slim\Container;

class FilesControllerTest extends TestCase
{
    /** @var FilesController $controller */
    protected static $controller;

    /** @var FileService $fs */
    protected static $fs;

    public static function setUpBeforeClass()
    {
        /** @var Container $container */
        global $container;
        self::$controller = new FilesController($container);
        self::$fs = $container->get('file_service');
    }

    public function testListFiles()
    {
        // We need a request and response object to invoke the action
        $environment = \Slim\Http\Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/files',
            ]
        );
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();

        $response = self::$controller->listFiles($request, $response);
        $this->assertSame((string)$response->getBody(), '["foo.bar","google.png","hello.txt","test.txt"]');
    }

    public function testGetMetadata()
    {
        $environment = \Slim\Http\Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/files/google.png',
            ]
        );
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $request = $request->withAttribute('file_name', 'google.png');
        $response = new \Slim\Http\Response();

        $response = self::$controller->getMetadata($request, $response);
        $this->assertContains('"size":5087', (string)$response->getBody());
    }


    public function testGetContent()
    {
        $environment = \Slim\Http\Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/files/hello.txt/content',
            ]
        );
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $request = $request->withAttribute('file_name', 'hello.txt');
        $response = new \Slim\Http\Response();

        $response = self::$controller->getContent($request, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Hello World!', (string)$response->getBody());
    }

    public function testUploadFile()
    {
        $file_name = 'new_file.txt';
        $copying_file_path = self::$fs->getPath('new_file.txt');

        $environment = \Slim\Http\Environment::mock([
                'REQUEST_METHOD' => 'PUT',
                'REQUEST_URI' => "/files/$file_name",
            ]
        );
        $request = \Slim\Http\Request::createFromEnvironment($environment);

        $request = $request->withAttribute('file_name', $file_name);
        $file_stream = new LazyOpenStream($copying_file_path, 'r');
        $request = $request->withBody($file_stream);

        $response = new \Slim\Http\Response();

        $response = self::$controller->uploadFile($request, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($file_name, (string)$response->getBody());

        unlink($copying_file_path);
    }

    public function testRewriteFileOnUpload()
    {
        $file_name = 'hello.txt';
        $file_path = self::$fs->getPath($file_name);
        $old_content = file_get_contents($file_path);
        $new_content = 'New content';
        $temp_file_path = self::$fs->getPath('temp_file');
        $temp_file = fopen($temp_file_path, 'wb');
        fwrite($temp_file, $new_content);

        $environment = \Slim\Http\Environment::mock([
                'REQUEST_METHOD' => 'PUT',
                'REQUEST_URI' => "/files/$file_name",
            ]
        );
        $request = \Slim\Http\Request::createFromEnvironment($environment);

        $request = $request->withAttribute('file_name', $file_name);
        $file_stream = new LazyOpenStream($temp_file_path, 'r');
        $request = $request->withBody($file_stream);

        $response = new \Slim\Http\Response();

        $response = self::$controller->uploadFile($request, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($file_name, (string)$response->getBody());
        $this->assertStringEqualsFile($file_path, $new_content);

        file_put_contents($file_path, $old_content);
        unlink($temp_file_path);
    }
}