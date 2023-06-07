<?php
namespace ITColima\SiitecApi\Resources\Usuario;

use Francerz\Http\Utils\HttpHelper;
use Francerz\JsonTools\JsonEncoder;
use Francerz\PowerData\Objects;
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
        $this->requiresAccessToken(true);
        $response = $this->protectedGet('/usuarios/perfil/own');
        return JsonEncoder::decode((string)$response->getBody(), Perfil::class);
    }
}
