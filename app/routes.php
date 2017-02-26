<?php
use App\Controllers\FilesController;
use App\Services\FileService;
use Slim\Http\Request;
use Slim\Http\Response;

/** Get list of files */
$app->get('/files', FilesController::class . ':listFiles');

/** Get file's metadata */
$app->get('/files/{file_name}', FilesController::class . ':getMetadata');

/** Get file's content */
$app->get('/files/{file_name}/content', FilesController::class . ':getContent');

/** Upload file */
$app->put('/files/{file_name}', FilesController::class . ':uploadFile');

