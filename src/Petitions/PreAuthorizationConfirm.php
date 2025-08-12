<?php

namespace josebobadillac\Bancard\Petitions;

use josebobadillac\Bancard\Bancard;
use josebobadillac\Bancard\Models\Confirmation as ConfirmationModel;

class PreAuthorizationConfirm extends Petition
{
    private $payload;

    public function __construct(string $shopProcessId, float $amount = 0)
    {
        $this->payload = [
            'shop_process_id' => $shopProcessId,
            'amount' => $amount
        ];
    }

    protected function token(): string
    {
        $privateKey = Bancard::privateKey();
        $token = "{$privateKey}{$this->payload['shop_process_id']}pre-authorization-confirm";

        return hash('md5', $token);
    }

    public function getOperationPetition(): array
    {
        $data = [
            'public_key' => Bancard::publicKey(),
            'operation' => [
                'token' => $this->token(),
                'shop_process_id' => $this->payload['shop_process_id']
            ]
        ];

        if (!empty($this->payload['amount']))
            $data['operation']['amount'] = $this->payload['amount'];

        return $data;
    }

    public function handlePayload(array $data = []): void
    {
        $securityInformation = $data['security_information'];
        unset($data['security_information']);
        $confirmation = array_merge($data, $securityInformation, ['command' => 'preauthorizations/confirm']);

        ConfirmationModel::create($confirmation);
    }
}
