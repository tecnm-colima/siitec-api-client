<?php

namespace ITColima\SiitecApi\Resources\Escolares;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;

class GruposResource extends AbstractResource
{
    /**
     * Obtiene una lista de los grupos con los parámetros proporcionados
     *
     * @param array $params
     *  - estudiante_usuario: ID de usuario del estudiante del que se obtienen los grupos.
     *  - docente_usuario: ID de usuario del docente del que se obtienen los grupos.
     *  - periodo: ID del periodo escolar o @current para el periodo actual.
     *  - nest_horarios: Indica que se obtendran los horarios del grupo.
     *  - nest_estudiantes: Indica que se obtienen los estudiantes del grupo.
     *  - link_carrera: Indica que se obtiene la carrera del grupo.
     *  - link_plan_estudio: Indica que se obtiene el plan de estudio del grupo.
     *  - link_asignatura: Indica que se obtiene la asignatura del grupo.
     * @return array
     */
    public function getAll(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $this->requiresOwnerAccessToken(false);
        $response = $this->protectedGet('/escolares/grupos', $params);
        return HttpHelper::getContent($response);
    }

    public function getById($grupo_id, array $params = [])
    {
        if (is_array($grupo_id)) {
            $grupo_id = join('+', $grupo_id);
        }
        $this->requiresClientAccessToken(true);
        $this->requiresOwnerAccessToken(false);
        $response = $this->protectedGet("/escolares/grupos/{$grupo_id}", $params);
        return HttpHelper::getContent($response);
    }

    public function getAsDocente(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $this->requiresOwnerAccessToken(true);
        $response = $this->protectedGet('/escolares/grupos/@docente', $params);
        return HttpHelper::getContent($response);
    }

    public function getAsEstudiante(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $this->requiresOwnerAccessToken(true);
        $response = $this->protectedGet('/escolares/grupos/@estudiante', $params);
        return HttpHelper::getContent($response);
    }
}
