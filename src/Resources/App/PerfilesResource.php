<?php

namespace ITColima\SiitecApi\Resources\App;

use Francerz\JsonTools\JsonEncoder;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\App\Perfil;

class PerfilesResource extends AbstractResource
{
    /**
     * @param array $params
     * @return Perfil[]
     */
    public function getAll(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet('/app/perfiles', $params);
        return JsonEncoder::decode((string)$response->getBody(), Perfil::class);
    }
}
