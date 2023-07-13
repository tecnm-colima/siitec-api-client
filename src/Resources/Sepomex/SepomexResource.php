<?php

namespace ITColima\SiitecApi\Resources\Sepomex;

use Francerz\JsonTools\JsonEncoder;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Sepomex\Asentamiento;
use ITColima\SiitecApi\Model\Sepomex\Ciudad;
use ITColima\SiitecApi\Model\Sepomex\Estado;
use ITColima\SiitecApi\Model\Sepomex\Municipio;
use ITColima\SiitecApi\Model\Sepomex\TipoAsentamiento;

class SepomexResource extends AbstractResource
{
    /**
     * @param array $params
     * @return TipoAsentamiento[]
     */
    public function getTiposAsentamiento(array $params = [])
    {
        $response = $this->protectedGet('/sepomex/tipos-asentamiento', $params);
        return JsonEncoder::decode((string)$response->getBody(), TipoAsentamiento::class);
    }

    /**
     * @param array $params
     * @return Estado[]
     */
    public function getEstados(array $params = [])
    {
        $response = $this->protectedGet('/sepomex/estados', $params);
        return JsonEncoder::decode((string)$response->getBody(), Estado::class);
    }

    /**
     * @param array $params
     * @return Ciudad[]
     */
    public function getCiudades(array $params = [])
    {
        $response = $this->protectedGet('/sepomex/ciudades', $params);
        return JsonEncoder::decode((string)$response->getBody(), Ciudad::class);
    }

    /**
     * @param array $params
     * @return Municipio[]
     */
    public function getMunicipios(array $params = [])
    {
        $response = $this->protectedGet('/sepomex/municipios', $params);
        return JsonEncoder::decode((string)$response->getBody(), Municipio::class);
    }

    /**
     * @param array $params
     * @return Asentamiento[]
     */
    public function getAsentamientos(array $params = [])
    {
        $response = $this->protectedGet('/sepomex/asentamientos', $params);
        return JsonEncoder::decode((string)$response->getBody(), Asentamiento::class);
    }
}
