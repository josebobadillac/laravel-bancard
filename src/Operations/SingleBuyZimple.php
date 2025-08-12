<?php 

namespace josebobadillac\Bancard\Operations;

use Illuminate\Http\Client\Response;
use josebobadillac\Bancard\Petitions\{Petition, SingleBuyZimple as SingleBuyZimplePetition};

class SingleBuyZimple extends Operation
{
    private static string $resource = 'vpos/api/0.3/single_buy';
    
    private string $description;
    private float $amount;
    private string $phone_number;
    private string $process_id;
    private ?string $return_url;
    private ?string $cancel_url;

    public function __construct(string $description, float $amount, string $phone_number, ?string $process_id = null, ?string $return_url = null, ?string $cancel_url = null)
    {
        $this->description = $description;
        $this->amount = $amount;
        $this->phone_number = $phone_number;
        $this->process_id = $process_id;
        $this->return_url = $return_url;
        $this->cancel_url = $cancel_url;
    }

    protected static function getResource(): string
    {
        return self::$resource;
    }

    protected function getPetition(): Petition
    {
        return new SingleBuyZimplePetition($this->description, $this->amount, $this->phone_number, $this->process_id, $this->return_url, $this->cancel_url);
    }

    protected function handleSuccess(Petition $petition, Response $response): void
    {
        $petition->handlePayload($response->json());
    }
}
