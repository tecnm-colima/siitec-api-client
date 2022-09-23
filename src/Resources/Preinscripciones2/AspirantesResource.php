<?php

namespace ITColima\SiitecApi\Resources\Preinscripciones2;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;

class AspirantesResource extends AbstractResource
{
    public function getById($aspirante_id, array $params = [])
    {
        $this->requiresClientAccessToken();
        $response = $this->protectedGet("/preinscripciones2/aspirantes/{$aspirante_id}");
        return HttpHelper::getContent($response);
    }
}
