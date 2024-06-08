<?php 

namespace Mancoide\Bancard\Operations;

use Illuminate\Http\Client\Response;
use Mancoide\Bancard\Petitions\{Petition, SingleBuyZimple as SingleBuyZimplePetition};

class SingleBuyZimple extends Operation
{
    private static string $resource = 'vpos/api/0.3/single_buy';
    
    private string $description;
    private float $amount;
    private string $phone_number;
    private string $process_id;

    public function __construct(string $description, float $amount, string $phone_number, string $process_id = null)
    {
        $this->description = $description;
        $this->amount = $amount;
        $this->phone_number = $phone_number;
        $this->process_id = $process_id;
    }

    protected static function getResource(): string
    {
        return self::$resource;
    }

    protected function getPetition(): Petition
    {
        return new SingleBuyZimplePetition($this->description, $this->amount, $this->phone_number, $this->process_id);
    }

    protected function handleSuccess(Petition $petition, Response $response): void
    {
        $petition->handlePayload($response->json());
    }
}
