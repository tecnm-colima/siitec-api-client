<?php

namespace ITColima\SiitecApi\Resources\Escolares;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;

class PeriodosResource extends AbstractResource
{
    /**
     * Obtiene los periodos escolares según los parámetros proporcionados
     *
     * @param array $params
     *  - fin_desde: Periodos que terminen a partir de la fecha proporcionada
     *  - inicio_desde: Periodos que inician a partir de la fecha proporcionada.
     * @return array
     */
    public function getAll(array $params = [])
    {
        $this->requiresAccessToken(false);
        $response = $this->protectedGet('/escolares/periodos', $params);
        return HttpHelper::getContent($response);
    }

    public function getById($periodo_id, array $params = [])
    {
        if (is_array($periodo_id)) {
            $periodo_id = join('+', $periodo_id);
        }
        $this->requiresAccessToken(false);
        $response = $this->protectedGet("/escolares/periodos/{$periodo_id}", $params);
        return HttpHelper::getContent($response);
    }

    public function getCurrent(array $params = [])
    {
        $this->requiresAccessToken(false);
        $response = $this->protectedGet('/escolares/periodos/@current', $params);
        return HttpHelper::getContent($response);
    }
}
