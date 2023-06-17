<?php

namespace ITColima\SiitecApi\Resources\Usuario;

use Francerz\JsonTools\JsonEncoder;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Perfil;

class PerfilResource extends AbstractResource
{
    /**
     * Obtiene el perfil del usuario actual.
     *
     * @return Perfil|null
     */
    public function getOwn()
    {
        $this->requiresClientAccessToken(true);
        $this->requiresOwnerAccessToken(true);
        $response = $this->protectedGet('/usuarios/perfil/own');
        return JsonEncoder::decode((string)$response->getBody(), Perfil::class);
    }
}
