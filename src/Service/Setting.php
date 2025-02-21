<?php

namespace App\Service;

use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class Setting
{
    public function __construct(
        private CacheInterface $cache,
        private EntityManagerInterface $entityManager,
        private SettingRepository $settingRepository,
    ) {}

    private function getCacheKey(): string
    {
        return __METHOD__;
    }

    public function getAll(): array
    {
        return $this->cache->get($this->getCacheKey(),  function (): array {
            $values = [];
            foreach ($this->settingRepository->findAll() as $setting) {
                $values[$setting->getKey()] = $setting->getValue();
            }
            return $values;
        });
    }

    public function setMultiple(array $values): void
    {
        foreach ($values as $key => $value) {
            $setting = $this->settingRepository->findOneBy(['key' => $key]);
            $setting->setValue($value);
            $this->entityManager->persist($setting);
        }
        $this->entityManager->flush();
        $this->cache->delete($this->getCacheKey());
    }

    public function get(string $key): string
    {
        return $this->getAll()[$key];
    }

    public function set(string $key, int|string $value): void
    {
        $this->setMultiple([$key => $value]);
    }
}
