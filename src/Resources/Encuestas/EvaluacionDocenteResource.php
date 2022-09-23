<?php

namespace ITColima\SiitecApi\Resources\Encuestas;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;

class EvaluacionDocenteResource extends AbstractResource
{
    public function getAll(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet('/encuestas/edocente', $params);
        return HttpHelper::getContent($response);
    }

    public function getRealizada($encuesta_id, $usuario_id, array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet("/encuestas/edocente/{$encuesta_id}/usuarios/{$usuario_id}", $params);
        return HttpHelper::getContent($response);
    }
}
