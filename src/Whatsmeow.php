<?php

namespace Shadowbane\Whatsmeow;

use Illuminate\Support\Facades\Http;
use Shadowbane\Whatsmeow\Exceptions\WhatsmeowException;

/**
 * Class Whatsmeow.
 *
 * @package Shadowbane\Whatsmeow
 */
class Whatsmeow
{
    protected string $endpoint = '';

    public function __construct(
        protected string $token = '',
        protected string $baseUrl = '',
    ) {
        if (blank($this->token)) {
            $this->setToken(config('whatsmeow.token'));
        }

        if (blank($this->baseUrl)) {
            $this->setBaseUrl(config('whatsmeow.endpoint'));
        }

        $this->setHttpMacro();
    }

    /**
     * Set the http macro for easier request.
     * The base url and token can't be changed after this,
     * so we need to check for the implementation in case
     * someone needs to change the token or base url.
     * For example, if you have different WhatsApp numbers for each role.
     *
     * @return void
     */
    final public function setHttpMacro()
    {
        $token = $this->getToken();
        $baseUrl = $this->baseUrl;

        Http::macro('whatsmeow', function () use ($token, $baseUrl) {
            $httpClient = Http::withHeaders([
                'Connection' => 'close',
                'Content-Type' => 'application/json',
            ])->baseUrl($baseUrl)->acceptJson();

            if (!blank($token)) {
                $httpClient = $httpClient->withToken($token);
            }

            if (app()->environment(['local', 'testing'])) {
                $httpClient = $httpClient->withOptions([
                    'debug' => config('whatsmeow.debug_mode') ?? false,
                ]);
            }

            return $httpClient;
        });
    }

    /**
     * Set Token.
     *
     * @param string $token
     *
     * @return static
     */
    public function setToken(string $token): static
    {
        $this->token = $token;

        if (Http::hasMacro('whatsmeow')) {
            $this->setHttpMacro();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * API Base URI setter.
     *
     * @param string $baseUrl
     *
     * @throws WhatsmeowException
     *
     * @return static
     */
    public function setBaseUrl(string $baseUrl): static
    {
        if (blank($baseUrl)) {
            throw WhatsmeowException::urlIsEmpty();
        }

        $this->baseUrl = rtrim($baseUrl, '/');

        return $this;
    }

    /**
     * API Endpoint setter.
     *
     * @param string $endpoint
     *
     * @return $this
     */
    public function setEndpoint(string $endpoint): static
    {
        $this->endpoint = '/'.rtrim(ltrim($endpoint, '/'), '/');

        return $this;
    }

    /**
     * Get API Endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Create HTTP requests to Whatsmeow API.
     *
     * @param array $params
     *
     * @throws WhatsmeowException
     *
     * @return array|null
     */
    public function sendMessage(array $params): ?array
    {
        $response = Http::whatsmeow()->post($this->getEndpoint(), [
            'data' => [$params],
        ]);

        if ($response->failed()) {
            throw new WhatsmeowException($response->getReasonPhrase());
        }

        return $response->json();
    }
}
