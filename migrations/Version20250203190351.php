<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203190351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recipe (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            ingredient LONGTEXT NOT NULL,
            description LONGTEXT NOT NULL,
            `portion` SMALLINT NOT NULL,
            `time_prepa` VARCHAR(12) NOT NULL,
            `time_cook` VARCHAR(12) NOT NULL,
            image VARCHAR(255) DEFAULT NULL,
            INDEX IDX_DA88B137A76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137A76ED395');
        $this->addSql('DROP TABLE recipe');
    }
}
