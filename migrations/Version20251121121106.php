<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251121121106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, street VARCHAR(255) NOT NULL, number INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE author (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE file (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, filename VARCHAR(255) NOT NULL, size INTEGER NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, CONSTRAINT FK_8C9F3610F675F31B FOREIGN KEY (author_id) REFERENCES author (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8C9F3610F675F31B ON file (author_id)');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, file VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_1D5EF26F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_1D5EF26F7E3C61F9 ON movie (owner_id)');
        $this->addSql('CREATE TABLE pdf (id INTEGER NOT NULL, pages_number INTEGER NOT NULL, orientation VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_EF0DB8CBF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE TABLE security_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52825A88E7927C74 ON security_user (email)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, address_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_8D93D649F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F5B7AF75 ON "user" (address_id)');
        $this->addSql('CREATE TABLE user_user (user_source INTEGER NOT NULL, user_target INTEGER NOT NULL, PRIMARY KEY(user_source, user_target), CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F7129A803AD8644E ON user_user (user_source)');
        $this->addSql('CREATE INDEX IDX_F7129A80233D34C1 ON user_user (user_target)');
        $this->addSql('CREATE TABLE video (id INTEGER NOT NULL, security_user_id INTEGER DEFAULT NULL, format VARCHAR(255) NOT NULL, duration INTEGER NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_7CC7DA2CCA85D888 FOREIGN KEY (security_user_id) REFERENCES security_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7CC7DA2CBF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2CCA85D888 ON video (security_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE pdf');
        $this->addSql('DROP TABLE security_user');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP TABLE video');
    }
}
