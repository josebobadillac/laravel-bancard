<?php 

namespace Mancoide\Bancard\Operations;

use Mancoide\Bancard\Bancard;
use Illuminate\Support\Facades\Http;
use Mancoide\Bancard\Petitions\Petition;
use Illuminate\Http\Client\{Response, RequestException};

abstract class Operation
{
    abstract protected static function getResource(): string;

    abstract protected function getPetition(): Petition;

    abstract protected function handleSuccess(Petition $petition, Response $response): void;

    protected function handleError(Petition $petition, Response $response): void
    {
        // do nothing
    }

    protected function getMethod(): string
    {
        return 'post';
    }

    public function makeRequest(): Response
    {
        $petition = $this->getPetition();
        $data = $petition->getOperationPetition();

        $method = $this->getMethod();
        $endpoint = self::getEndpoint();

        try {
            $response = Http::timeout(config('bancard.request_timeout'))->$method($endpoint, $data)->throw();
            $this->handleSuccess($petition, $response);
        } catch (RequestException $exception) {
            $response = $exception->response;
            $this->handleError($petition, $response);
        }

        return $response;
    }

    protected static function getEndpoint(): string
    {
        $baseUrl = Bancard::baseUrl();
        $resource = static::getResource();

        return "{$baseUrl}/{$resource}";
    }
}
