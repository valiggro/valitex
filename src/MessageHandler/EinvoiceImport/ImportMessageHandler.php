<?php

namespace App\MessageHandler\EinvoiceImport;

use App\Message\EinvoiceImport\ImportMessage;
use App\Service\EinvoiceImport;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ImportMessageHandler
{
    public function __construct(
        private EinvoiceImport $einvoiceImport,
    ) {}

    public function __invoke(ImportMessage $message): void
    {
        $einvoice = $this->einvoiceImport->importMessage($message->message);
        $this->einvoiceImport->importZip($einvoice);
        $this->einvoiceImport->importXml($einvoice);
        $this->einvoiceImport->importPdf($einvoice);
    }
}
