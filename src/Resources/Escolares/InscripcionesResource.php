<?php

namespace ITColima\SiitecApi\Resources\Escolares;

use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Escolares\Inscripcion;

class InscripcionesResource extends AbstractResource
{
    /**
     * Registra la inscripción de un estudiante a un periodo
     *
     * @param Inscripcion $reins Objeto que contiene los datos para inscripción.
     * @return void
     */
    public function put(Inscripcion $reins)
    {
        $this->requiresClientAccessToken();
        $response = $this->_put("/escolares/inscripciones/{$reins->id_estudiante}/{$reins->id_periodo}", null);
        return $response;
    }
}
