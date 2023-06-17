<?php

namespace ITColima\SiitecApi;

use Fig\Http\Message\RequestMethodInterface;
use Francerz\Http\Utils\Constants\MediaTypes;
use Francerz\Http\Utils\Exceptions\ClientErrorException;
use Francerz\Http\Utils\Exceptions\ServerErrorException;
use Francerz\Http\Utils\HttpHelper;
use Francerz\Http\Utils\UriHelper;
use Francerz\PowerData\Objects;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractResource
{
    private $cliente;
    private $requiresOwnerAccessToken;
    private $requiresClientAccessToken;
    private $httpHelper;

    public function __construct(?SiitecApi $cliente = null)
    {
        $this->cliente = $cliente ?? SiitecApi::getLastInstance();
        $this->requiresClientAccessToken = true;
        $this->requiresOwnerAccessToken = false;
        $this->httpHelper = new HttpHelper($this->cliente->getHttpFactoryManager());
    }

    protected function requiresOwnerAccessToken(bool $requires = true)
    {
        $this->requiresOwnerAccessToken = $requires;
    }

    protected function requiresClientAccessToken(bool $requires = true)
    {
        $this->requiresClientAccessToken = $requires;
    }

    protected function buildRequest(
        string $method,
        string $path,
        array $params = [],
        $content = null,
        string $mediaType = MediaTypes::APPLICATION_X_WWW_FORM_URLENCODED
    ): RequestInterface {
        $uri = $this->cliente->getResourcesEndpoint();
        $uri = UriHelper::appendPath($uri, $path);
        if (!empty($params)) {
            $uri = UriHelper::withQueryParams($uri, $params);
        }
        if (isset($fragment)) {
            $uri = $uri->withFragment($fragment);
        }

        $requestFactory = $this->cliente->getHttpFactoryManager()->getRequestFactory();
        $request = $requestFactory->createRequest($method, $uri);
        if ($this->requiresClientAccessToken) {
            $request = $this->cliente->getOAuth2Client()->bindClientAccessToken($request);
        }
        if ($this->requiresOwnerAccessToken) {
            $request = $this->cliente->getOAuth2Client()->bindOwnerAccessToken($request);
        }

        if (isset($content)) {
            $request = $this->httpHelper->withContent($request, $mediaType, $content);
        }

        return $request;
    }

    protected function sendRequest(RequestInterface $request): ResponseInterface
    {
        $response = $this->cliente->getHttpClient()->sendRequest($request);
        if (HttpHelper::isClientError($response)) {
            throw new ClientErrorException(
                $request,
                $response,
                "HTTP Client error: {$response->getStatusCode()}" . print_r($response->getBody(), true)
            );
        } elseif (HttpHelper::isServerError($response)) {
            throw new ServerErrorException(
                $request,
                $response,
                "HTTP Server error: {$response->getStatusCode()}" . print_r($response->getBody(), true)
            );
        }
        return $response;
    }

    protected function protectedGet(string $path, array $params = [])
    {
        $request = $this->buildRequest(RequestMethodInterface::METHOD_GET, $path, $params);
        return $this->sendRequest($request);
    }

    protected function protectedPost(
        string $path,
        $content,
        string $mediaType = MediaTypes::APPLICATION_X_WWW_FORM_URLENCODED
    ) {
        $request = $this->buildRequest(RequestMethodInterface::METHOD_POST, $path, [], $content, $mediaType);
        return $this->sendRequest($request);
    }

    protected function protectedPut(
        string $path,
        $content,
        string $mediaType = MediaTypes::APPLICATION_X_WWW_FORM_URLENCODED
    ) {
        $request = $this->buildRequest(RequestMethodInterface::METHOD_PUT, $path, [], $content, $mediaType);
        return $this->sendRequest($request);
    }

    protected function protectedPatch(
        string $path,
        $content,
        string $mediaType = MediaTypes::APPLICATION_X_WWW_FORM_URLENCODED
    ) {
        $request = $this->buildRequest(RequestMethodInterface::METHOD_PATCH, $path, [], $content, $mediaType);
        return $this->sendRequest($request);
    }

    protected function protectedDelete(string $path)
    {
        $request = $this->buildRequest(RequestMethodInterface::METHOD_DELETE, $path);
        return $this->sendRequest($request);
    }

    protected function cast(object $obj, string $className)
    {
        return Objects::cast($obj, $className);
    }

    protected function castArray(array $data, string $className)
    {
        $objs = [];
        foreach ($data as $k => $obj) {
            $objs[$k] = Objects::cast($obj, $className);
        }
        return $objs;
    }
}
