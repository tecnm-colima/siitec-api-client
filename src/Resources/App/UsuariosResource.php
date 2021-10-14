<?php

namespace ITColima\SiitecApi\Resources\App;

use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Perfil;

class UsuariosResource extends AbstractResource
{
    /**
     * @param int $id
     * @param array $params
     * @return Perfil
     */
    public function getById($id, array $params = [])
    {
        if (is_array($id)) {
            $id = join('+', $id);
        }
        $this->requiresClientAccessToken(true);
        $response = $this->_get("/app/usuarios/{$id}", $params);
        return HttpHelper::getContent($response);
    }

    /**
     * Find a user by given parameter
     *
     * @param array $params
     *  - term: Termino de búsqueda
     *  - matricula: Número de control o ID de aspirante
     *  - rol: Tipo de usuario (alumno, aspirante, empleado)
     *  - curp: CURP del usuario
     *  - correo: Dirección de correo electrónico
     *  - usuario: Nombre de usuario utilizado para ingresar a SIITEC
     *  - nombre: Nombre o Apellidos del usuario
     * @return Perfil[]
     */
    public function find(array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_get('/app/usuarios', $params);
        return HttpHelper::getContent($response);
    }

    /**
     * @return Perfil[]
     */
    public function findTerm($term, array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $params['q'] = $term;
        $response = $this->_get('/app/usuarios', $params);
        return HttpHelper::getContent($response);
    }

    /**
     * @param string $matricula
     * @param array $params
     * @return Perfil[]
     */
    public function findMatricula($matricula, array $params = [])
    {
        $this->requiresClientAccessToken(true);
        $params['matricula'] = $matricula;
        $response = $this->_get('/app/usuarios', $params);
        return HttpHelper::getContent($response);
    }

    /**
     * @param string $curp
     * @param array $params
     * @return Perfil[]
     */
    public function findCurp($curp, array $params = []) 
    {
        $this->requiresClientAccessToken(true);
        $params['curp'] = $curp;
        $response = $this->_get('/app/usuarios', $params);
        return HttpHelper::getContent($response);
    }
}
