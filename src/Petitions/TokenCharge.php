<?php

namespace Mancoide\Bancard\Petitions;

use Mancoide\Bancard\Bancard;
use Mancoide\Bancard\Models\{SingleBuy as SingleBuyModel, Confirmation as ConfirmationModel};

class TokenCharge extends Petition
{
    private SingleBuyModel $payload;
    private string $aliasToken;

    public function __construct(string $description, float $amount, string $aliasToken, string $process_id = null, bool $pre_authorization = false)
    {
        $payload = SingleBuyModel::create([
            'description' => $description, 
            'amount' => $amount, 
            'currency' => 'PYG',
            'process_id' => $process_id,
            'pre_authorization' => $pre_authorization
        ]);
        $this->payload = SingleBuyModel::find($payload->id);
        $this->aliasToken = $aliasToken;
    }

    protected function token(): string
    {
        $privateKey = Bancard::privateKey();
        $token = "{$privateKey}{$this->payload->shop_process_id}charge{$this->payload->amount}{$this->payload->currency}{$this->aliasToken}";

        return hash('md5', $token);
    }

    public function getOperationPetition(): array
    {
        $data = [
            'public_key' => Bancard::publicKey(), 
            'operation' => [
                'token' => $this->token(), 
                'shop_process_id' => $this->payload->shop_process_id, 
                'amount' => "{$this->payload->amount}", 
                'number_of_payments' => 1, 
                'currency' => $this->payload->currency, 
                'additional_data' => "{$this->payload->additional_data}",
                'description' => $this->payload->description, 
                'alias_token' => $this->aliasToken
            ]
        ];

        if ($this->payload->pre_authorization)
            $data['operation']['preauthorization'] = 'S';

        return $data;
    }

    public function handlePayload(array $data = []): void
    {
        $securityInformation = $data['security_information'];
        unset($data['security_information']);
        $confirmation = array_merge($data, $securityInformation, ['command' => 'charge']);

        ConfirmationModel::create($confirmation);
    }
}
