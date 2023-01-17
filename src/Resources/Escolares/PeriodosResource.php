<?php

namespace ITColima\SiitecApi\Resources\Escolares;

use Francerz\Http\Utils\HttpHelper;
use Francerz\JsonTools\JsonEncoder;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Escolares\Periodo;
use ITColima\SiitecApi\Model\Escolares\PeriodoCarrera;

class PeriodosResource extends AbstractResource
{
    /**
     * Obtiene los periodos escolares según los parámetros proporcionados
     *
     * @param array $params
     *  - fin_desde: Periodos que terminen a partir de la fecha proporcionada
     *  - inicio_desde: Periodos que inician a partir de la fecha proporcionada.
     * @return Periodo[]
     */
    public function getAll(array $params = [])
    {
        $this->requiresAccessToken(false);
        $response = $this->protectedGet('/escolares/periodos', $params);
        return JsonEncoder::decode((string)$response->getBody(), Periodo::class);
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

    /**
     * @param array $params
     * @return Periodo
     */
    public function getCurrent(array $params = [])
    {
        $this->requiresAccessToken(false);
        $response = $this->protectedGet('/escolares/periodos/@current', $params);
        return JsonEncoder::decode((string)$response->getBody(), Periodo::class);
    }

    /**
     * @param int|string $periodo_id
     * @param int|string $carrera_id
     * @return PeriodoCarrera
     */
    public function getCarrera($periodo_id, $carrera_id)
    {
        $this->requiresAccessToken(false);
        $response = $this->protectedGet("/escolares/periodos/{$periodo_id}/carreras/{$carrera_id}");
        return JsonEncoder::decode((string)$response->getBody(), PeriodoCarrera::class);
    }
}
