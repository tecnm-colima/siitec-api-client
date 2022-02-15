<?php

namespace ITColima\SiitecApi\Model\App\Usuarios;

class Aspirante
{
    // usuarios
    public $id_usuario;
    public $usuario;
    public $password;
    public $password_method;

    // aspirantes
    public $id_aspirante;
    public $nombres;
    public $apellido1;
    public $apellido2;

    // usuarios_correos
    public $correo;
    public $correo_verify;
}
