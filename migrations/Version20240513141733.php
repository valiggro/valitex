<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240513141733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE einvoice (id BIGINT NOT NULL, creation_date BIGINT NOT NULL, tax_identification_number BIGINT NOT NULL, solicitation_id BIGINT NOT NULL, type VARCHAR(255) NOT NULL, supplier_id BIGINT NOT NULL, message_json TEXT NOT NULL, zip BYTEA NOT NULL, zip_time BIGINT NOT NULL, xml TEXT NOT NULL, signature_xml TEXT NOT NULL, pdf BYTEA NOT NULL, pdf_time BIGINT NOT NULL, supplier_name VARCHAR(255) NOT NULL, number VARCHAR(255) NOT NULL, issue_date VARCHAR(255) NOT NULL, due_date VARCHAR(255) NOT NULL, line_extension_amount DOUBLE PRECISION NOT NULL, tax_amount DOUBLE PRECISION NOT NULL, payable_amount DOUBLE PRECISION NOT NULL, note_number BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE setting (id VARCHAR(255) NOT NULL, value TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql("INSERT INTO setting (id, value) VALUES ('anaf_oauth2_access_token', '');");
        $this->addSql("INSERT INTO setting (id, value) VALUES ('anaf_oauth2_access_token_expires', '0');");
        $this->addSql("INSERT INTO setting (id, value) VALUES ('anaf_oauth2_refresh_token', '');");
        $this->addSql("INSERT INTO setting (id, value) VALUES ('anaf_oauth2_refresh_token_expires', '0');");
        $this->addSql("INSERT INTO setting (id, value) VALUES ('note_last_number', '0');");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE einvoice');
        $this->addSql('DROP TABLE setting');
    }
}
