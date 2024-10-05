<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241001160035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access (id UUID NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN access.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE content (id UUID NOT NULL, author_id UUID NOT NULL, main_text VARCHAR(8000) NOT NULL, is_delete BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FEC530A9F675F31B ON content (author_id)');
        $this->addSql('COMMENT ON COLUMN content.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN content.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE country (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN country.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE news (id UUID NOT NULL, content_id UUID NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1DD3995084A0A3ED ON news (content_id)');
        $this->addSql('COMMENT ON COLUMN news.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN news.content_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscribe (id UUID NOT NULL, author_id UUID NOT NULL, subscriber_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_68B95F3EF675F31B ON subscribe (author_id)');
        $this->addSql('CREATE INDEX IDX_68B95F3E7808B1AD ON subscribe (subscriber_id)');
        $this->addSql('COMMENT ON COLUMN subscribe.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscribe.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscribe.subscriber_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, country_id UUID NOT NULL, access_id UUID NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, patronumic VARCHAR(255) NOT NULL, date_of_birth DATE NOT NULL, date_of_registration DATE NOT NULL, is_blocked BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D93D649F92F3E70 ON "user" (country_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494FEA67CF ON "user" (access_id)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".access_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A9F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD3995084A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3EF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3E7808B1AD FOREIGN KEY (subscriber_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6494FEA67CF FOREIGN KEY (access_id) REFERENCES access (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE content DROP CONSTRAINT FK_FEC530A9F675F31B');
        $this->addSql('ALTER TABLE news DROP CONSTRAINT FK_1DD3995084A0A3ED');
        $this->addSql('ALTER TABLE subscribe DROP CONSTRAINT FK_68B95F3EF675F31B');
        $this->addSql('ALTER TABLE subscribe DROP CONSTRAINT FK_68B95F3E7808B1AD');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649F92F3E70');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6494FEA67CF');
        $this->addSql('DROP TABLE access');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE subscribe');
        $this->addSql('DROP TABLE "user"');
    }
}
