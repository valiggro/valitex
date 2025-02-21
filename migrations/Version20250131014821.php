<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250131014821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ALTER note_number TYPE INT');
        $this->addSql('ALTER TABLE einvoice ALTER issue_date TYPE DATE USING issue_date::date');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ALTER note_number TYPE BIGINT');
        $this->addSql('ALTER TABLE einvoice ALTER issue_date TYPE VARCHAR(255)');
    }
}
