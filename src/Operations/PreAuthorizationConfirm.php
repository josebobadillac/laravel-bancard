<?php

namespace Mancoide\Bancard\Operations;

use Illuminate\Http\Client\Response;
use Mancoide\Bancard\Petitions\{Petition, PreAuthorizationConfirm as PreAuthorizationConfirmPetition};

class PreAuthorizationConfirm extends Operation
{
    private static string $resource = 'vpos/api/0.3/preauthorizations/confirm';

    private string $shopProcessId;
    private float $amount;

    public function __construct(string $shopProcessId, float $amount = 0)
    {
        $this->shopProcessId = $shopProcessId;
        $this->amount = $amount;
    }

    protected static function getResource(): string
    {
        return self::$resource;
    }

    protected function getPetition(): Petition
    {
        return new PreAuthorizationConfirmPetition($this->shopProcessId, $this->amount);
    }

    protected function handleSuccess(Petition $petition, Response $response): void
    {
        $data = $response->json();
        $petition->handlePayload($data['operation']);
    }

    protected function handleError(Petition $petition, Response $response): void
    {
        $data = $response->json();
        if (!empty($data['messages']) && is_array($data['messages'])) {
            foreach ($data['messages'] as $message) {
                $petition->handlePayload([
                    'shop_process_id'               => $this->shopProcessId,
                    'response'                      => 'N',
                    'response_details'              => $message['key'],
                    'response_code'                 => 99, //Custom
                    'response_description'          => $message['level'],
                    'extended_response_description' => "{$message['dsc']}",
                    'security_information'          => []
                ]);
            }
        }
    }
}
