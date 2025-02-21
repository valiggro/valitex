<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250108185817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice DROP creation_date');
        $this->addSql('ALTER TABLE einvoice DROP tax_identification_number');
        $this->addSql('ALTER TABLE einvoice DROP solicitation_id');
        $this->addSql('ALTER TABLE einvoice DROP type');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ADD creation_date BIGINT NOT NULL');
        $this->addSql('ALTER TABLE einvoice ADD tax_identification_number BIGINT NOT NULL');
        $this->addSql('ALTER TABLE einvoice ADD solicitation_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE einvoice ADD type VARCHAR(255) NOT NULL');
    }
}
