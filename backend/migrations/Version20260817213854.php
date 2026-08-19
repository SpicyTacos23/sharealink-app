<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260817213854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE episode (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, season_number INT NOT NULL, episode_number INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, show_id_id INT DEFAULT NULL, media_file_id_id INT DEFAULT NULL, INDEX IDX_DDAA1CDA7DF5FA8B (show_id_id), INDEX IDX_DDAA1CDADFE2E0F4 (media_file_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media_file (id INT AUTO_INCREMENT NOT NULL, server VARCHAR(50) NOT NULL, quality INT NOT NULL, link VARCHAR(255) NOT NULL, iframe_link VARCHAR(255) DEFAULT NULL, media_type VARCHAR(25) NOT NULL, language VARCHAR(10) NOT NULL, user_id INT NOT NULL, mediaId INT NOT NULL, INDEX IDX_4FD8E9C3A76ED395 (user_id), INDEX IDX_4FD8E9C327D9F5AC (mediaId), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_processed_messages (run_id INT NOT NULL, attempt SMALLINT NOT NULL, message_type VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, dispatched_at DATETIME NOT NULL, received_at DATETIME NOT NULL, finished_at DATETIME NOT NULL, wait_time BIGINT NOT NULL, handle_time BIGINT NOT NULL, memory_usage BIGINT NOT NULL, transport VARCHAR(255) NOT NULL, tags VARCHAR(255) DEFAULT NULL, failure_type VARCHAR(255) DEFAULT NULL, failure_message LONGTEXT DEFAULT NULL, results JSON DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, movie_id VARCHAR(255) DEFAULT NULL, tmdb_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, overview LONGTEXT DEFAULT NULL, poster_path VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, movie_image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, uploaded_by_id INT DEFAULT NULL, INDEX IDX_1D5EF26FA2B28FE8 (uploaded_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE page_visit (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(512) NOT NULL, visited_at DATETIME NOT NULL, ip VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(1024) DEFAULT NULL, referer VARCHAR(1024) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `show` (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, release_year INT DEFAULT NULL, movie_id VARCHAR(255) DEFAULT NULL, tmdb_id INT DEFAULT NULL, poster_path VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, imdb_id VARCHAR(20) DEFAULT NULL, show_image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_320ED90153B538EB (imdb_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(50) NOT NULL, username VARCHAR(100) NOT NULL, avatar VARCHAR(16) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_UUID (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE watch_status (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id_id INT DEFAULT NULL, movie_id_id INT DEFAULT NULL, show_id_id INT DEFAULT NULL, episode_id_id INT DEFAULT NULL, INDEX IDX_3C4099809D86650F (user_id_id), INDEX IDX_3C40998010684CB (movie_id_id), INDEX IDX_3C4099807DF5FA8B (show_id_id), INDEX IDX_3C409980444E6803 (episode_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDA7DF5FA8B FOREIGN KEY (show_id_id) REFERENCES `show` (id)');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDADFE2E0F4 FOREIGN KEY (media_file_id_id) REFERENCES media_file (id)');
        $this->addSql('ALTER TABLE media_file ADD CONSTRAINT FK_4FD8E9C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE media_file ADD CONSTRAINT FK_4FD8E9C327D9F5AC FOREIGN KEY (mediaId) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE movie ADD CONSTRAINT FK_1D5EF26FA2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE watch_status ADD CONSTRAINT FK_3C4099809D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE watch_status ADD CONSTRAINT FK_3C40998010684CB FOREIGN KEY (movie_id_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE watch_status ADD CONSTRAINT FK_3C4099807DF5FA8B FOREIGN KEY (show_id_id) REFERENCES `show` (id)');
        $this->addSql('ALTER TABLE watch_status ADD CONSTRAINT FK_3C409980444E6803 FOREIGN KEY (episode_id_id) REFERENCES episode (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDA7DF5FA8B');
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDADFE2E0F4');
        $this->addSql('ALTER TABLE media_file DROP FOREIGN KEY FK_4FD8E9C3A76ED395');
        $this->addSql('ALTER TABLE media_file DROP FOREIGN KEY FK_4FD8E9C327D9F5AC');
        $this->addSql('ALTER TABLE movie DROP FOREIGN KEY FK_1D5EF26FA2B28FE8');
        $this->addSql('ALTER TABLE watch_status DROP FOREIGN KEY FK_3C4099809D86650F');
        $this->addSql('ALTER TABLE watch_status DROP FOREIGN KEY FK_3C40998010684CB');
        $this->addSql('ALTER TABLE watch_status DROP FOREIGN KEY FK_3C4099807DF5FA8B');
        $this->addSql('ALTER TABLE watch_status DROP FOREIGN KEY FK_3C409980444E6803');
        $this->addSql('DROP TABLE episode');
        $this->addSql('DROP TABLE media_file');
        $this->addSql('DROP TABLE messenger_processed_messages');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE page_visit');
        $this->addSql('DROP TABLE `show`');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE watch_status');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
