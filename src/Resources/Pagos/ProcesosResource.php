<?php

namespace ITColima\SiitecApi\Resources\Pagos;

use Francerz\Http\Utils\HttpHelper;
use Francerz\PowerData\Objects;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Pagos\Pago;

class ProcesosResource extends AbstractResource
{
    public function getPagos($id_proceso, array $params = [])
    {
        $this->requiresClientAccessToken();
        $response = $this->_get("/pagos/procesos/{$id_proceso}/pagos", $params);
        $rows = HttpHelper::getContent($response);
        foreach ($rows as &$row) {
            $row = Objects::cast($row, Pago::class);
        }
        return $rows;
    }

    public function getById($id_proceso, array $params = [])
    {
        $response = $this->_get("/pagos/procesos/{$id_proceso}", $params);
        return HttpHelper::getContent($response);
    }
}
