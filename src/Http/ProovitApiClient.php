<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Proovit\LaravelProovit\Exceptions\ApiException;
use Proovit\LaravelProovit\Exceptions\NetworkException;

final class ProovitApiClient
{
    public function __construct(
        private readonly ClientInterface $http
    ) {
    }

    public function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->http->request($method, $uri, $options);
        } catch (GuzzleException $exception) {
            throw new NetworkException($exception->getMessage(), previous: $exception);
        }

        return $this->decodeJsonResponse($response);
    }

    public function download(string $uri, array $options = []): string
    {
        try {
            $response = $this->http->request('GET', $uri, $options);
        } catch (GuzzleException $exception) {
            throw new NetworkException($exception->getMessage(), previous: $exception);
        }

        return (string) $response->getBody();
    }

    public function stream(string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->http->request('GET', $uri, $options);
        } catch (GuzzleException $exception) {
            throw new NetworkException($exception->getMessage(), previous: $exception);
        }
    }

    private function decodeJsonResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();
        $decoded = json_decode($body, true);

        if (! is_array($decoded)) {
            throw new NetworkException(sprintf('Invalid JSON response received: %s', $body));
        }

        if ($response->getStatusCode() >= 400) {
            $message = (string) ($decoded['message'] ?? 'Unknown API error');

            throw new ApiException(
                sprintf('API error: %d - %s', $response->getStatusCode(), $message),
                $response->getStatusCode(),
                $decoded
            );
        }

        return $decoded;
    }
}
