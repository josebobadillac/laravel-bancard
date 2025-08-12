<?php

namespace josebobadillac\Bancard\Operations;

use josebobadillac\Bancard\Models\Confirmation as ConfirmationModel;

class WebhookConfirm
{
    public function parseRequest(): ?array
    {
        $content = file_get_contents('php://input');
        if ($content != null) {
            $data = json_decode($content, true);
            if ($data) {
                $this->handlePayload($data['operation']);
                return $data;
            }
        }

        return null;
    }

    protected function handlePayload(array $data = []): void
    {
        $securityInformation = $data['security_information'];
        unset($data['security_information']);
        $confirmation = array_merge($data, $securityInformation, ['command' => 'webhook/confirm']);

        ConfirmationModel::create($confirmation);
    }
}
