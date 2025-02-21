<?php

namespace App\Entity;

use App\Repository\EinvoiceItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EinvoiceItemRepository::class)]
class EinvoiceItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $supplierId = null;

    #[ORM\Column(length: 255)]
    private ?string $nameMatch = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?float $sellPrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSupplierId(): ?string
    {
        return $this->supplierId;
    }

    public function setSupplierId(string $supplierId): static
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    public function getNameMatch(): ?string
    {
        return $this->nameMatch;
    }

    public function setNameMatch(string $nameMatch): static
    {
        $this->nameMatch = $nameMatch;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSellPrice(): ?float
    {
        return $this->sellPrice;
    }

    public function setSellPrice(float $sellPrice): static
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }
}
