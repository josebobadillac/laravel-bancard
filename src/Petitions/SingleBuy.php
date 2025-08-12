<?php

namespace josebobadillac\Bancard\Petitions;

use josebobadillac\Bancard\Bancard;
use josebobadillac\Bancard\Models\SingleBuy as SingleBuyModel;

class SingleBuy extends Petition
{
    private SingleBuyModel $payload;

    public function __construct(string $description, float $amount, string $process_id = null, bool $pre_authorization = false)
    {
        $payload = SingleBuyModel::create([
            'description' => $description, 
            'amount' => $amount, 
            'currency' => 'PYG',
            'process_id' => $process_id,
            'pre_authorization' => $pre_authorization
        ]);
        $this->payload = SingleBuyModel::find($payload->id);
    }

    protected function token(): string
    {
        $privateKey = Bancard::privateKey();
        $token = "{$privateKey}{$this->payload->shop_process_id}{$this->payload->amount}{$this->payload->currency}";

        return hash('md5', $token);
    }

    public function getOperationPetition(): array
    {
        $data = [
            'public_key' => Bancard::publicKey(), 
            'operation' => [
                'token' => $this->token(), 
                'shop_process_id' => $this->payload->shop_process_id, 
                'currency' => $this->payload->currency, 
                'amount' => "{$this->payload->amount}", 
                'description' => $this->payload->description,
                'return_url' => config('bancard.single_buy_return_url'), 
                'cancel_url' => config('bancard.single_buy_cancel_url')
            ]
        ];

        if($this->payload->additional_data)
            $data['operation']['additional_data'] = $this->payload->additional_data;

        if ($this->payload->pre_authorization)
            $data['operation']['preauthorization'] = 'S';

        return $data;
    }

    public function handlePayload(array $data = []): void
    {
        $this->payload->update($data);
    }
}
