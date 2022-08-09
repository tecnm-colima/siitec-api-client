<?php

namespace ITColima\SiitecApi\Model\Escolares;

class AspiranteInscrito
{
    public $id_aspirante;
    public $id_usuario;
    public $id_alumno;
    public $num_control;

    public function __construct($id_aspirante, $id_usuario, $id_alumno, $num_control)
    {
        $this->id_aspirante = $id_aspirante;
        $this->id_usuario = $id_usuario;
        $this->id_alumno = $id_alumno;
        $this->num_control = $num_control;
    }
}
