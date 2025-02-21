<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240514075045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE einvoice_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE einvoice_item (id INT NOT NULL, supplier_id INT NOT NULL, name_match VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, sell_price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE einvoice ADD sell_price_json TEXT NOT NULL DEFAULT \'\'');
        $this->addSql('ALTER TABLE einvoice ALTER COLUMN sell_price_json DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE einvoice_item_id_seq CASCADE');
        $this->addSql('DROP TABLE einvoice_item');
        $this->addSql('ALTER TABLE einvoice DROP sell_price_json');
    }
}
