<?php

namespace App\Service;

use App\Model\Einvoice\EinvoiceModel;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class S3
{
    private string $bucket;

    public function __construct(
        private CacheInterface $cache,
        private ContainerBagInterface $containerBag,
        private Filesystem $filesystem,
        private S3Client $client,
    ) {
        $this->bucket = $this->containerBag->get('aws.s3_bucket');
    }

    public function uploadFile(string $fileName, string $filePath): Result
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $fileName,
            ]);
        } catch (S3Exception $e) {
            $result = $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $fileName,
                'SourceFile' => $filePath,
            ]);
        }
        if (200 != $result['@metadata']['statusCode']) {
            throw new \Exception(json_encode($result['@metadata']));
        }
        return $result;
    }

    public function uploadZip(EinvoiceModel $einvoiceModel): Result
    {
        return $this->uploadFile(
            fileName: $einvoiceModel->getZipName(),
            filePath: $einvoiceModel->getZipPath(),
        );
    }

    public function uploadPdf(EinvoiceModel $einvoiceModel): Result
    {
        return $this->uploadFile(
            fileName: $einvoiceModel->getPdfName(),
            filePath: $einvoiceModel->getPdfPath(),
        );
    }

    public function downloadFile(string $fileName, string $filePath): void
    {
        if ($this->filesystem->exists($filePath)) {
            return;
        }
        $result = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $fileName,
        ]);
        if (200 != $result['@metadata']['statusCode']) {
            throw new \Exception(json_encode($result['@metadata']));
        }
        $this->filesystem->dumpFile(
            filename: $filePath,
            content: $result['Body']
        );
    }

    public function downloadZip(EinvoiceModel $einvoiceModel): Result
    {
        return $this->uploadFile(
            fileName: $einvoiceModel->getZipName(),
            filePath: $einvoiceModel->getZipPath(),
        );
    }

    public function getPresignedUrl(string $fileName): string
    {
        return $this->cache->get(__METHOD__ . $fileName, function (ItemInterface $item) use ($fileName) {
            $item->expiresAfter(24 * 3600);

            $command = $this->client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key' => $fileName,
            ]);
            $presignedRequest = $this->client->createPresignedRequest(
                command: $command,
                expires: '+24 hours',
            );
            return $presignedRequest->getUri();
        });
    }
}
