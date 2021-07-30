Cliente PHP de la API de SIITEC
=======================================

Instalación
---------------------------------------

La instalación del paquete se puede hacer mediante **composer** utilizando el
siguiente comando:

```
composer require itcolima/siitec-api-client
```

Inicialización
---------------------------------------

La forma de inicializar la API de SIITEC es mediante la clase
`ITColima\SiitecApi\SiitecApi`.

```php
use ITColima\SiitecApi\SiitecApi;

// Carga manual de las variables de entorno
$_ENV['SIITEC_API_CLIENT_ID'] = '<client_id>';
$_ENV['SIITEC_API_CLIENT_SECRET'] = '<client_secret>';

$api = new SiitecApi();
```

> **Carga automática de las variables de entorno `$_ENV`**
> La API de SIITEC puede cargar automáticamente las variables de entorno si
> se utiliza un framework o librería que las cargue desde un archivo `.env`.
> En el archivo deberían incluirse de la siguiente manera:
> ```bash
> # Credenciales de API de SIITEC
> SIITEC_API_CLIENT_ID = '<client_id>'
> SIITEC_API_CLIENT_SECRET = '<client_secret>'
> ```

> **NOTA**  
> Los valores de los parámetros `<client_id>` y `<client_secret>` son proporcionados
> por el Departamento de Centro de Cómputo del Instituto Tecnológico de Colima.

Inicio de sesión
---------------------------------------

Una de las funcionalidades especiales de la libería API de SIITEC es permitir
acceso a recursos propios de cada usuario, identificándolo mediante su inicio
de sesión.

El inicio de sesión en la API de SIITEC se realiza utilizando el Framework
de Autorización OAuth 2.0, el cual permite obtener acceso a recursos protegidos
utilizando claves temporales de acceso, llamadas Access Token, y permitiendo una
operación continua.

Estos procesos de inicio de sesión requieren de una compleja red de interacciones
e intercambio de peticiones HTTP entre el Cliente (aplicación) y el
servidor de SIITEC. Ese complejo mecanismo se simplifica utilizando funciones
de la librería, que permiten centrarse menos en la estructura y más en la
funcionalidad.

### Implementación de las funciones de inicio de sesión

```php
namespace App\Controllers;

use ITColima\SiitecApi\SiitecApi;
use Francerz\Http\Utils\UriHelper;

class OAuth2 extends AbstractController
{
    public function login()
    {
        $siitecApi = new SiitecApi();

        if ($api->isLoggedIn()) {
            $response = $siitecApi->redirectTo($siitecApi->siteUrl());
            return $siitecApi->emitResponse($response);
        }

        $response = $siitecApi->login(
            $siitecApi->siteUrl('/oauth2/login_handler'),
            $siitecApi->siteUrl('/oauth2/logout')
        );
        return $siitecApi->emitResponse($response);
    }

    public function login_handler()
    {
        $siitecApi = new SiitecApi();
        $siitecApi->handleLogin();
        return $siitecApi->redirectTo($siitec->siteUrl());
    }

    public function logout()
    {
        $siitecApi = new SiitecApi();
        $response = $siitecApi->handleLogout();
        return $siitecApi->emitResponse($response);
    }
}
```

> **NOTA**
> La implementación puede variar dependiendo del framework y técnica
> para el desarrollo que se esté utilizando.

DEPURACIÓN
----------

De manera predeterminada la API tiene asociadas direcciones de inicialización,
mismas que pueden modificarse para depuración y ejecución con entonos locales.

```env
SIITEC_API_AUTHORIZE_ENDPOINT = 'https://siitec.colima.tecnm.mx/index.php/oauth2/authorize'
SIITEC_API_TOKEN_ENDPOINT = 'https://siitec.colima.tecnm.mx/index.php/oauth2/token'
SIITEC_API_RESOURCE_ENDPOINT = 'https://siitec.colima.tecnm.mx/api/index.php'
```