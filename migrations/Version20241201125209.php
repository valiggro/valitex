<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241201125209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ALTER zip DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER zip_time DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER xml DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER signature_xml DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER pdf DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER pdf_time DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER supplier_name DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER number DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER issue_date DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER due_date DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER line_extension_amount DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER tax_amount DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER payable_amount DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER note_number DROP NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER sell_price_json DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ALTER zip SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER zip_time SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER xml SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER signature_xml SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER pdf SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER pdf_time SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER supplier_name SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER number SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER issue_date SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER due_date SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER line_extension_amount SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER tax_amount SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER payable_amount SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER note_number SET NOT NULL');
        $this->addSql('ALTER TABLE einvoice ALTER sell_price_json SET NOT NULL');
    }
}
