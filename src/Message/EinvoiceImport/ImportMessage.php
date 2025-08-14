<?php

namespace App\Message\EinvoiceImport;

use Anaf\Responses\Efactura\Message;

final class ImportMessage
{
    public function __construct(
        public readonly Message $message,
    ) {}
}
