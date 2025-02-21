<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250131010717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ADD s3_zip_modified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE einvoice ADD s3_pdf_modified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');

        $this->addSql('UPDATE einvoice SET s3_zip_modified_at = r2_zip_modified_at, s3_pdf_modified_at = r2_pdf_modified_at');

        $this->addSql('ALTER TABLE einvoice DROP r2_zip_modified_at');
        $this->addSql('ALTER TABLE einvoice DROP r2_pdf_modified_at');
        $this->addSql('COMMENT ON COLUMN einvoice.s3_zip_modified_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN einvoice.s3_pdf_modified_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE einvoice ADD r2_zip_modified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE einvoice ADD r2_pdf_modified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');

        $this->addSql('UPDATE einvoice SET r2_zip_modified_at = s3_zip_modified_at, r2_pdf_modified_at = s3_pdf_modified_at');

        $this->addSql('ALTER TABLE einvoice DROP s3_zip_modified_at');
        $this->addSql('ALTER TABLE einvoice DROP s3_pdf_modified_at');
        $this->addSql('COMMENT ON COLUMN einvoice.r2_zip_modified_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN einvoice.r2_pdf_modified_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
