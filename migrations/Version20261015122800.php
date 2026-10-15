<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261015122800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, title, date, description, category, seats, image, valide FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, description CLOB NOT NULL, category VARCHAR(50) NOT NULL, seats INTEGER NOT NULL, image VARCHAR(255) DEFAULT NULL, validated BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO event (id, title, date, description, category, seats, image, validated) SELECT id, title, date, description, category, seats, image, valide FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, title, date, description, category, seats, image, validated FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, description CLOB NOT NULL, category VARCHAR(50) NOT NULL, seats INTEGER NOT NULL, image VARCHAR(255) DEFAULT NULL, valide BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO event (id, title, date, description, category, seats, image, valide) SELECT id, title, date, description, category, seats, image, validated FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }
}
