Cliente PHP de la API de SIITEC
=======================================

Instalación
---------------------------------------

La instalación del paquete se puede hacer mediante **composer** utilizando el
siguiente comando:

```sh
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

$siitecApi = new SiitecApi();
```

> **CARGA AUTOMÁTICA DE LAS VARIABLES DE ENTORNO `$_ENV`**
>
> La API de SIITEC puede cargar automáticamente las variables de entorno si
> se utiliza un framework o librería que las cargue desde un archivo `.env`.
> En el archivo deberían incluirse de la siguiente manera:
> ```sh
> # ===================================
> # SIITEC API SETTINGS
> # ===================================
> SIITEC_API_CLIENT_ID = '<client_id>'
> SIITEC_API_CLIENT_SECRET = '<client_secret>'
> ```

> **NOTA**
>
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
use ITColima\SiitecApi\SiitecApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OAuth2Controller
{
    /**
     * Recibe la solicitud del cliente para iniciar proceso de inicio de sesión.
     *
     * Ruta: GET /oauth2[/]
     */
    public function indexGet(): ResponseInterface
    {
        $siitecApi = new SiitecApi();

        if ($api->isLoggedIn()) {
            return SiitecApi::redirectTo(SiitecApi::siteUrl());
        }

        $response = $siitecApi->login(
            SiitecApi::siteUrl('/oauth2/login_handler'),
            SiitecApi::siteUrl('/oauth2/logout')
        );
        return $response;
    }

    /**
     * Recibe la respuesta del servidor de autorización con el código de
     * autorización o error, según corresponda el caso.
     *
     * Ruta: GET /oauth2/callback[/]
     */
    public function callbackGet(ServerRequestInterface $request): ResponseInterface
    {
        $siitecApi = new SiitecApi();
        $redirUri = $siitecApi->handleLogin($request);
        return SiitecApi::redirectTo($redirUri);
    }

    /**
     * Destruye la sesión y hace la solicitud para cancelar la sesión activa del
     * usuario en SIITEC.
     *
     * Ruta: GET /logout[/]
     */
    public function logoutGet(ServerRequestInterface $request): ResponseInterface
    {
        $siitecApi = new SiitecApi();
        $response = $siitecApi->handleLogout($request);
        session_destroy();
        return SiitecApi::emitResponse($response);
    }
}
```

> **NOTA**
>
> La implementación puede variar dependiendo del framework y técnica
> para el desarrollo que se esté utilizando.

DEPURACIÓN
----------

De manera predeterminada la API tiene asociadas direcciones de inicialización,
mismas que pueden modificarse para depuración y ejecución con entornos locales.

```sh
# =====================================
# SIITEC API DEBUGGING
# =====================================
SIITEC_HOME_BASE                = 'https://siitec.colima.tecnm.mx'
SIITEC_API_RESOURCES_ENDPOINT   = 'https://siitec.colima.tecnm.mx/api/index.php'
```

> **VARIABLES DE ENTORNO ESPECÍFICAS**
>
> Alternativamente puede utilizar variables de entorno específicas para los
> mecanismo de autenticación, aunque esto se recomienda para depuraciones
> específicas, y no conviene utilizarse de manera generalizada.
> 
> ```sh
> # =====================================
> # SIITEC API DEBUGGING
> # =====================================
> SIITEC_API_AUTHORIZE_ENDPOINT = 'https://siitec.colima.tecnm.mx/index.php/oauth2/authorize'
> SIITEC_API_TOKEN_ENDPOINT     = 'https://siitec.colima.tecnm.mx/index.php/oauth2/token'
> SIITEC_API_RESOURCES_ENDPOINT = 'https://siitec.colima.tecnm.mx/api/index.php'
> ```