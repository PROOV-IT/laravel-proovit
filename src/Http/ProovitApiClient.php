<?php

declare(strict_types=1);

namespace Proovit\LaravelProovit\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Proovit\LaravelProovit\Exceptions\ApiException;
use Proovit\LaravelProovit\Exceptions\NetworkException;
use Psr\Http\Message\ResponseInterface;

final class ProovitApiClient
{
    public function __construct(
        private readonly ClientInterface $http
    ) {}

    public function request(string $method, string $uri, array $options = []): array
    {
        return $this->decodeJsonResponse($this->send($method, $uri, $options));
    }

    public function download(string $uri, array $options = []): string
    {
        $response = $this->send('GET', $uri, $options);
        $this->assertSuccessful($response);

        return (string) $response->getBody();
    }

    public function stream(string $uri, array $options = []): ResponseInterface
    {
        $response = $this->send('GET', $uri, $options);
        $this->assertSuccessful($response);

        return $response;
    }

    private function send(string $method, string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->http->request($method, $uri, $options);
        } catch (RequestException $exception) {
            if ($exception->hasResponse()) {
                $this->throwApiException($exception->getResponse());
            }

            throw new NetworkException($exception->getMessage(), previous: $exception);
        } catch (GuzzleException $exception) {
            throw new NetworkException($exception->getMessage(), previous: $exception);
        }
    }

    private function assertSuccessful(ResponseInterface $response): void
    {
        if ($response->getStatusCode() < 400) {
            return;
        }

        $this->throwApiException($response);
    }

    private function decodeJsonResponse(ResponseInterface $response): array
    {
        if ($response->getStatusCode() >= 400) {
            $this->throwApiException($response);
        }

        $body = trim((string) $response->getBody());
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true);
        if (! is_array($decoded)) {
            throw new NetworkException(sprintf('Invalid JSON response received: %s', $body));
        }

        return $decoded;
    }

    private function throwApiException(ResponseInterface $response): never
    {
        $body = trim((string) $response->getBody());
        $decoded = $body !== '' ? json_decode($body, true) : null;
        $payload = is_array($decoded) ? $decoded : ['message' => $body];
        $message = (string) ($payload['message'] ?? $payload['error'] ?? 'Unknown API error');

        throw new ApiException(
            sprintf('API error: %d - %s', $response->getStatusCode(), $message),
            $response->getStatusCode(),
            $payload,
        );
    }
}
