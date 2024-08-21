<?php

namespace ITColima\SiitecApi\Resources\Escolares;

use Francerz\Http\Utils\Constants\MediaTypes;
use Francerz\Http\Utils\HttpHelper;
use Francerz\JsonTools\JsonEncoder;
use InvalidArgumentException;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\AlumnoContacto;
use ITColima\SiitecApi\Model\Escolares\EstudianteDocumento;
use ITColima\SiitecApi\Model\Escolares\EstudianteEmergencia;

class EstudiantesResource extends AbstractResource
{
    /**
     * Obtiene los estudiantes según los parametros proporcionados
     *
     * @param array $params
     *  - num_control: Número de control de estudiante.
     *  - usuario_id: ID de usuario de estudiante.
     * @return array
     */
    public function getAll(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet('/escolares/estudiantes', $params);
        return HttpHelper::getContent($response);
    }

    public function getByNumControl(string $num_control, array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $params['num_control'] = $num_control;
        $response = $this->protectedGet('/escolares/estudiantes', $params);
        $output = HttpHelper::getContent($response);
        if (empty($output)) {
            return null;
        }
        return reset($output);
    }

    /**
     * @param int|string $id
     * @param array $params
     * @return Estudiante
     */
    public function getById($id, array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $id = is_array($id) ? implode('+', $id) : $id;
        $response = $this->protectedGet("/escolares/estudiantes/{$id}", $params);
        $output = HttpHelper::getContent($response);
        if (empty($output)) {
            return null;
        }
        return $output;
    }

    public function getByUsuarioId($id, array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $params['usuario_id'] = is_array($id) ? implode('+', $id) : $id;
        $response = $this->protectedGet("/escolares/estudiantes", $params);
        $output = HttpHelper::getContent($response);
        if (empty($output)) {
            return null;
        }
        return reset($output);
    }

    /**
     * @param int|string $id_estudiante
     * @param int|string $id_documento
     * @param EstudianteDocumento $data
     * @return void
     */
    public function putDocumento($id_estudiante, $id_documento, EstudianteDocumento $data)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedPut(
            "/escolares/estudiantes/{$id_estudiante}/documentos/{$id_documento}",
            $data,
            MediaTypes::APPLICATION_JSON
        );
        return $response;
    }

    /**
     * @param int|string $id_estudiante
     * @param EstudianteDocumento[] $documentos
     */
    public function putDocumentos($id_estudiante, array $documentos)
    {
        foreach ($documentos as $d) {
            $d->alumno_id = $id_estudiante;
            if (!$d instanceof EstudianteDocumento) {
                throw new InvalidArgumentException("array \$documentos MUST contain only EstudianteDocumento objects.");
            }
        }
        $this->requiresClientAccessToken(true);
        $response = $this->protectedPost(
            "/escolares/estudiantes/{$id_estudiante}/documentos",
            $documentos,
            MediaTypes::APPLICATION_JSON
        );
        return $response;
    }

    /**
     * @param EstudianteDocumento[] $documentos
     */
    public function postDocumentosBatch(array $documentos)
    {
        foreach ($documentos as $d) {
            if (!$d instanceof EstudianteDocumento) {
                throw new InvalidArgumentException("array \$documentos MUST contain only EstudianteDocumento objects.");
            }
        }
        $this->requiresClientAccessToken(true);
        $response = $this->protectedPost(
            "/escolares/estudiantes/documentos/batch",
            $documentos,
            MediaTypes::APPLICATION_JSON
        );
        return $response;
    }

    public function putFotoFromFilepath($id_estudiante, string $filepath)
    {
        if (!file_exists($filepath)) {
            throw new InvalidArgumentException('Path does not exists.');
        }
        $data = base64_encode(file_get_contents($filepath));

        $this->requiresClientAccessToken(true);
        $response = $this->protectedPut(
            "/escolares/estudiantes/{$id_estudiante}/foto",
            $data,
            MediaTypes::TEXT_PLAIN
        );
        return $response;
    }

    /**
     * @param int|string $id_estudiante
     * @return AlumnoContacto[]
     */
    public function getContactos($id_estudiante)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedGet("/escolares/estudiantes/{$id_estudiante}/contactos");
        return JsonEncoder::decode((string)$response->getBody(), AlumnoContacto::class);
    }

    /**
     * @param int|string $id_estudiante
     * @return EstudianteEmergencia
     */
    public function getDatosEmergencia($id_estudiante)
    {
        $this->requiresOwnerAccessToken(true);
        $response = $this->protectedGet("/escolares/estudiantes/{$id_estudiante}/emergencia");
        return JsonEncoder::decode((string)$response->getBody(), EstudianteEmergencia::class);
    }
}
