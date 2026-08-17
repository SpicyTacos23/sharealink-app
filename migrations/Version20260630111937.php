<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260630111937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_processed_messages (run_id INT NOT NULL, attempt SMALLINT NOT NULL, message_type VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, dispatched_at DATETIME NOT NULL, received_at DATETIME NOT NULL, finished_at DATETIME NOT NULL, wait_time BIGINT NOT NULL, handle_time BIGINT NOT NULL, memory_usage BIGINT NOT NULL, transport VARCHAR(255) NOT NULL, tags VARCHAR(255) DEFAULT NULL, failure_type VARCHAR(255) DEFAULT NULL, failure_message LONGTEXT DEFAULT NULL, results JSON DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_processed_messages');
    }
}
