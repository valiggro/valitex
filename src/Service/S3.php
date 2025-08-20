<?php

namespace App\Service;

use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Psr\Log\LoggerInterface;
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
        private LoggerInterface $logger,
        private S3Client $client,
    ) {
        $this->bucket = $this->containerBag->get('aws.s3_bucket');
    }

    public function uploadFile(string $fileName, string $filePath): Result
    {
        try {
            $args = [
                'Bucket' => $this->bucket,
                'Key' => $fileName,
            ];
            $this->logger->info('Download file from S3', $args);
            $result = $this->client->getObject($args);
        } catch (S3Exception $e) {
            $args = [
                'Bucket' => $this->bucket,
                'Key' => $fileName,
                'SourceFile' => $filePath,
            ];
            $this->logger->info('File not found in S3, upload now', $args);
            $result = $this->client->putObject($args);
        }
        if ($result['@metadata']['statusCode'] !== 200) {
            throw new \Exception('Upload to S3 failed with statusCode=' . $result['@metadata']['statusCode']);
        }
        return $result;
    }

    public function downloadFile(string $fileName, string $filePath): void
    {
        if ($this->filesystem->exists($filePath)) {
            return;
        }
        $args = [
            'Bucket' => $this->bucket,
            'Key' => $fileName,
        ];
        $this->logger->info('Download file from S3', $args);
        $result = $this->client->getObject($args);
        if ($result['@metadata']['statusCode'] !== 200) {
            throw new \Exception('Download from S3 failed with statusCode=' . $result['@metadata']['statusCode']);
        }
        $this->logger->info('Write file to disk', [
            'filename' => $filePath,
        ]);
        $this->filesystem->dumpFile(
            filename: $filePath,
            content: $result['Body']
        );
    }

    public function getPresignedUrl(string $fileName): string
    {
        return $this->cache->get(__METHOD__ . $fileName, function (ItemInterface $item) use ($fileName) {
            $item->expiresAfter(24 * 3600);
            $args = [
                'Bucket' => $this->bucket,
                'Key' => $fileName,
            ];
            $this->logger->info('Create S3 presigned url', $args);
            $command = $this->client->getCommand('GetObject', $args);
            $presignedRequest = $this->client->createPresignedRequest(
                command: $command,
                expires: '+24 hours',
            );
            return $presignedRequest->getUri();
        });
    }
}
