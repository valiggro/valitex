<?php

namespace App\Entity;

use App\Repository\EinvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EinvoiceRepository::class)]
class Einvoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $supplierId = null;

    #[ORM\Column(type: Types::BIGINT, unique: true)]
    private ?string $messageId = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $messageJson = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $supplierName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $issueDate = null;

    #[ORM\Column(nullable: true)]
    private ?float $payableAmount = null;

    #[ORM\Column(nullable: true)]
    private ?int $noteNumber = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sellPriceJson = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $s3ZipModifiedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $s3PdfModifiedAt = null;

    public function getId(): ?string
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

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    private function setMessageId(string $messageId): static
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getMessage(): object
    {
        return json_decode($this->messageJson);
    }

    public function setMessage(object $message)
    {
        $this->setMessageId($message->id);
        $this->messageJson = json_encode($message);

        return $this;
    }

    public function getSupplierName(): ?string
    {
        return $this->supplierName;
    }

    public function setSupplierName(?string $supplierName): static
    {
        $this->supplierName = $supplierName;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function hasXml(): bool
    {
        return (bool) $this->number;
    }

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(?\DateTimeInterface $issueDate): static
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function getPayableAmount(): ?float
    {
        return $this->payableAmount;
    }

    public function setPayableAmount(?float $payableAmount): static
    {
        $this->payableAmount = $payableAmount;

        return $this;
    }

    public function getNoteNumber(): ?string
    {
        return $this->noteNumber;
    }

    public function setNoteNumber(?string $noteNumber): static
    {
        $this->noteNumber = $noteNumber;

        return $this;
    }

    public function getSellPrice()
    {
        return json_decode($this->sellPriceJson, true);
    }

    public function setSellPrice(?array $sellPrice): static
    {
        $this->sellPriceJson = json_encode($sellPrice);

        return $this;
    }

    public function getS3ZipModifiedAt(): ?\DateTimeImmutable
    {
        return $this->s3ZipModifiedAt;
    }

    public function setS3ZipModifiedAt(?\DateTimeImmutable $s3ZipModifiedAt): static
    {
        $this->s3ZipModifiedAt = $s3ZipModifiedAt;

        return $this;
    }

    public function hasZip(): bool
    {
        return (bool) $this->getS3ZipModifiedAt();
    }

    public function getS3PdfModifiedAt(): ?\DateTimeImmutable
    {
        return $this->s3PdfModifiedAt;
    }

    public function setS3PdfModifiedAt(?\DateTimeImmutable $s3PdfModifiedAt): static
    {
        $this->s3PdfModifiedAt = $s3PdfModifiedAt;

        return $this;
    }

    public function hasPdf(): bool
    {
        return (bool) $this->getS3PdfModifiedAt();
    }
}
