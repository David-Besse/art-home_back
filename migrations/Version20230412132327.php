<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230412132327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artwork ADD exhibition_id INT NOT NULL');
        $this->addSql('ALTER TABLE artwork ADD CONSTRAINT FK_881FC5762A7D4494 FOREIGN KEY (exhibition_id) REFERENCES exhibition (id)');
        $this->addSql('CREATE INDEX IDX_881FC5762A7D4494 ON artwork (exhibition_id)');
        $this->addSql('ALTER TABLE exhibition ADD artist_id INT NOT NULL');
        $this->addSql('ALTER TABLE exhibition ADD CONSTRAINT FK_B8353389B7970CF8 FOREIGN KEY (artist_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_B8353389B7970CF8 ON exhibition (artist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artwork DROP FOREIGN KEY FK_881FC5762A7D4494');
        $this->addSql('DROP INDEX IDX_881FC5762A7D4494 ON artwork');
        $this->addSql('ALTER TABLE artwork DROP exhibition_id');
        $this->addSql('ALTER TABLE exhibition DROP FOREIGN KEY FK_B8353389B7970CF8');
        $this->addSql('DROP INDEX IDX_B8353389B7970CF8 ON exhibition');
        $this->addSql('ALTER TABLE exhibition DROP artist_id');
    }
}
