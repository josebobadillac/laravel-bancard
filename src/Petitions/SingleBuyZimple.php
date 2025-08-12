<?php

namespace josebobadillac\Bancard\Petitions;

use josebobadillac\Bancard\Bancard;
use josebobadillac\Bancard\Models\SingleBuy as SingleBuyModel;

class SingleBuyZimple extends Petition
{
    private SingleBuyModel $payload;

    public function __construct(string $description, float $amount, string $phone_number, ?string $process_id = null, ?string $return_url = null, ?string $cancel_url = null)
    {
        $payload = SingleBuyModel::create([
            'description' => $description, 
            'amount' => $amount, 
            'currency' => 'PYG',
            'additional_data' => $phone_number,
            'process_id' => $process_id,
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
        ]);
        $this->payload = SingleBuyModel::find($payload->id);
    }

    protected function token(): string
    {
        $privateKey = Bancard::privateKey();
        $token = "{$privateKey}{$this->payload->process_id}{$this->payload->amount}{$this->payload->currency}";

        return hash('md5', $token);
    }

    public function getOperationPetition(): array
    {
        return [
            'public_key' => Bancard::publicKey(), 
            'operation' => [
                'token' => $this->token(), 
                'shop_process_id' => $this->payload->process_id, 
                'currency' => $this->payload->currency, 
                'amount' => "{$this->payload->amount}", 
                'additional_data' => $this->payload->additional_data, 
                'description' => $this->payload->description, 
                'return_url' => $this->payload->return_url,
                'cancel_url' => $this->payload->cancel_url,
                'zimple' => 'S'
            ]
        ];
    }

    public function handlePayload(array $data = []): void
    {
        $this->payload->update($data);
    }
}
