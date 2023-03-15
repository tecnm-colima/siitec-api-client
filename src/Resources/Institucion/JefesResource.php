<?php

namespace ITColima\SiitecApi\Resources\Institucion;

use Francerz\JsonTools\JsonEncoder;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Institucion\Jefe;

class JefesResource extends AbstractResource
{

    /**
     * @param array $params
     * @return Jefe[]
     */
    public function getAll(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet('/institucion/jefes', $params);
        return JsonEncoder::decode((string)$response->getBody(), Jefe::class);
    }

    /**
     * @param int|string $pk_id
     * @param array $params
     * @return Jefe
     */
    public function getById($pk_id, array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet("/institucion/jefes/{$pk_id}", $params);
        return JsonEncoder::decode((string)$response->getBody(), Jefe::class);
    }
}
