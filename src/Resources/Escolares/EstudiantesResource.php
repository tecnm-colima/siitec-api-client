<?php

namespace ITColima\SiitecApi\Resources\Escolares;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;

class EstudiantesResource extends AbstractResource
{
    /**
     * Obtiene los estudiantes según los parametros proporcionados
     *
     * @param array $params
     *  - num_control: Número de control de estudiante.
     *  - usuario_id: ID de usuario de estudiante.
     * @return array
     */
    public function getAll(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_get('/escolares/estudiantes', $params);
        return HttpHelper::getContent($response);
    }

    public function getByNumControl(string $num_control, array $params = [])
    {
        $this->requiresClientAccessToken();
        $params['num_control'] = $num_control;
        $response = $this->_get('/escolares/estudiantes', $params);
        $output = HttpHelper::getContent($response);
        if (empty($output)) {
            return null;
        }
        return reset($output);
    }

    public function getById($id, array $params=[])
    {
        $this->requiresClientAccessToken();
        $id = is_array($id) ? implode('+', $id) : $id;
        $response = $this->_get("/escolares/estudiantes/{$id}", $params);
        $output = HttpHelper::getContent($response);
        if (empty($output)) {
            return null;
        }
        return reset($output);
    }

    public function getByUsuarioId($id, array $params=[])
    {
        $this->requiresClientAccessToken();
        $params['usuario_id'] = is_array($id) ? implode('+', $id) : $id;
        $response = $this->_get("/escolares/estudiantes", $params);
        $output = HttpHelper::getContent($response);
        if (empty($output)) {
            return null;
        }
        return reset($output);
    }
}