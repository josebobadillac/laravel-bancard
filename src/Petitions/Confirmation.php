<?php

namespace Mancoide\Bancard\Petitions;

use Mancoide\Bancard\Bancard;
use Mancoide\Bancard\Models\Confirmation as ConfirmationModel;

class Confirmation extends Petition
{
    private $payload;

    public function __construct(string $shopProcessId)
    {
        $this->payload = $shopProcessId;
    }

    protected function token(): string
    {
        $privateKey = Bancard::privateKey();
        $token = "{$privateKey}{$this->payload}get_confirmation";

        return hash('md5', $token);
    }

    public function getOperationPetition(): array
    {
        return [
            'public_key' => Bancard::publicKey(), 
            'operation' => [
                'token' => $this->token(), 
                'shop_process_id' => $this->payload
            ]
        ];
    }

    public function handlePayload(array $data = []): void
    {
        $securityInformation = $data['security_information'];
        unset($data['security_information']);
        $confirmation = array_merge($data, $securityInformation, ['command' => 'single_buy/confirmations']);

        ConfirmationModel::create($confirmation);
    }
}
