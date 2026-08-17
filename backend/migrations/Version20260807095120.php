<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260807095120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie ADD tmdb_id INT DEFAULT NULL, ADD overview LONGTEXT DEFAULT NULL, ADD poster_path VARCHAR(255) DEFAULT NULL, ADD release_date DATE DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE movie_image movie_image VARCHAR(255) DEFAULT NULL, CHANGE movie_id movie_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie DROP tmdb_id, DROP overview, DROP poster_path, DROP release_date, CHANGE movie_id movie_id VARCHAR(255) NOT NULL, CHANGE title title VARCHAR(100) NOT NULL, CHANGE movie_image movie_image VARCHAR(255) NOT NULL');
    }
}
