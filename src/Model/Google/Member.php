<?php

namespace ITColima\SiitecApi\Model\Google;

use JsonSerializable;

Class Member implements JsonSerializable
{
    public $email;
    public $role;
    public $type;
    public $deliverySttings;
    public $kind;

    /**
     * function constructor
     */
    public function __construct(
        $email,
        $role,
        $type,
        $deliverySttings,
        $kind
    )
    {
        $this->email = $email;
        $this->role = $role;
        $this->type = $type;
        $this->deliverySttings = $deliverySttings;
        $this->kind = $kind;

    }

    /**
     * function jsonSerialize
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return[
            'email' => $this->email,
            'role' => $this->role,
            'type' => $this->type,
            'deliverySettings' => $this->deliverySttings,
            'kind' => $this->kind
        ];
    }
}