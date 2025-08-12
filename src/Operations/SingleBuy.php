<?php 

namespace josebobadillac\Bancard\Operations;

use Illuminate\Http\Client\Response;
use josebobadillac\Bancard\Petitions\{Petition, SingleBuy as SingleBuyPetition};

class SingleBuy extends Operation
{
    private static string $resource = 'vpos/api/0.3/single_buy';
    
    private string $description;
    private float $amount;
    private ?string $process_id;
    private ?string $return_url;
    private ?string $cancel_url;
    private bool $pre_authorization;

    public function __construct(string $description, float $amount, ?string $process_id = null, ?string $return_url = null, ?string $cancel_url = null, bool $pre_authorization = false)
    {
        $this->description = $description;
        $this->amount = $amount;
        $this->process_id = $process_id;
        $this->return_url = $return_url;
        $this->cancel_url = $cancel_url;
        $this->pre_authorization = $pre_authorization;
    }

    protected static function getResource(): string
    {
        return self::$resource;
    }

    protected function getPetition(): Petition
    {
        return new SingleBuyPetition($this->description, $this->amount, $this->process_id, $this->return_url, $this->cancel_url, $this->pre_authorization);
    }

    protected function handleSuccess(Petition $petition, Response $response): void
    {
        $petition->handlePayload($response->json());
    }
}
