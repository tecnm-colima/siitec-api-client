<?php

namespace ITColima\SiitecApi\Resources\Preinscripciones;

use Francerz\Http\Utils\Constants\MediaTypes;
use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Preinscripciones\Aspirante;

class AspirantesResource extends AbstractResource
{
    public function getById($aspirante_id, array $params = [])
    {
        $this->requiresClientAccessToken();
        $response = $this->protectedGet("/preinscripciones/aspirantes/{$aspirante_id}");
        return HttpHelper::getContent($response);
    }

    public function patch($id_aspirante, Aspirante $aspirante)
    {
        $this->requiresClientAccessToken();
        $response = $this->protectedPatch(
            "/preinscripciones/aspirantes/{$id_aspirante}",
            $aspirante,
            MediaTypes::APPLICATION_JSON
        );
        return HttpHelper::getContent($response);
    }
}
