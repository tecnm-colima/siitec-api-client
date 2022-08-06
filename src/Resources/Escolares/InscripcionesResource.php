<?php

namespace ITColima\SiitecApi\Resources\Escolares;

use Francerz\Http\Utils\Constants\MediaTypes;
use Francerz\Http\Utils\HttpHelper;
use InvalidArgumentException;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Escolares\Estudiante;
use ITColima\SiitecApi\Model\Escolares\Inscripcion;
use ITColima\SiitecApi\Model\Escolares\InscripcionAspirante;

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
        return HttpHelper::getContent($response);
    }

    public function putAspirante(InscripcionAspirante $inscr)
    {
        $this->requiresClientAccessToken();
        $response = $this->_put("/escolares/inscripciones/aspirantes/{$inscr->id_aspirante}/{$inscr->id_periodo}", null);
        return HttpHelper::getContent($response);
    }

    /**
     * @param int|string $periodo_id
     * @param Estudiante[] $estudiantes
     */
    public function inscribirBatch($periodo_id, array $estudiantes)
    {
        foreach ($estudiantes as $e) {
            if (!$e instanceof Estudiante) {
                throw new InvalidArgumentException("Each item in \$estudiantes MUST be an Estudiante type object");
            }
            if (!isset($e->aspirante_id) || !isset($e->curp)) {
                throw new InvalidArgumentException("Aspirantes MUST have aspirante_id and CURP.");
            }
        }

        $this->requiresClientAccessToken();
        $response = $this->_post(
            "/escolares/periodos/{$periodo_id}/inscripciones/batch",
            $estudiantes,
            MediaTypes::APPLICATION_JSON
        );
        return HttpHelper::getContent($response);
    }
}
