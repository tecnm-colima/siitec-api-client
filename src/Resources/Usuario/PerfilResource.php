<?php
namespace ITColima\SiitecApi\Resources\Usuario;

use Francerz\Http\Utils\HttpHelper;
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
        $response = $this->_get('/usuarios/perfil/own');
        return HttpHelper::getContent($response);
    }
}
