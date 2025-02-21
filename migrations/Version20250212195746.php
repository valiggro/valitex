<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250212195746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ADD message_id BIGINT');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F020B98C537A1329 ON einvoice (message_id)');

        $einvoices = $this->connection->fetchAllAssociative('SELECT id, message_json FROM einvoice ORDER BY id ASC');
        $id = 1;
        foreach ($einvoices as $einvoice) {
            $message = json_decode($einvoice['message_json']);
            $this->addSql("UPDATE einvoice SET id={$id}, message_id={$message->id} WHERE id = {$einvoice['id']};");
            $id++;
        }

        $this->addSql('ALTER TABLE einvoice ALTER message_id SET NOT NULL');

        $this->addSql('ALTER TABLE einvoice ALTER id TYPE INT');
        $this->addSql('CREATE SEQUENCE einvoice_id_seq');
        $this->addSql('SELECT setval(\'einvoice_id_seq\', (SELECT MAX(id) FROM einvoice))');
        $this->addSql('ALTER TABLE einvoice ALTER id SET DEFAULT nextval(\'einvoice_id_seq\')');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_F020B98C537A1329');
        $this->addSql('ALTER TABLE einvoice DROP message_id');

        $this->addSql('ALTER TABLE einvoice ALTER id TYPE BIGINT');
        $this->addSql('ALTER TABLE einvoice ALTER id DROP DEFAULT');

        $einvoices = $this->connection->fetchAllAssociative('SELECT id, message_json FROM einvoice ORDER BY id ASC');
        foreach ($einvoices as $einvoice) {
            $message = json_decode($einvoice['message_json']);
            $this->addSql("UPDATE einvoice SET id={$message->id} WHERE id = {$einvoice['id']};");
        }
    }
}
