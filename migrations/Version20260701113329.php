<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260701113329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `show` ADD imdb_id VARCHAR(20) NOT NULL, DROP show_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_320ED90153B538EB ON `show` (imdb_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_320ED90153B538EB ON `show`');
        $this->addSql('ALTER TABLE `show` ADD show_id VARCHAR(255) DEFAULT NULL, DROP imdb_id');
    }
}
