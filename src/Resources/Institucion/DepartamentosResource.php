<?php

namespace ITColima\SiitecApi\Resources\Institucion;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Institucion\Departamento;

class DepartamentosResource extends AbstractResource
{
    /**
     * Obtiene un listado de departamentos disponibles en el sistema.
     *
     * @param array $params
     * @return Departamento[]
     */
    public function getAll(array $params = [])
    {
        $this->requiresAccessToken(false);
        $response = $this->_get('/institucion/departamentos', $params);
        $data = HttpHelper::getContent($response);
        return $this->castArray($data, Departamento::class);
    }

    /**
     * Obtiene un departamento a partir de su ID.
     *
     * @param int|string $departamento_id
     * @param array $params
     * @return Departamento
     */
    public function getById($departamento_id, array $params = [])
    {
        $this->requiresAccessToken(false);
        $response = $this->_get("/institucion/departamentos/{$departamento_id}", $params);
        $data = HttpHelper::getContent($response);
        return $this->cast($data, Departamento::class);
    }
}
