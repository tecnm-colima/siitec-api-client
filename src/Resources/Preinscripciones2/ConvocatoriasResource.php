<?php

namespace ITColima\SiitecApi\Resources\Preinscripciones2;

use Francerz\JsonTools\JsonEncoder;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Preinscripciones2\Inscrito;

class ConvocatoriasResource extends AbstractResource
{
    public function getInscritos($id_convocatoria)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet("/preinscripciones2/convocatorias/{$id_convocatoria}/inscritos");
        return JsonEncoder::decode((string)$response->getBody(), Inscrito::class);
    }
}
