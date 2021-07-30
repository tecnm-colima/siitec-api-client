<?php
namespace ITColima\SiitecApi;

use Fig\Http\Message\StatusCodeInterface;
use Francerz\ApiClient\AbstractClient;
use Francerz\Http\Client as HttpClient;
use Francerz\Http\HttpFactory;
use Francerz\Http\Server;
use Francerz\Http\Utils\HttpFactoryManager;
use Francerz\Http\Utils\HttpHelper;
use Francerz\Http\Utils\ServerInterface;
use Francerz\Http\Utils\UriHelper;
use Francerz\OAuth2\ScopeHelper;
use InvalidArgumentException;
use ITColima\SiitecApi\Resources\Usuario\PerfilResource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class SiitecApi extends AbstractClient
{
    private $perfil = null;

    private $httpHelper;

    private $loginHandlerUri = null;

    public function __construct()
    {
        $httpFactory = new HttpFactoryManager(new HttpFactory());
        $httpClient = new HttpClient();
        $this->httpHelper = new HttpHelper($httpFactory);
        parent::__construct($httpFactory, $httpClient);

        $this->getOAuth2Client()->setAuthorizationEndpoint(SiitecApiConstants::AUTHORIZE_ENDPOINT);
        $this->getOAuth2Client()->setTokenEndpoint(SiitecApiConstants::TOKEN_ENDPOINT);
        $this->setApiEndpoint(SiitecApiConstants::API_ENDPOINT);

        $this->setOwnerAccessTokenSessionKey('siitec.access_token');
        $this->setClientAccessTokenSessionKey('siitec.client_access_token');

        $this->loadDefaultAccessTokenHandlers();
        $this->loadDefaultClientAccessTokenHandlers();

        $this->loadOwnerAccessToken();
        $this->loadClientAccessToken();
        $this->loadConfigEnv();   
    }

    private function loadConfigEnv()
    {
        // LEGACY KEYS
        if (array_key_exists('SIITEC2_API_CLIENT_ID', $_ENV)) {
            $this->setClientId($_ENV['SIITEC2_API_CLIENT_ID']);
        }
        if (array_key_exists('SIITEC2_API_CLIENT_SECRET', $_ENV)) {
            $this->setClientSecret($_ENV['SIITEC2_API_CLIENT_SECRET']);
        }
        if (array_key_exists('SIITEC2_API_CLIENT_LOGOUT_URI', $_ENV)) {
            $this->logoutUri = $_ENV['SIITEC2_API_CLIENT_LOGOUT_URI'];
        } 
        if (array_key_exists('SIITEC2_API_AUTHORIZE_ENDPOINT', $_ENV)) {
            $this->getOAuth2Client()->setAuthorizationEndpoint($_ENV['SIITEC2_API_AUTHORIZE_ENDPOINT']);
        }
        if (array_key_exists('SIITEC2_API_TOKEN_ENDPOINT', $_ENV)) {
            $this->getOAuth2Client()->setTokenEndpoint($_ENV['SIITEC2_API_TOKEN_ENDPOINT']);
        }
        if (array_key_exists('SIITEC2_API_RESOURCE_ENDPOINT', $_ENV)) {
            $this->setApiEndpoint($_ENV['SIITEC2_API_RESOURCE_ENDPOINT']);
        }

        # REQUIRED
        if (array_key_exists('SIITEC_API_CLIENT_ID', $_ENV)) {
            $this->setClientId($_ENV['SIITEC_API_CLIENT_ID']);
        }
        if (array_key_exists('SIITEC_API_CLIENT_SECRET', $_ENV)) {
            $this->setClientSecret($_ENV['SIITEC_API_CLIENT_SECRET']);
        }

        # OPTIONAL
        if (array_key_exists('SIITEC_API_LOGIN_HANDLER_URI', $_ENV)) {
            $this->setLoginHandlerUri($_ENV['SIITEC_API_LOGIN_HANDLER_URI']);
        }
        if (array_key_exists('SIITEC_API_LOGOUT_URI', $_ENV)) {
            $this->logoutUri = $_ENV['SIITEC_API_LOGOUT_URI'];
        }

        # DEBUGGING
        if (array_key_exists('SIITEC_API_AUTHORIZE_ENDPOINT', $_ENV)) {
            $this->getOAuth2Client()->setAuthorizationEndpoint($_ENV['SIITEC_API_AUTHORIZE_ENDPOINT']);
        }
        if (array_key_exists('SIITEC_API_TOKEN_ENDPOINT', $_ENV)) {
            $this->getOAuth2Client()->setTokenEndpoint($_ENV['SIITEC_API_TOKEN_ENDPOINT']);
        }
        if (array_key_exists('SIITEC_API_RESOURCE_ENDPOINT', $_ENV)) {
            $this->setApiEndpoint($_ENV['SIITEC_API_RESOURCE_ENDPOINT']);
        }
    }

    public static function getPlatformUrl(string $url = '') : string
    {
        return SiitecApiConstants::PLATFORM_URL.'/'.ltrim($url,'/');
    }

    public static function getLogoutUrl() : string
    {
        return static::getPlatformUrl('/index.php/usuarios/logout');
    }

    public static function getPagosUrl(string $url = '') : string 
    {
        $retUrl = static::getPlatformUrl('/pagos/index.php');
        if (array_key_exists('SIITEC_API_PAGOS_URL', $_ENV)) {
            $retUrl = $_ENV['SIITEC_API_PAGOS_URL'];
        }
        $retUrl.= empty($url) ? '' : '/'.ltrim($url, '/');
        return $retUrl;
    }

    public static function getDocenciaUrl(string $url = '') : string
    {
        $retUrl = static::getPlatformUrl('/docencia/index.php');
        if (array_key_exists('SIITEC_API_DOCENCIA_URL', $_ENV)) {
            $retUrl = $_ENV['SIITEC_API_DOCENCIA_URL'];
        }
        $retUrl.= empty($url) ? '' : '/'.ltrim($url,'/');
        return $retUrl;
    }

    /**
     * Shorthand to emit PSR-7 responses.
     *
     * @param ResponseInterface $response
     * @return void
     */
    public static function emitResponse(ResponseInterface $response, ?ServerInterface $sever = null)
    {
        $server = $server ?? Server::new();
        $server->emitResponse($response);
    }

    public function redirectTo($location, int $code = StatusCodeInterface::STATUS_TEMPORARY_REDIRECT)
    {
        return $this->httpHelper->makeRedirect($location, $code);
    }

    public function siteUrl(?string $path = null)
    {
        return UriHelper::getSiteUrl($path);
    }

    public function getOAuth2Client()
    {
        return parent::getOAuth2Client();
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
        return parent::makeAuthorizeRedirUri($loginUri);
    }

    /**
     * Adds 'redir' parameter from login to login_handler.
     *
     * @param UriInterface $handlerUri
     * @return void
     */
    protected function addFollowParameters(UriInterface $handlerUri)
    {
        $uriFactory = $this->getHttpFactoryManager()->getUriFactory();
        $currentUri = UriHelper::getCurrent($uriFactory);
        return UriHelper::copyQueryParams($currentUri, $handlerUri, ['redir']);
    }

    /**
     * Retrieves OAuth 2.0 Authorization Code Uri adapted to given $scopes and $state
     *
     * @param array $scopes
     * @param string $state
     * @return UriInterface
     */
    public function getAuthCodeUri(array $scopes = [], string $state = '') : UriInterface
    {
        $loginHandlerUri = $this->addFollowParameters($this->loginHandlerUri);
        $scopes = ScopeHelper::merge($scopes, [SiitecUserScopes::GET_USUARIO_PERFIL_OWN]);
        return parent::makeRequestAuthorizationCodeUri($loginHandlerUri, $scopes, $state);
    }

    /**
     * Creates a Login Request and then is packed into a PSR-7 ResponseInterface
     * object.
     *
     * @param array $scopes OAuth2 scopes
     * @param string $state
     * @return ResponseInterface
     */
    public function getLoginRequest(array $scopes = [], string $state = '') : ResponseInterface
    {
        $authCodeUri = $this->getAuthCodeUri($scopes, $state);
        return $this->httpHelper->makeRedirect($authCodeUri);
    }

    /**
     * @deprecated v0.1.7
     * Deprecated in favor of SiitecApi::emitResponse()
     * 
     * Creates a Login request and then emits.
     *
     * @param array $scopes
     * @param string $state
     * @param ServerInterface|null $server
     * @return void
     */
    public function performLogin(array $scopes = [], string $state = '', ?ServerInterface $server = null)
    {
        $response = $this->getLoginRequest($scopes, $state);
        $this->emitResponse($response);
    }

    public function setLoginHandlerUri($uri)
    {
        if (is_string($uri)) {
            $uriFactory = $this->getHttpFactoryManager()->getUriFactory();
            $uri = $uriFactory->createUri($uri);
        }
        $this->loginHandlerUri = $uri;
    }

    public function login($handlerUri, $logoutUri, array $scopes = [], string $state = '')
    {
        $uriFactory = $this->getHttpFactoryManager()->getUriFactory();

        if (is_string($handlerUri)) {
            $handlerUri = $uriFactory->createUri($handlerUri);
        }
        if (!$handlerUri instanceof UriInterface) {
            throw new InvalidArgumentException('Invalid $handlerUri.');
        }

        if (is_string($logoutUri)) {
            $logoutUri = $uriFactory->createUri($logoutUri);
        }
        if (!$logoutUri instanceof UriInterface) {
            throw new InvalidArgumentException('Invalid $logoutUri.');
        }

        $handlerUri = $this->addFollowParameters($handlerUri);
        $scopes = ScopeHelper::merge($scopes, [SiitecUserScopes::GET_USUARIO_PERFIL_OWN]);
        $uri = parent::makeRequestAuthorizationCodeUri($handlerUri, $scopes, $state);

        $uri = UriHelper::withQueryParam($uri, 'logout', $logoutUri);
        
        return $this->httpHelper->makeRedirect($uri);
    }

    public function handleLogin(?ServerRequestInterface $request = null)
    {
        $at = parent::handleAuthorizeResponse($request);

        if (isset($at)) {
            $this->retrievePerfil();
        }

        return $at;
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
        $this->perfil = $_SESSION['siitec.perfil'] = $perfil;
    }

    private function loadPerfilFromSession()
    {
        $s2pk = 'siitec.perfil';
        if (array_key_exists($s2pk, $_SESSION) && is_object($_SESSION[$s2pk])) {
            $this->perfil = $_SESSION[$s2pk];
        }
    }

    private function unsetPerfil()
    {
        unset($this->perfil);
        unset($_SESSION['siitec.perfil']);
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
        $this->revokeOwnerAcccessToken();
    }

    /**
     * Handles direct logout.
     *
     * @return ResponseInterface
     */
    public function handleLogout() : ResponseInterface
    {
        $this->revoke();

        $currentUri = UriHelper::getCurrent($this->getHttpFactoryManager()->getUriFactory());
        $continue = UriHelper::getQueryParam($currentUri, 'continue');

        if (!empty($continue)) {
            return $this->httpHelper->makeRedirect($continue);
        }
        return $this->httpHelper->makeRedirect(static::getPlatformUrl());
    }
}