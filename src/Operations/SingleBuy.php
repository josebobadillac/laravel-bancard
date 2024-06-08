<?php 

namespace Mancoide\Bancard\Operations;

use Illuminate\Http\Client\Response;
use Mancoide\Bancard\Petitions\{Petition, SingleBuy as SingleBuyPetition};

class SingleBuy extends Operation
{
    private static string $resource = 'vpos/api/0.3/single_buy';
    
    private string $description;
    private float $amount;
    private ?string $process_id;
    private bool $pre_authorization;

    public function __construct(string $description, float $amount, string $process_id = null, bool $pre_authorization = false)
    {
        $this->description = $description;
        $this->amount = $amount;
        $this->process_id = $process_id;
        $this->pre_authorization = $pre_authorization;
    }

    protected static function getResource(): string
    {
        return self::$resource;
    }

    protected function getPetition(): Petition
    {
        return new SingleBuyPetition($this->description, $this->amount, $this->process_id, $this->pre_authorization);
    }

    protected function handleSuccess(Petition $petition, Response $response): void
    {
        $petition->handlePayload($response->json());
    }
}
