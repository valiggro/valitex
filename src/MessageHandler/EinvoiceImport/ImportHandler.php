<?php

namespace App\MessageHandler\EinvoiceImport;

use App\Message\EinvoiceImport\Import;
use App\Message\EinvoiceImport\ImportMessage;
use App\Service\AnafEfactura;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class ImportHandler
{
    public function __construct(
        private AnafEfactura $anafEfactura,
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(Import $message): void
    {
        foreach ($this->anafEfactura->getMessages() as $m) {
            $this->messageBus->dispatch(new ImportMessage(message: $m));
        }
    }
}
