<?php

namespace ITColima\SiitecApi\Model\Google;

use JsonSerializable;

class UserPhoto implements JsonSerializable
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function fromFile($filepath)
    {
        return new static(file_get_contents($filepath));
    }

    /**
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'base64' => base64_encode($this->data)
        ];
    }
}