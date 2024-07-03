<?php

namespace ITColima\SiitecApi\Resources\Institucion;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Institucion\Edificio;

class EdificiosResource extends AbstractResource
{
    /**
     * Obtiene un listado de los edificios registrados en el sistema.
     *
     * @param array $params
     * @return Edificio[]
     */
    public function getAll(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet('/institucion/edificios', $params);
        return $this->castArray(HttpHelper::getContent($response), Edificio::class);
    }
}
