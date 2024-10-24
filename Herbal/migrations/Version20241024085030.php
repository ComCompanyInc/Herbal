<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241024085030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content ALTER main_text TYPE TEXT');
        $this->addSql('ALTER TABLE content ALTER main_text TYPE TEXT');
        $this->addSql('ALTER TABLE content ALTER date_sending TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C9665E237E06 ON country (name)');
        $this->addSql('ALTER TABLE "user" ALTER date_of_registration TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ALTER date_of_registration TYPE DATE');
        $this->addSql('DROP INDEX UNIQ_5373C9665E237E06');
        $this->addSql('ALTER TABLE content ALTER main_text TYPE VARCHAR(8000)');
        $this->addSql('ALTER TABLE content ALTER date_sending TYPE DATE');
    }
}
