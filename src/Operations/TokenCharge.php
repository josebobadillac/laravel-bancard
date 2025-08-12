<?php 

namespace josebobadillac\Bancard\Operations;

use Illuminate\Http\Client\Response;
use josebobadillac\Bancard\Petitions\{Petition, TokenCharge as TokenCargePetition};

class TokenCharge extends Operation
{
    private static string $resource = 'vpos/api/0.3/charge';

    private string $description;
    private float $amount;
    private string $aliasToken;
    private string $process_id;
    private bool $pre_authorization;

    public function __construct(string $description, float $amount, string $aliasToken, string $process_id = null, bool $pre_authorization = false)
    {
        $this->description = $description;
        $this->amount = $amount;
        $this->aliasToken = $aliasToken;
        $this->process_id = $process_id;
        $this->pre_authorization = $pre_authorization;
    }

    protected static function getResource(): string
    {
        return self::$resource;
    }

    protected function getPetition(): Petition
    {
        return new TokenCargePetition($this->description, $this->amount, $this->aliasToken, $this->process_id, $this->pre_authorization);
    }

    protected function handleSuccess(Petition $petition, Response $response): void
    {
        $data = $response->json();
        $petition->handlePayload($data['confirmation']);
    }
}
