<?php 

namespace ITColima\SiitecApi\Resources\Google;

use Francerz\Http\Utils\Constants\MediaTypes;
use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\Google\Member;
use ITColima\SiitecApi\Model\Google\User;
use ITColima\SiitecApi\Model\Google\UserPhoto;

class UsersResource extends AbstractResource
{
    /**
     * Envía los datos para poder crear una cuenta de usuario nueva.
     * 
     * @param User $user
     * @return void
     */
    public function create(User $user)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_post('/google/users/', $user, MediaTypes::APPLICATION_JSON);
        return HttpHelper::getContent($response);
    }

    /**
     * Actualiza el nombre de usuario del correo especificado.
     *
     * @param string $email
     * @param User $user
     * @return void
     */
    public function update($email, User $user)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_patch("/google/users/{$email}", $user);
        return HttpHelper::getContent($response);
    }

    /**
     * Actualiza la foto del usuario segun el correo.
     *
     * @param string $email
     * @param UserPhoto $photo
     * @return void
     */
    public function putPhoto($email, UserPhoto $photo)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_put("/google/users/{$email}/foto", $photo, MediaTypes::APPLICATION_JSON);
        return HttpHelper::getContent($response);
    }

      /**
     * Actualiza el nombre de usuario del correo especificado.
     *
     * @param string $email
     * @param string $newEmail
     * @return void
     */
    public function updateEmail($email, $newEmail)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_post("/google/users/{$email}/email", ['newEmail' => $newEmail]);
        return HttpHelper::getContent($response);
    }

    /**
     * Suspende la cuenta de correo deseada.
     *
     * @param string $email
     * @return void
     */
    public function suspend($email)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_post("/google/users/{$email}/suspender", []);
        return HttpHelper::getContent($response);
    }

    /**
     * Activa la cuenta de correo deseada. 
     *
     * @param string $email
     * @return void
     */
    public function activate($email)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_post("/google/users/{$email}/activar", []);
        return HttpHelper::getContent($response);
    }

    /**
     * Cambia la contraseña de un correo.
     *
     * @param string $email
     * @param string $newPassword
     * @return void
     */
    public function setPassword($email, $newPassword)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_post("/google/users/{$email}/password", ['password' => $newPassword]);
        return HttpHelper::getContent($response);
    }

    /**
     * Cambia la contraseña de un correo con SHA1.
     *
     * @param string $email
     * @param string $newPasswordSha1
     * @return void
     */
    public function setPasswordSha1($email, $newPasswordSHA1)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_post("/google/users/{$email}/password/sha1", ['password' => $newPasswordSHA1]);
        return HttpHelper::getContent($response);
    }

    /**
     * Obtiene la información de un usuario atraves de una dirección de correo.
     *
     * @param string $email
     * @param array $params
     * @return Usuario
     */
    public function getInfo($email)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_get("/google/users/{$email}", []);
        return httpHelper::getContent($response);
    }

    /**
     * Elimina una dirección de correo.
     *
     * @param string $email
     * @return void
     */
    public function delete($email)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_delete("/google/users/{$email}");
        return HttpHelper::getContent($response);
    }

    /**
     * Remueve un alias deseado de una dirección de correo.
     *
     * @param string $email
     * @param string $alias
     * @return void
     */
    public function removeAlias($email, $alias)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_delete("/google/users/{$email}/alias", ['alias' => $alias]);
        return HttpHelper::getContent($response);
    }

    /**
     * Añade un alias a una dirección de correo.
     *
     * @param string $email
     * @param string $alias
     * @return void
     */
    public function addAlias($email, $alias)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_post("/google/users/{$email}/alias", ['alias' => $alias]);
        return HttpHelper::getContent($response);
    }

    /**
     *  Funcion para obtener la informacion de un grupo.
     * 
     * @param string $email
     * @return void
     */
    public function getInfoGroup($email)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_get("/google/users/{$email}/group", []);
        return httpHelper::getContent($response);
    }

    /**
     * Funcion para añadir miembros  a un grupo.
     * 
     * @param string $email
     * @param Member $member
     * @return void
     */
    public function insertMemeber($email, Member $member)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->_put("/google/users/{$email}/group", $member, MediaTypes::APPLICATION_JSON);
        return HttpHelper::getContent($response);
    }
}