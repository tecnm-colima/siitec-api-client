<?php

namespace ITColima\SiitecApi\Resources\RH;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\App\Usuarios\Empleado;

class EmpleadosResource extends AbstractResource
{
    /**
     * Obtiene un listado de los empleados activos en el sistema.
     *
     * @param array $params
     * Lista de parámetros disponibles para filtrar empleados:
     * - `string rfc`
     * - `string curp`
     * - `string nombre`
     * - `int departamento`
     * - `int departamento_academico`
     * @return Empleado[]
     */
    public function getAll(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet('/rh/empleados', $params);
        return $this->castArray(HttpHelper::getContent($response), Empleado::class);
    }

    /**
     * Obtiene el registro de un empleado a partir de su ID.
     *
     * @param int|string $empleado_id
     * @param array $params
     * Lista de parámetros disponibles para filtrar empleados:
     * - `string rfc`
     * - `string curp`
     * - `string nombre`
     * - `int departamento`
     * - `int departamento_academico`
     * @return Empleado
     */
    public function getById($empleado_id, array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet("/rh/empleados/{$empleado_id}", $params);
        return $this->cast(HttpHelper::getContent($response), Empleado::class);
    }
}
