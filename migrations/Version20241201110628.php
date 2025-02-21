<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241201110628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE einvoice_item_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE einvoice_item_id_seq');
        $this->addSql('SELECT setval(\'einvoice_item_id_seq\', (SELECT MAX(id) FROM einvoice_item))');
        $this->addSql('ALTER TABLE einvoice_item ALTER id SET DEFAULT nextval(\'einvoice_item_id_seq\')');
        $this->addSql('ALTER TABLE einvoice_item ALTER supplier_id TYPE BIGINT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE einvoice_item_id_seq CASCADE');
        $this->addSql('ALTER TABLE einvoice_item ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE einvoice_item ALTER supplier_id TYPE INT');
        $this->addSql('CREATE SEQUENCE einvoice_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    }
}
