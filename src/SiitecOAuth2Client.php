<?php

namespace ITColima\SiitecApi;

use Francerz\Http\Uri;
use Francerz\OAuth2\AccessToken;
use Francerz\OAuth2\Client\ClientAccessTokenSaverInterface;
use Francerz\OAuth2\Client\OAuth2ClientInterface;
use Francerz\OAuth2\Client\OwnerAccessTokenSaverInterface;
use Francerz\OAuth2\Client\PKCECode;
use Francerz\OAuth2\Client\PKCEManagerInterface;
use Francerz\OAuth2\Client\StateManagerInterface;
use Francerz\OAuth2\CodeChallengeMethodsEnum;
use Francerz\OAuth2\PKCEHelper;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
class SiitecOAuth2Client implements
    OAuth2ClientInterface,
    ClientAccessTokenSaverInterface,
    OwnerAccessTokenSaverInterface,
    StateManagerInterface,
    PKCEManagerInterface
{
    private const KEY_CLIENT_ACCESS_TOKEN = 'siitec.client_access_token';
    private const KEY_OWNER_ACCESS_TOKEN = 'siitec.owner_access_token';
    private const KEY_STATE = 'siitec.state';
    private const KEY_PKCE = 'siitec.pkce';

    private const ENV_CLIENT_ID = 'SIITEC_API_CLIENT_ID';
    private const ENV_CLIENT_SECRET = 'SIITEC_API_CLIENT_SECRET';
    private const ENV_AUTHORIZE_ENDPOINT = 'SIITEC_API_AUTHORIZE_ENDPOINT';
    private const ENV_TOKEN_ENDPOINT = 'SIITEC_API_TOKEN_ENDPOINT';
    private const ENV_CALLBACK_ENDPOINT = 'SIITEC_API_LOGIN_HANDLER_URI';

    private $clientId;
    private $clientSecret;
    private $authorizeEndpoint;
    private $tokenEndpoint;
    private $callbackEndpoint;

    private $keyClientAccessToken = self::KEY_CLIENT_ACCESS_TOKEN;
    private $keyOwnerAccessToken = self::KEY_OWNER_ACCESS_TOKEN;
    private $keyState = self::KEY_STATE;
    private $keyPkce = self::KEY_PKCE;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $this->authorizeEndpoint = new Uri(SiitecApiConstants::AUTHORIZE_ENDPOINT);
        $this->tokenEndpoint = new Uri(SiitecApiConstants::TOKEN_ENDPOINT);

        if (array_key_exists(self::ENV_CLIENT_ID, $_ENV)) {
            $this->clientId = $_ENV[self::ENV_CLIENT_ID];
        }
        if (array_key_exists(self::ENV_CLIENT_SECRET, $_ENV)) {
            $this->clientSecret = $_ENV[self::ENV_CLIENT_SECRET];
        }
        if (array_key_exists(self::ENV_AUTHORIZE_ENDPOINT, $_ENV)) {
            $this->authorizeEndpoint = new Uri($_ENV[self::ENV_AUTHORIZE_ENDPOINT]);
        }
        if (array_key_exists(self::ENV_TOKEN_ENDPOINT, $_ENV)) {
            $this->tokenEndpoint = new Uri($_ENV[self::ENV_TOKEN_ENDPOINT]);
        }
        if (array_key_exists(self::ENV_CALLBACK_ENDPOINT, $_ENV)) {
            $this->callbackEndpoint = new Uri($_ENV[self::ENV_CALLBACK_ENDPOINT]);
        }

        $this->keyClientAccessToken = self::KEY_CLIENT_ACCESS_TOKEN . '@' . $this->clientId;
        $this->keyOwnerAccessToken = self::KEY_OWNER_ACCESS_TOKEN . '@' . $this->clientId;
        $this->keyState = self::KEY_STATE . '@' . $this->clientId;
        $this->keyPkce = self::KEY_PKCE . '@' . $this->keyPkce;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function getAuthorizationEndpoint(): ?UriInterface
    {
        return $this->authorizeEndpoint;
    }

    public function getTokenEndpoint(): ?UriInterface
    {
        return $this->tokenEndpoint;
    }

    public function setCallbackEndpoint(?UriInterface $callbackEndpoint)
    {
        $this->callbackEndpoint = $callbackEndpoint;
    }

    public function getCallbackEndpoint(): ?UriInterface
    {
        return $this->callbackEndpoint;
    }

    public function loadClientAccessToken(): ?AccessToken
    {
        return $_SESSION[$this->keyClientAccessToken] ?? null;
    }

    public function saveClientAccessToken(AccessToken $accessToken)
    {
        $_SESSION[$this->keyClientAccessToken] = $accessToken;
    }

    public function loadOwnerAccessToken(): ?AccessToken
    {
        return $_SESSION[$this->keyOwnerAccessToken] ?? null;
    }

    public function saveOwnerAccessToken(AccessToken $accessToken)
    {
        $_SESSION[$this->keyOwnerAccessToken] = $accessToken;
    }

    public function generateState(): string
    {
        $state = PKCEHelper::generateCode(8);
        $_SESSION[$this->keyState] = $state;
        return $state;
    }

    public function getState(): ?string
    {
        return $_SESSION[$this->keyState] ?? null;
    }

    public function generatePKCECode(): PKCECode
    {
        $pkceCode = new PKCECode(
            PKCEHelper::generateCode(64),
            CodeChallengeMethodsEnum::SHA256
        );
        $_SESSION[$this->keyPkce] = $pkceCode;
        return $pkceCode;
    }

    public function getPKCECode(): ?PKCECode
    {
        return $_SESSION[$this->keyPkce] ?? null;
    }
}
