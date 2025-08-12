<?php 

namespace josebobadillac\Bancard\Petitions;

abstract class Petition
{
    abstract protected function token(): string;

    abstract public function getOperationPetition(): array;

    public function handlePayload(array $data = []): void
    {
        //
    }
}