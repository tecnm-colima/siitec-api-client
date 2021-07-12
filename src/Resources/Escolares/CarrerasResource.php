<?php

namespace ITColima\SiitecApi\Resources\Escolares;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;

class CarrerasResource extends AbstractResource
{
    /**
     * Obtiene un listado de carreras disponibles en el sistema.
     *
     * @param array $params
     * @return array
     */
    public function getAll(array $params = [])
    {
        $this->requiresAccessToken(false);
        $response = $this->_get('/escolares/carreras', $params);
        return HttpHelper::getContent($response);
    }

    public function getById($carrera_id, array $params = [])
    {
        if (is_array($carrera_id)) {
            $carrera_id = join('+', $carrera_id);
        }
        $this->requiresAccessToken(false);
        $response = $this->_get("/escolares/carreras/{$carrera_id}", $params);
        return HttpHelper::getContent($response);
    }
}