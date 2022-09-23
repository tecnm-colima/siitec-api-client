<?php

namespace ITColima\SiitecApi\Resources\Pagos\Procesos;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;

class InscripcionResource extends AbstractResource
{
    /**
     * Obtiene los procesos de pago de Inscripción
     *
     * @param array $params
     *  - carrera: ID de la carrera a la que aplica el proceso de inscripción.
     *  - periodo: ID del periodo al a que aplica el proceso de inscripción.
     *  - vigente: Fecha del periodo de inscripción o @now para el tiempo actual.
     * @return array
     */
    public function getAll(array $params = [])
    {
        $response = $this->protectedGet('/pagos/procesos/inscripcion', $params);
        return HttpHelper::getContent($response);
    }

    public function getById($id_proceso, array $params = [])
    {
        if (is_array($id_proceso)) {
            $id_proceso = join('+', $id_proceso);
        }
        $response = $this->protectedGet("/pagos/procesos/inscripcion/{$id_proceso}", $params);
        return HttpHelper::getContent($response);
    }

    public function getCurrent(array $params = [])
    {
        $response = $this->protectedGet('/pagos/procesos/inscripcion/@current', $params);
        return HttpHelper::getContent($response);
    }
}
