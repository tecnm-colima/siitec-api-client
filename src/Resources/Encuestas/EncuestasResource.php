<?php

namespace ITColima\SiitecApi\Resources\Encuestas;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;

class EncuestasResource extends AbstractResource
{
    public function getAll()
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet('/encuestas');
        return HttpHelper::getContent($response);
    }

    public function getRealizada($encuesta_id, $usuario_id)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet("/encuestas/{$encuesta_id}/usuarios/{$usuario_id}");
        return HttpHelper::getContent($response);
    }
}
