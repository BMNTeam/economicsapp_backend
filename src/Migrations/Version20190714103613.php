<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190714103613 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE culture_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stat_info (id INT AUTO_INCREMENT NOT NULL, year_id INT NOT NULL, municipalities_id INT DEFAULT NULL, farm_category_id INT NOT NULL, culture_id INT NOT NULL, stat_type_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_EAC1CA0340C1FEA7 (year_id), INDEX IDX_EAC1CA03B5FF1302 (municipalities_id), INDEX IDX_EAC1CA0385E1586 (farm_category_id), INDEX IDX_EAC1CA03B108249D (culture_id), INDEX IDX_EAC1CA0321B6FB0A (stat_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE year (id INT AUTO_INCREMENT NOT NULL, name INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stat_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, unit VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE municipality (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE farm_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE culture (id INT AUTO_INCREMENT NOT NULL, culture_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_B6A99CEBE642F503 (culture_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stat_info ADD CONSTRAINT FK_EAC1CA0340C1FEA7 FOREIGN KEY (year_id) REFERENCES year (id)');
        $this->addSql('ALTER TABLE stat_info ADD CONSTRAINT FK_EAC1CA03B5FF1302 FOREIGN KEY (municipalities_id) REFERENCES municipality (id)');
        $this->addSql('ALTER TABLE stat_info ADD CONSTRAINT FK_EAC1CA0385E1586 FOREIGN KEY (farm_category_id) REFERENCES farm_category (id)');
        $this->addSql('ALTER TABLE stat_info ADD CONSTRAINT FK_EAC1CA03B108249D FOREIGN KEY (culture_id) REFERENCES culture (id)');
        $this->addSql('ALTER TABLE stat_info ADD CONSTRAINT FK_EAC1CA0321B6FB0A FOREIGN KEY (stat_type_id) REFERENCES stat_type (id)');
        $this->addSql('ALTER TABLE culture ADD CONSTRAINT FK_B6A99CEBE642F503 FOREIGN KEY (culture_type_id) REFERENCES culture_type (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE culture DROP FOREIGN KEY FK_B6A99CEBE642F503');
        $this->addSql('ALTER TABLE stat_info DROP FOREIGN KEY FK_EAC1CA0340C1FEA7');
        $this->addSql('ALTER TABLE stat_info DROP FOREIGN KEY FK_EAC1CA0321B6FB0A');
        $this->addSql('ALTER TABLE stat_info DROP FOREIGN KEY FK_EAC1CA03B5FF1302');
        $this->addSql('ALTER TABLE stat_info DROP FOREIGN KEY FK_EAC1CA0385E1586');
        $this->addSql('ALTER TABLE stat_info DROP FOREIGN KEY FK_EAC1CA03B108249D');
        $this->addSql('DROP TABLE culture_type');
        $this->addSql('DROP TABLE stat_info');
        $this->addSql('DROP TABLE year');
        $this->addSql('DROP TABLE stat_type');
        $this->addSql('DROP TABLE municipality');
        $this->addSql('DROP TABLE farm_category');
        $this->addSql('DROP TABLE culture');
    }
}
