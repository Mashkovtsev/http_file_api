<?php

namespace App\Services;

class FileService
{
    private $base_dir;

    /**
     * FileService constructor.
     * @param string $base_dir
     */
    public function __construct($base_dir)
    {
        $this->base_dir = $base_dir;
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return $this->base_dir;
    }

    /**
     * @param $file_name
     * @return string
     */
    public function getPath($file_name)
    {
        return $this->base_dir . '/' . $file_name;
    }

    /**
     * @param string $file_name
     * @return array
     */
    public function getMetadata($file_name)
    {
        $file_path = $this->getPath($file_name);
        return [
            'name' => $file_name,
            'size' => filesize($file_path),
            'mime_type' => mime_content_type($file_path),
            'date_created' => date('Y-m-d H:i:s', filectime($file_path)),
            'date_updated' => date('Y-m-d H:i:s', filemtime($file_path))
        ];
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        //array_values() needed to force array json_encode()
        return array_values(array_diff(scandir($this->getBaseDir()), ['.', '..']));
    }

    /**
     * @param $file_name
     * @return bool
     */
    public function isFileExists($file_name)
    {
        return file_exists($this->getPath($file_name));
    }

}