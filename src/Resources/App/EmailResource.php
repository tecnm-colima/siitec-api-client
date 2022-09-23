<?php

namespace ITColima\SiitecApi\Resources\App;

use Francerz\Http\Utils\Constants\MediaTypes;
use Francerz\Http\Utils\HttpHelper;
use ITColima\SiitecApi\AbstractResource;
use ITColima\SiitecApi\Model\App\Email;

class EmailResource extends AbstractResource
{
    /**
     * Envía un correo electrónico utilizando la misma dirección de correo de SIITEC.
     *
     * @param Email $email
     * @return void
     */
    public function send(Email $email)
    {
        $this->requiresClientAccessToken(true);
        $response = $this->protectedPost('/app/email', $email, MediaTypes::APPLICATION_JSON);
        return HttpHelper::getContent($response);
    }
}
