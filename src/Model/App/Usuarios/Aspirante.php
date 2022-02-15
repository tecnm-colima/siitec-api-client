<?php

namespace ITColima\SiitecApi\Model\App\Usuarios;

use Francerz\Http\Utils\HttpHelper;
use JsonSerializable;
use Psr\Http\Message\MessageInterface;

class Aspirante implements JsonSerializable
{
    // usuarios
    public $id_usuario;
    public $usuario;
    public $password;

    // aspirantes
    public $id_aspirante;
    public $nombres;
    public $apellido1;
    public $apellido2;

    // usuarios_correos
    public $correo;
    public $correo_verify;

    public static function fromHttpMessage(MessageInterface $message)
    {
        $data = HttpHelper::getContent($message);
        $aspirante = new self();
        $aspirante->id_usuario = $data->id_usuario ?? null;
        $aspirante->usuario = $data->usuario ?? null;
        $aspirante->password = $data->password ?? null;
        $aspirante->id_aspirante = $data->id_aspirante ?? null;
        $aspirante->nombres = $data->nombres ?? null;
        $aspirante->apellido1 = $data->apellido1 ?? null;
        $aspirante->apellido2 = $data->apellido2 ?? null;
        $aspirante->correo = $data->correo ?? null;
        $aspirante->correo_verify = $data->correo_verify ?? null;
        return $aspirante;
    }

    public function jsonSerialize()
    {
        return [
            'id_usuario' => $this->id_usuario,
            'usuario' => $this->usuario,
            'password' => $this->password,
            'id_aspirante' => $this->id_aspirante,
            'nombres' => $this->nombres,
            'apellido1' => $this->apellido1,
            'apellido2' => $this->apellido2,
            'correo' => $this->correo,
            'correo_verify' => $this->correo_verify
        ];
    }
}
