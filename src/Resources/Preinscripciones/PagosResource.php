<?php

namespace ITColima\SiitecApi\Resources\Preinscripciones;

use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Preinscripciones\Pago;

class PagosResource extends AbstractResource
{
    /**
     * Registra un pago de preinscripción.
     *
     * @param Pago $pago Objeto con los parámetros de preinscripción.
     * @return void
     */
    public function put(Pago $pago)
    {
        $this->requiresClientAccessToken();
        $response = $this->protectedPut("/preinscripciones/pagos/{$pago->id_aspirante}/{$pago->id_periodo}", null);
        return $response;
    }
}
