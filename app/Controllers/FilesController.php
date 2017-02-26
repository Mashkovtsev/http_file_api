<?php

namespace App\Controllers;

use App\Services\FileService;
use GuzzleHttp\Psr7\LazyOpenStream;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class FilesController
{
    /** @var FileService $fs */
    protected $fs;

    /**
     * FilesController constructor.
     * @param Container $container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $container)
    {
        $this->fs = $container->get('file_service');
    }

    public function listFiles(Request $req, Response $res)
    {
        return $res->withJson($this->fs->getFiles());
    }

    public function getMetadata(Request $req, Response $res)
    {
        $file_name = $req->getAttribute('file_name');
        if (!$this->fs->isFileExists($file_name)) {
            return $res
                ->withStatus(404)
                ->withJson(['message' => 'File not found']);
        }

        return $res->withJson($this->fs->getMetadata($file_name));

    }

    /**
     * @param Request $req
     * @param Response $res
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function getContent(Request $req, Response $res)
    {
        $file_name = $req->getAttribute('file_name');
        $file_path = $this->fs->getPath($file_name);

        if (!file_exists($file_path)) {
            return $res
                ->withStatus(404)
                ->withJson(['message' => 'File not found']);
        }

        $file_stream = new LazyOpenStream($file_path, 'r');
        return $res
            ->withHeader('Content-Type', mime_content_type($file_path))
            ->withHeader('Content-Disposition', 'attachment; filename="' . $file_name . '"')
            ->withBody($file_stream);
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return Response
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function uploadFile(Request $req, Response $res)
    {
        $file_name = $req->getAttribute('file_name');
        $rewrite = $req->getQueryParam('rewrite') ?: 'true';
        $upload_stream = $req->getBody();

        if ($rewrite === 'false' && $this->fs->isFileExists($file_name)) {
            $info = pathinfo($this->fs->getPath($file_name));
            $file_name = $info['filename'] . ' (copy).' . $info['extension'];
        }

        $file = fopen($this->fs->getPath($file_name), 'wb');
        while (!$upload_stream->eof()) {
            $fwrite = fwrite($file, $upload_stream->read(1024));
            if ($fwrite === false) {
                return $res
                    ->withStatus(505)
                    ->withJson(['message' => 'Error on file write']);
            }
        }
        return $res->withJson($this->fs->getMetadata($file_name));
    }
}