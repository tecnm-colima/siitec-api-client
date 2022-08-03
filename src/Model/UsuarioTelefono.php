<?php

namespace ITColima\SiitecApi\Model;

class UsuarioTelefono
{
    public const TIPO_FIJO = 'Fijo';
    public const TIPO_MOVIL = 'Móvil';
    public const TIPO_FAX = 'Fax';

    public $telefono_id;
    public $usuario_id;
    public $tipo;
    public $descripcion;
    public $telefono;
}
