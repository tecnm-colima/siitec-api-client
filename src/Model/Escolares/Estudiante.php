<?php

namespace ITColima\SiitecApi\Model\Escolares;

use ITColima\SiitecApi\Model\UsuarioCorreo;
use ITColima\SiitecApi\Model\UsuarioTelefono;
use ITColima\SiitecApi\Model\AlumnoContacto;

class Estudiante
{
    public const ESTATUS_VIGENTE = 1;
    public const ESTATUS_BAJA_TEMPORAL = 2;
    public const ESTATUS_BAJA_POR_ESPECIAL = 3;
    public const ESTATUS_BAJA_DEFINITIVA = 4;
    public const ESTATUS_EGRESADO = 5;
    public const ESTATUS_BAJA_POR_PAGO = 6;

    public const TIPO_SANGRE_A_POSITIVO     = 'A+';
    public const TIPO_SANGRE_A_NEGATIVO     = 'A-';
    public const TIPO_SANGRE_B_POSITIVO     = 'B+';
    public const TIPO_SANGRE_B_NEGATIVO     = 'B-';
    public const TIPO_SANGRE_AB_POSITIVO    = 'AB+';
    public const TIPO_SANGRE_AB_NEGATIVO    = 'AB-';
    public const TIPO_SANGRE_O_POSITIVO     = 'O+';
    public const TIPO_SANGRE_O_NEGATIVO     = 'O-';

    public $aspirante_id;
    public $ingreso_periodo_id;
    public $carrera_id;
    public $planestudio_id;
    public $modalidad_id;
    public $ano_ingreso;
    public $semestre;

    public $estatus;
    public $num_control;
    public $nombres;
    public $apellido1;
    public $apellido2;
    public $curp;
    public $sexo;

    public $calle;
    public $numero_exterior;
    public $numero_interior;
    public $colonia;
    public $codigo_postal;
    public $localidad_id;
    public $municipio_id;
    public $estado_id;
    public $pais_id;

    public $rfc;
    public $estado_civil_id;
    public $fecha_nacimiento;
    public $nacimiento_localidad_id;
    public $nacimiento_municipio_id;
    public $nacimiento_estado_id;
    public $nacimiento_pais_id;

    public $seguro_medico;
    public $institucion_medica;
    public $tipo_sangre;

    /** @var string */
    public $escuela_procedencia;
    /** @var string */
    public $area_procedencia;
    public $fecha_egreso;
    public $promedio;

    /** @var UsuarioCorreo[] */
    public $emails = [];
    /** @var UsuarioTelefono[] */
    public $telefonos = [];
    /** @var AlumnoContacto[] */
    public $contactos = [];
    /** @var AlumnoDocumento */
    public $documentacion = [];
}
