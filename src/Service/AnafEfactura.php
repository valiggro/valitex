<?php

namespace App\Service;

use Anaf\Resources\Efactura as Client;
use App\Model\Einvoice\EinvoiceModel;
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
    ) {}

    public function getMessages(): array
    {
        return $this->cache->get(__METHOD__, function (ItemInterface $item) {
            $item->expiresAfter(3600);

            return $this->client->messages([
                'cif' => $this->containerBag->get('anaf.cif'),
                'zile' => 45,
            ])->messages;
        });
    }

    public function downloadZip(EinvoiceModel $einvoiceModel): void
    {
        if ($this->filesystem->exists($einvoiceModel->getZipPath())) {
            return;
        }
        $fileContract = $this->client->download([
            'id' => $einvoiceModel->getEinvoice()->getMessageId(),
        ]);
        if (!$content = $fileContract->getContent()) {
            throw new \Exception($einvoiceModel->getZipName());
        }
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
        $fileContract = $this->client->xmlToPdf(
            xml_path: $einvoiceModel->getXmlPath(),
        );
        if (!$content = $fileContract->getContent()) {
            throw new \Exception($einvoiceModel->getPdfName());
        }
        $this->filesystem->dumpFile(
            filename: $einvoiceModel->getPdfPath(),
            content: $content,
        );
    }
}
