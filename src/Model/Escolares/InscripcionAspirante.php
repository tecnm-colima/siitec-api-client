<?php

namespace ITColima\SiitecApi\Model\Escolares;

class InscripcionAspirante
{
    public $id_aspirante;
    public $id_periodo;

    public function __construct($id_aspirante = null, $id_periodo = null)
    {
        $this->id_aspirante = $id_aspirante;
        $this->id_periodo = $id_periodo;
    }
}