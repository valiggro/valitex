<?php

namespace App\Service;

use Anaf\Resources\Efactura as Client;
use App\Model\Einvoice\EinvoiceModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class AnafEfactura
{
    public function __construct(
        private CacheInterface $cache,
        private Client $client,
        private ContainerBagInterface $containerBag,
        private Filesystem $filesystem,
        private LoggerInterface $logger,
    ) {}

    public function getMessages(): array
    {
        return $this->cache->get(__METHOD__, function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $parameters = [
                'cif' => $this->containerBag->get('anaf.cif'),
                'zile' => 45,
            ];
            $this->logger->info('Get messages from Anaf', $parameters);
            return $this->client->messages(parameters: $parameters)->messages;
        });
    }

    public function downloadZip(EinvoiceModel $einvoiceModel): void
    {
        if ($this->filesystem->exists($einvoiceModel->getZipPath())) {
            return;
        }

        $parameters = [
            'id' => $einvoiceModel->getEinvoice()->getMessageId(),
        ];
        $this->logger->info('Download zip from Anaf', $parameters);
        $fileContract = $this->client->download(parameters: $parameters);
        if (!$content = $fileContract->getContent()) {
            throw new \Exception('Download zip from Anaf failed for id=' . $einvoiceModel->getEinvoice()->getMessageId());
        }

        $this->logger->info('Write file to disk', [
            'filename' => $einvoiceModel->getZipPath(),
        ]);
        $this->filesystem->dumpFile(
            filename: $einvoiceModel->getZipPath(),
            content: $content,
        );
    }

    public function downloadPdf(EinvoiceModel $einvoiceModel): void
    {
        if ($this->filesystem->exists($einvoiceModel->getPdfPath())) {
            return;
        }

        $this->logger->info('Download pdf from Anaf', [
            'xml_path' => $einvoiceModel->getXmlPath(),
        ]);
        $fileContract = $this->client->xmlToPdf(
            xml_path: $einvoiceModel->getXmlPath(),
        );
        if (!$content = $fileContract->getContent()) {
            throw new \Exception('Download pdf from Anaf failed for xml_path=' . $einvoiceModel->getXmlPath());
        }

        $this->logger->info('Write file to disk', [
            'filename' => $einvoiceModel->getPdfPath(),
        ]);
        $this->filesystem->dumpFile(
            filename: $einvoiceModel->getPdfPath(),
            content: $content,
        );
    }
}
