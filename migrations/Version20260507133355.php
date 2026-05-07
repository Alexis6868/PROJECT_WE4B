<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260507133355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entrepot (id INT AUTO_INCREMENT NOT NULL, adresse VARCHAR(255) NOT NULL, capacite INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, prix NUMERIC(10, 2) NOT NULL, id_user_id INT NOT NULL, id_vehicule_id INT NOT NULL, INDEX IDX_42C8495579F37AE5 (id_user_id), INDEX IDX_42C849555258F8E6 (id_vehicule_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, type VARCHAR(50) NOT NULL, image VARCHAR(1024) NOT NULL, etat VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, masse INT NOT NULL, indice_maintenance INT NOT NULL, entrepot_id INT DEFAULT NULL, INDEX IDX_292FFF1D72831E97 (entrepot_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495579F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849555258F8E6 FOREIGN KEY (id_vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D72831E97 FOREIGN KEY (entrepot_id) REFERENCES entrepot (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495579F37AE5');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849555258F8E6');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D72831E97');
        $this->addSql('DROP TABLE entrepot');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE vehicule');
    }
}
