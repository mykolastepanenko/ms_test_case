<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113195314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create base tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `users` (
          id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
          email VARCHAR(180) NOT NULL,
          roles JSON NOT NULL,
          password VARCHAR(255) NOT NULL,
          trust_status TINYINT UNSIGNED NOT NULL DEFAULT 1,
          INDEX IDX_USER (trust_status, email),
          UNIQUE (email),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql('CREATE TABLE phone_numbers (
          id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
          phone_number VARCHAR(50) NOT NULL,
          user_id BIGINT UNSIGNED DEFAULT NULL,
          trust_status TINYINT UNSIGNED NOT NULL DEFAULT 1,
          INDEX IDX_PHONE_NUMBER (trust_status, user_id, phone_number),
          UNIQUE (phone_number),
          UNIQUE (user_id),
          CONSTRAINT fk_user
            FOREIGN KEY (user_id) REFERENCES users(id)
              ON DELETE CASCADE,
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql('CREATE TABLE client_ip (
          client_ip CHAR(15),
          trust_status TINYINT UNSIGNED NOT NULL DEFAULT 2,
          PRIMARY KEY(client_ip)
        ) DEFAULT CHARACTER
        SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql('CREATE TABLE `ban_log` (
          id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
          banned_id VARCHAR(255) NOT NULL,
          banned_type VARCHAR(255) NOT NULL,
          reason VARCHAR(255) NOT NULL,
          banned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          INDEX IDX_BAN_LOG (banned_id, banned_type),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `ban_log`');
        $this->addSql('DROP TABLE `client_ip`');
        $this->addSql('DROP TABLE phone_numbers');
        $this->addSql('DROP TABLE `users`');
    }
}
