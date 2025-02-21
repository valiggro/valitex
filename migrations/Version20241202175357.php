<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241202175357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice DROP due_date');
        $this->addSql('ALTER TABLE einvoice DROP line_extension_amount');
        $this->addSql('ALTER TABLE einvoice DROP tax_amount');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ADD due_date VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE einvoice ADD line_extension_amount DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE einvoice ADD tax_amount DOUBLE PRECISION DEFAULT NULL');
    }
}
