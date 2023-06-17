<?php

namespace ITColima\SiitecApi\Resources\Escolares;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Escolares\Carrera;

class CarrerasResource extends AbstractResource
{
    /**
     * Obtiene un listado de carreras disponibles en el sistema.
     *
     * @param array $params
     * @return Carrera[]
     */
    public function getAll(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet('/escolares/carreras', $params);
        $data = HttpHelper::getContent($response);
        return $this->castArray($data, Carrera::class);
    }

    /**
     * Obtiene una carrera a partir de su ID.
     *
     * @param int|string $carrera_id
     * @param array $params
     * @return Carrera
     */
    public function getById($carrera_id, array $params = [])
    {
        $this->requiresClientAccessToken(true);
        if (is_array($carrera_id)) {
            $carrera_id = join('+', $carrera_id);
        }
        $response = $this->protectedGet("/escolares/carreras/{$carrera_id}", $params);
        $data = HttpHelper::getContent($response);
        return is_array($carrera_id) ?
            $this->castArray($data, Carrera::class) :
            $this->cast($data, Carrera::class);
    }
}
