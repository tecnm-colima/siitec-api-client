<?php

namespace ITColima\SiitecApi;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Francerz\Http\Client as HttpClient;
use Francerz\Http\HttpFactory;
use Francerz\Http\Server;
use Francerz\Http\Uri;
use Francerz\Http\Utils\HttpFactoryManager;
use Francerz\Http\Utils\HttpHelper;
use Francerz\Http\Utils\ServerInterface;
use Francerz\Http\Utils\UriHelper;
use Francerz\OAuth2\Client\OAuth2Client;
use Francerz\OAuth2\ScopeHelper;
use InvalidArgumentException;
use ITColima\SiitecApi\Model\Perfil;
use ITColima\SiitecApi\Resources\Usuario\PerfilResource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class SiitecApi
{
    public const SSL_MODE_DEFAULT = 0;
    public const SSL_MODE_DISABLED = 1;
    public const SSL_MODE_INTERNAL = 2;

    private const ENV_RESOURCES_ENDPOINT = 'SIITEC_API_RESOURCES_ENDPOINT';
    private const ENV_LOGOUT_ENDPOINT = 'SIITEC_API_LOGOUT_ENDPOINT';
    private const ENV_PAGOS_URI = 'SIITEC_API_PAGOS_URL';
    private const ENV_DOCENCIA_URI = 'SIITEC_API_DOCENCIA_URL';

    private const SESSION_PERFIL_KEY = 'siitec.perfil';
    private const SESSION_CALLBACK_KEY = 'siitec.oauth2Callback';

    public const QUERY_REDIR_PARAMETER = 'redir';

    private $httpClient;
    private $oauth2Client;
    private $oauth2ClientParams;
    private $resourcesEndpoint;

    /** @var Perfil */
    private $perfil = null;
    private $httpHelper;
    private $loginHandlerUri = null;

    #region STATIC METHODS
    public static function getPlatformUrl(string $url = ''): string
    {
        return SiitecApiConstants::PLATFORM_URL . '/' . ltrim($url, '/');
    }

    public static function getLogoutUrl(): string
    {
        return static::getPlatformUrl('/index.php/usuarios/logout');
    }

    public static function getPagosUrl(string $url = ''): string
    {
        static $pagosUrl = null;
        if (is_null($pagosUrl)) {
            $pagosUrl = array_key_exists(self::ENV_PAGOS_URI, $_ENV) ?
                $_ENV[self::ENV_PAGOS_URI] :
                self::getPlatformUrl('/pagos/index.php');
        }
        $retUrl = $pagosUrl;
        $retUrl .= empty($url) ? '' : '/' . ltrim($url, '/');
        return $retUrl;
    }

    public static function getDocenciaUrl(string $url = ''): string
    {
        static $docenciaUrl = null;
        if (is_null($docenciaUrl)) {
            $docenciaUrl = array_key_exists(self::ENV_DOCENCIA_URI, $_ENV) ?
                $_ENV[self::ENV_DOCENCIA_URI] :
                self::getPlatformUrl('/docencia/index.php');
        }
        $retUrl = $docenciaUrl;
        $retUrl .= empty($url) ? '' : '/' . ltrim($url, '/');
        return $retUrl;
    }

    public static function emitResponse(ResponseInterface $response, ?ServerInterface $server = null)
    {
        $server = $server ?? Server::new();
        $server->emitResponse($response);
    }

    public function redirectTo($location, int $code = StatusCodeInterface::STATUS_TEMPORARY_REDIRECT)
    {
        return $this->httpHelper->makeRedirect($location, $code);
    }

    public static function siteUrl(?string $path = null)
    {
        return UriHelper::getSiteUrl($path);
    }

    public static function baseUrl(?string $path = null)
    {
        return UriHelper::getBaseUrl($path);
    }
    #endregion

    public function __construct()
    {
        $this->initSessions();
        
        $this->httpClient = new HttpClient();
        $this->httpHelper = new HttpHelper(new HttpFactoryManager(new HttpFactory()));

        $this->oauth2ClientParams = new SiitecOAuth2Client();
        $this->oauth2Client = new OAuth2Client(
            $this->oauth2ClientParams,
            $this->httpClient,
            $this->httpHelper->getHttpFactoryManager()->getRequestFactory(),
            $this->oauth2ClientParams,  // client saver
            $this->oauth2ClientParams,  // owner saver
            $this->oauth2ClientParams,  // state manager
            $this->oauth2ClientParams   // pkce manager
        );
        $this->init();
    }

    private function init()
    {
        $this->setSSLMode(self::SSL_MODE_DEFAULT);
        $this->setResourcesEndpoint(new Uri(SiitecApiConstants::API_ENDPOINT));

        if (array_key_exists(self::ENV_RESOURCES_ENDPOINT, $_ENV)) {
            $this->setResourcesEndpoint(new Uri($_ENV[self::ENV_RESOURCES_ENDPOINT]));
        }
        if (array_key_exists(self::ENV_LOGOUT_ENDPOINT, $_ENV)) {
            $this->logoutUri = new Uri($_ENV[self::ENV_LOGOUT_ENDPOINT]);
        }
    }

    private function initSessions()
    {
        switch (session_status()) {
            case PHP_SESSION_DISABLED:
                throw new Exception('Cannot start bceause Sessions are disabled.');
            case PHP_SESSION_NONE:
                session_start();
                break;
        }
    }

    public function getHttpFactoryManager()
    {
        return $this->httpHelper->getHttpFactoryManager();
    }

    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function setResourcesEndpoint(UriInterface $resourcesEndpoint)
    {
        $this->resourcesEndpoint = $resourcesEndpoint;
    }

    public function getResourcesEndpoint(): UriInterface
    {
        return $this->resourcesEndpoint;
    }

    public function setSSLMode($mode = self::SSL_MODE_DEFAULT)
    {
        if (!$this->httpClient instanceof HttpClient) {
            return;
        }

        $httpClient = $this->httpClient;
        switch ($mode) {
            case self::SSL_MODE_DEFAULT:
                $httpClient->setSSLCheck(true);
                $httpClient->setCaCertFile(null);
                break;
            case self::SSL_MODE_INTERNAL:
                $httpClient->setSSLCheck(true);
                $httpClient->setCaCertFile(dirname(__FILE__, 2) . '/cacert.pem');
                break;
            case self::SSL_MODE_DISABLED:
                $httpClient->setSSLCheck(false);
                $httpClient->setCaCertFile(null);
                break;
        }
    }

    public function getOAuth2Client()
    {
        return $this->oauth2Client;
    }

    /**
     * Creates an URI for login and 'redir' query parameter to return after
     * login process completed.
     *
     * @param string|UriInterface $loginUri
     * @return UriInterface
     */
    public function redirectAuthUri($loginUri)
    {
        return $this->goToLogin($loginUri);
    }

    /**
     * Crea una URI para iniciar sesión en SIITEC y después regresar a la página
     * actual una vez que el proceso se haya completado.
     *
     * @param string|UriInterface $loginUri
     * @return UriInterface
     */
    public function goToLogin($loginUri)
    {
        $uriFactory = $this->httpHelper->getHttpFactoryManager()->getUriFactory();

        if (is_string($loginUri)) {
            $loginUri = $uriFactory->createUri($loginUri);
        }

        if (!$loginUri instanceof UriInterface) {
            throw new InvalidArgumentException('Cannot convert login uri.');
        }

        $currentUri = UriHelper::getCurrent($uriFactory);
        $loginUri = UriHelper::withQueryParam($loginUri, self::QUERY_REDIR_PARAMETER, (string)$currentUri);
        return $loginUri;
    }

    /**
     * Adds 'redir' parameter from login to login_handler.
     *
     * @param UriInterface $authorizeUri
     * @return UriInterface
     */
    private function addFollowParameters(UriInterface $authorizeUri)
    {
        $uriFactory = $this->httpHelper->getHttpFactoryManager()->getUriFactory();

        $redirectUri = UriHelper::getQueryParam($authorizeUri, 'redirect_uri');
        if (is_null($redirectUri)) {
            return $authorizeUri;
        }

        $currentUri = UriHelper::getCurrent($uriFactory);
        $redirectUri = $uriFactory->createUri($redirectUri);
        $redirectUri = UriHelper::copyQueryParams($currentUri, $redirectUri, [self::QUERY_REDIR_PARAMETER]);

        $authorizeUri = UriHelper::withQueryParam($authorizeUri, 'redirect_uri', $redirectUri);
        return $authorizeUri;
    }

    /**
     * Creates OAuth 2.0 Authorization Code Request Uri with given $scopes.
     *
     * @param array $scopes
     * @return UriInterface
     */
    private function createAuthorizationCodeUri(array $scopes = []): UriInterface
    {
        $scopes = ScopeHelper::merge($scopes, [SiitecUserScopes::GET_USUARIO_PERFIL_OWN]);
        $authorizeUri = $this->oauth2Client->createAuthorizationCodeUri($scopes);
        $authorizeUri = $this->addFollowParameters($authorizeUri);
        return $authorizeUri;
    }

    /**
     * Inicia proceso de inicio de sesión en SIITEC.
     *
     * @param string|UriInterface $callbackUri
     * @param string|UriInterface $logoutUri
     * @param string[]|string $scopes
     * @param string|null $state
     * @return ResponseInterface
     */
    public function login($callbackUri, $logoutUri, $scopes = [], $state = null)
    {
        $uriFactory = $this->httpHelper->getHttpFactoryManager()->getUriFactory();

        if (is_string($callbackUri)) {
            $callbackUri = $uriFactory->createUri($callbackUri);
        }
        if (!$callbackUri instanceof UriInterface) {
            throw new InvalidArgumentException('Invalid $callbackUri.');
        }
        $this->oauth2ClientParams->setCallbackEndpoint($callbackUri);

        if (is_string($logoutUri)) {
            $logoutUri = $uriFactory->createUri($logoutUri);
        }
        if (!$logoutUri instanceof UriInterface) {
            throw new InvalidArgumentException('Invalid $logoutUri.');
        }

        $scopes = ScopeHelper::merge($scopes, [SiitecUserScopes::GET_USUARIO_PERFIL_OWN]);
        $uri = $this->createAuthorizationCodeUri($scopes);

        $uri = UriHelper::withQueryParam($uri, 'logout', $logoutUri);

        $_SESSION[self::SESSION_CALLBACK_KEY] = UriHelper::getQueryParam($uri, 'redirect_uri');
        return $this->httpHelper->makeRedirect($uri);
    }

    public function handleLogin(?ServerRequestInterface $request = null)
    {
        $uriFactory = $this->getHttpFactoryManager()->getUriFactory();

        if (is_null($request)) {
            $request = $this->httpHelper->getCurrentRequest();
        }
        if (!empty($_SESSION[self::SESSION_CALLBACK_KEY])) {
            $this->oauth2ClientParams->setCallbackEndpoint(
                $uriFactory->createUri($_SESSION[self::SESSION_CALLBACK_KEY])
            );
        }
        $this->oauth2Client->handleCallback($request);
        $this->retrievePerfil();

        $uriFactory = $this->httpHelper->getHttpFactoryManager()->getUriFactory();
        $redirUri = $this->getRedir(UriHelper::getSiteUrl());
        $redirUri = $uriFactory->createUri($redirUri);
        return $redirUri;
    }

    #region Perfil (ResourceOwner)
    private function retrievePerfil()
    {
        $perfilResource = new PerfilResource($this);
        $perfil = $perfilResource->getOwn();
        if (!is_object($perfil)) {
            $perfil = is_string($perfil) ? $perfil : print_r($perfil);
            throw new RuntimeException("Failed retrieving perfil from API. {$perfil}");
        }
        $this->perfil = $_SESSION[self::SESSION_PERFIL_KEY] = $perfil;
    }

    private function loadPerfilFromSession()
    {
        if (array_key_exists(self::SESSION_PERFIL_KEY, $_SESSION) && is_object($_SESSION[self::SESSION_PERFIL_KEY])) {
            $this->perfil = $_SESSION[self::SESSION_PERFIL_KEY];
        }
    }

    private function unsetPerfil()
    {
        unset($this->perfil);
        unset($_SESSION[self::SESSION_PERFIL_KEY]);
    }

    public function getPerfil()
    {
        if (is_null($this->perfil)) {
            $this->loadPerfilFromSession();
        }
        return $this->perfil;
    }
    #endregion

    /**
     * Checks that User (Resource Owner) granted access.
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return !is_null($this->getPerfil());
    }

    /**
     * Revokes all tokens and user data.
     *
     * @return void
     */
    public function revoke()
    {
        $this->unsetPerfil();
        // $this->revokeOwnerAcccessToken();
    }

    /**
     * Handles direct logout.
     *
     * @return ResponseInterface
     */
    public function handleLogout(): ResponseInterface
    {
        $this->revoke();

        $currentUri = UriHelper::getCurrent($this->httpHelper->getHttpFactoryManager()->getUriFactory());
        $continue = UriHelper::getQueryParam($currentUri, 'continue');

        if (!empty($continue)) {
            return $this->httpHelper->makeRedirect($continue);
        }
        return $this->httpHelper->makeRedirect(static::getPlatformUrl());
    }

    /**
     * @param string $defaultUri
     * @return string
     */
    public function getRedir(string $defaultUri)
    {
        $uriFactory = $this->httpHelper->getHttpFactoryManager()->getUriFactory();
        $currentUri = UriHelper::getCurrent($uriFactory);
        $redirUri = UriHelper::getQueryParam($currentUri, self::QUERY_REDIR_PARAMETER);
        if (UriHelper::isValid($redirUri)) {
            return $redirUri;
        }
        return $defaultUri;
    }
}
