<?php

namespace App\Factory;

use Anaf\Resources\Efactura;
use App\Service\Setting;

class EfacturaFactory
{
    public function __construct(
        private Setting $setting,
    ) {}

    public function __invoke(): Efactura
    {
        if (time() > $this->setting->get('anaf_oauth2_access_token_expires')) {
            throw new \Exception;
        }
        $client = \Anaf::authorizedClient(
            apiKey: $this->setting->get('anaf_oauth2_access_token'),
        );
        return $client->efactura();
    }
}
