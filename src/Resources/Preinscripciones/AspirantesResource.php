<?php

namespace ITColima\SiitecApi\Resources\Preinscripciones;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;

class AspirantesResource extends AbstractResource
{
    public function getById($aspirante_id, array $params = [])
    {
        $response = $this->_get("/preinscripciones/aspirantes/{$aspirante_id}");
        return HttpHelper::getContent($response);
    }
}