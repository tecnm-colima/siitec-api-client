<?php

namespace ITColima\SiitecApi\Model\Escolares;

use JsonSerializable;
use RuntimeException;

class EstudianteDocumento implements JsonSerializable
{
    public $alumno_id;
    public $documentacion_id;
    public $description;
    public $filename;
    public $content;

    public static function encodeFile($filepath)
    {
        return base64_encode(file_get_contents($filepath));
    }

    public static function fromFile($filepath)
    {
        if (!file_exists($filepath)) {
            return null;
        }
        $obj = new static();
        $obj->filename = basename($filepath);
        $obj->content = static::encodeFile($filepath);
        return $obj;
    }

    public function getExtension()
    {
        if (empty($this->filename)) {
            return null;
        }
        $pos = strrpos($this->filename, '.');
        if ($pos === false) {
            return null;
        }
        return substr($this->filename, $pos + 1);
    }

    public function writeFile($filepath)
    {
        $dirpath = dirname($filepath);
        if (!file_exists($dirpath) || !is_dir($dirpath)) {
            mkdir($dirpath, 0664, true);
        }

        $f = fopen($filepath, 'w+');
        if ($f === false) {
            throw new RuntimeException("Failed opening file '{$filepath}'.");
        }
        $l = fwrite($f, base64_decode($this->content));
        if ($l === false) {
            throw new RuntimeException("Failed to write file '{$filepath}'.");
        }
    }

    public function jsonSerialize()
    {
        return [
            'alumno_id' => $this->alumno_id,
            'documentacion_id' => $this->documentacion_id,
            'description' => $this->description,
            'filename' => $this->filename,
            'content' => $this->content
        ];
    }
}
