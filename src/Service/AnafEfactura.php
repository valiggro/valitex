<?php

namespace App\Service;

use Anaf\Resources\Efactura as Client;
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

    public function downloadZip(int $id, string $zipPath): void
    {
        if ($this->filesystem->exists($zipPath)) {
            return;
        }

        $parameters = [
            'id' => $id,
        ];
        $this->logger->info('Download zip from Anaf', $parameters);
        $fileContract = $this->client->download(parameters: $parameters);
        if (!$content = $fileContract->getContent()) {
            throw new \Exception("Download zip from Anaf failed for id={$id}");
        }

        $this->logger->info('Write zip to disk', [
            'zipPath' => $zipPath,
        ]);
        $this->filesystem->dumpFile(
            filename: $zipPath,
            content: $content,
        );
    }

    public function downloadPdf(string $xmlPath, string $pdfPath): void
    {
        if ($this->filesystem->exists($pdfPath)) {
            return;
        }

        $this->logger->info('Download pdf from Anaf', [
            'xml_path' => $xmlPath,
        ]);
        $fileContract = $this->client->xmlToPdf(
            xml_path: $xmlPath,
        );
        if (!$content = $fileContract->getContent()) {
            throw new \Exception("Download pdf from Anaf failed for xml_path={$xmlPath}");
        }

        $this->logger->info('Write pdf to disk', [
            'pdfPath' => $pdfPath,
        ]);
        $this->filesystem->dumpFile(
            filename: $pdfPath,
            content: $content,
        );
    }
}
