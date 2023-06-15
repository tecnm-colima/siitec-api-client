<?php

namespace ITColima\SiitecApi;

use Exception;
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
use RuntimeException;

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

    private $clientId;
    private $clientSecret;
    private $authorizeEndpoint;
    private $tokenEndpoint;
    private $callbackEndpoint;

    private $keyClientAccessToken = self::KEY_CLIENT_ACCESS_TOKEN;
    private $keyOwnerAccessToken = self::KEY_OWNER_ACCESS_TOKEN;
    private $keyState = self::KEY_STATE;
    private $keyPkce = self::KEY_PKCE;

    private $pathClientAccessToken = null;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $authorizeEndpoint = SiitecApi::DEFAULT_AUTHORIZE_ENDPOINT;
        $tokenEndpoint = SiitecApi::DEFAULT_TOKEN_ENDPOINT;

        if (array_key_exists(SiitecApi::ENV_CLIENT_ID, $_ENV)) {
            $this->clientId = $_ENV[SiitecApi::ENV_CLIENT_ID];
        }
        if (array_key_exists(SiitecApi::ENV_CLIENT_SECRET, $_ENV)) {
            $this->clientSecret = $_ENV[SiitecApi::ENV_CLIENT_SECRET];
        }

        if (array_key_exists(SiitecApi::ENV_SIITEC_HOME, $_ENV)) {
            $authorizeEndpoint = SiitecApi::getHomeUrl('/oauth2/authorize', true);
            $tokenEndpoint = SiitecApi::getHomeUrl('/oauth2/token', true);
        }

        if (array_key_exists(SiitecApi::ENV_ENDPOINT_AUTHORIZE, $_ENV)) {
            $authorizeEndpoint = $_ENV[SiitecApi::ENV_ENDPOINT_AUTHORIZE];
        }
        if (array_key_exists(SiitecApi::ENV_ENDPOINT_TOKEN, $_ENV)) {
            $tokenEndpoint = $_ENV[SiitecApi::ENV_ENDPOINT_TOKEN];
        }

        $this->authorizeEndpoint = new Uri($authorizeEndpoint);
        $this->tokenEndpoint = new Uri($tokenEndpoint);

        $this->keyClientAccessToken = self::KEY_CLIENT_ACCESS_TOKEN . '@' . $this->clientId;
        $this->keyOwnerAccessToken = self::KEY_OWNER_ACCESS_TOKEN . '@' . $this->clientId;
        $this->keyState = self::KEY_STATE . '@' . $this->clientId;
        $this->keyPkce = self::KEY_PKCE . '@' . $this->keyPkce;

        $this->pathClientAccessToken = dirname(__FILE__, 2) . "/.oauth2/access_token_{$this->clientId}.json";
    }

    private function saveClientAccessTokenToFile(AccessToken $accessToken)
    {
        $filepath = $this->pathClientAccessToken;
        $filedir = dirname($filepath);
        if (!file_exists($filedir)) {
            mkdir($filedir, 0777, true);
        }
        $file = fopen($filepath, 'w');
        if ($file === false) {
            $error = error_get_last();
            throw new RuntimeException(
                is_array($error) && isset($error['message']) ?
                $error['message'] :
                'Failed to open Siitec API Client Access Token file.'
            );
        }
        $written = fwrite($file, serialize($accessToken));
        if ($written === false) {
            $error = error_get_last();
            throw new RuntimeException(
                is_array($error) && isset($error['message']) ?
                $error['message'] :
                'Failed to write Siitec API Client Access Token file.'
            );
        }
        fclose($file);
    }

    private function loadClientAccessTokenFromFile(): ?AccessToken
    {
        $filepath = $this->pathClientAccessToken;
        if (!file_exists($filepath)) {
            return null;
        }
        $serializedToken = \file_get_contents($filepath);
        try {
            $token = @unserialize($serializedToken);
            return $token === false ? null : $token;
        } catch (Exception $ex) {
            return null;
        }
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
        /** @var AccessToken|null */
        $accessToken = $_SESSION[$this->keyClientAccessToken] ?? null;
        if (!isset($accessToken) || isset($accessToken) && $accessToken->isExpired()) {
            $_SESSION[$this->keyClientAccessToken] = $this->loadClientAccessTokenFromFile();
        }
        return $_SESSION[$this->keyClientAccessToken];
    }

    public function saveClientAccessToken(AccessToken $accessToken)
    {
        $_SESSION[$this->keyClientAccessToken] = $accessToken;
        try {
            $this->saveClientAccessTokenToFile($accessToken);
        } catch (Exception $ex) {
        }
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
