<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241202063128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $settings = $this->connection->fetchAllAssociative('SELECT * FROM setting');
        $this->addSql('DROP TABLE setting');
        $this->addSql('CREATE TABLE setting (id SERIAL NOT NULL, key VARCHAR(255) NOT NULL, value TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F74B8988A90ABA9 ON setting (key)');
        foreach ($settings as $setting) {
            $this->addSql("INSERT INTO setting (key, value) VALUES ('{$setting['id']}', '{$setting['value']}');");
        }
    }

    public function down(Schema $schema): void
    {
        $settings = $this->connection->fetchAllAssociative('SELECT * FROM setting');
        $this->addSql('DROP INDEX UNIQ_9F74B8988A90ABA9');
        $this->addSql('DROP TABLE setting');
        $this->addSql('CREATE TABLE setting (id VARCHAR(255) NOT NULL, value TEXT NOT NULL, PRIMARY KEY(id))');
        foreach ($settings as $setting) {
            $this->addSql("INSERT INTO setting (id, value) VALUES ('{$setting['key']}', '{$setting['value']}');");
        }
    }
}
