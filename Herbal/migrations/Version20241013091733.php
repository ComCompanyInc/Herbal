<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241013091733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_news (id UUID NOT NULL, content_id UUID NOT NULL, news_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D0B1749584A0A3ED ON content_news (content_id)');
        $this->addSql('CREATE INDEX IDX_D0B17495B5A459A0 ON content_news (news_id)');
        $this->addSql('COMMENT ON COLUMN content_news.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN content_news.content_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN content_news.news_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE content_news ADD CONSTRAINT FK_D0B1749584A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_news ADD CONSTRAINT FK_D0B17495B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE content_news DROP CONSTRAINT FK_D0B1749584A0A3ED');
        $this->addSql('ALTER TABLE content_news DROP CONSTRAINT FK_D0B17495B5A459A0');
        $this->addSql('DROP TABLE content_news');
    }
}
