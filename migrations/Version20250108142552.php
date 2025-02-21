<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250108142552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice DROP zip');
        $this->addSql('ALTER TABLE einvoice DROP zip_time');
        $this->addSql('ALTER TABLE einvoice DROP xml');
        $this->addSql('ALTER TABLE einvoice DROP signature_xml');
        $this->addSql('ALTER TABLE einvoice DROP pdf');
        $this->addSql('ALTER TABLE einvoice DROP pdf_time');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ADD zip BYTEA DEFAULT NULL');
        $this->addSql('ALTER TABLE einvoice ADD zip_time BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE einvoice ADD xml TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE einvoice ADD signature_xml TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE einvoice ADD pdf BYTEA DEFAULT NULL');
        $this->addSql('ALTER TABLE einvoice ADD pdf_time BIGINT DEFAULT NULL');
    }
}
