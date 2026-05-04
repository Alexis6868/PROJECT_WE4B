<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504180318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE entrepot MODIFY id_entrepot INT NOT NULL');
        $this->addSql('ALTER TABLE entrepot ADD id INT AUTO_INCREMENT NOT NULL, CHANGE id_entrepot id_entrepot INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE entretien DROP FOREIGN KEY `entretien_ibfk_1`');
        $this->addSql('ALTER TABLE entretien DROP FOREIGN KEY `entretien_ibfk_2`');
        $this->addSql('DROP INDEX id_user ON entretien');
        $this->addSql('DROP INDEX id_vehicule ON entretien');
        $this->addSql('ALTER TABLE entretien MODIFY id_entretien INT NOT NULL');
        $this->addSql('ALTER TABLE entretien ADD id INT AUTO_INCREMENT NOT NULL, CHANGE id_entretien id_entretien INT NOT NULL, CHANGE type_travaux type_travaux LONGTEXT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY `reservation_ibfk_1`');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY `reservation_ibfk_2`');
        $this->addSql('DROP INDEX id_user ON reservation');
        $this->addSql('DROP INDEX id_vehicule ON reservation');
        $this->addSql('ALTER TABLE reservation MODIFY id_reservation INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD id INT AUTO_INCREMENT NOT NULL, CHANGE id_reservation id_reservation INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX mail ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur MODIFY id_user INT NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD id INT AUTO_INCREMENT NOT NULL, CHANGE id_user id_user INT NOT NULL, CHANGE a2f a2f SMALLINT DEFAULT NULL, CHANGE role role VARCHAR(255) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY `vehicule_ibfk_1`');
        $this->addSql('DROP INDEX id_entrepot ON vehicule');
        $this->addSql('ALTER TABLE vehicule MODIFY id_vehicule INT NOT NULL');
        $this->addSql('ALTER TABLE vehicule ADD id INT AUTO_INCREMENT NOT NULL, CHANGE id_vehicule id_vehicule INT NOT NULL, CHANGE type type VARCHAR(15) DEFAULT NULL, CHANGE indice_maintenance indice_maintenance INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE entrepot MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE entrepot DROP id, CHANGE id_entrepot id_entrepot INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_entrepot)');
        $this->addSql('ALTER TABLE entretien MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE entretien DROP id, CHANGE id_entretien id_entretien INT AUTO_INCREMENT NOT NULL, CHANGE type_travaux type_travaux TEXT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_entretien)');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT `entretien_ibfk_1` FOREIGN KEY (id_user) REFERENCES utilisateur (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT `entretien_ibfk_2` FOREIGN KEY (id_vehicule) REFERENCES vehicule (id_vehicule) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX id_user ON entretien (id_user)');
        $this->addSql('CREATE INDEX id_vehicule ON entretien (id_vehicule)');
        $this->addSql('ALTER TABLE reservation MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP id, CHANGE id_reservation id_reservation INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_reservation)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (id_user) REFERENCES utilisateur (id_user) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (id_vehicule) REFERENCES vehicule (id_vehicule) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX id_user ON reservation (id_user)');
        $this->addSql('CREATE INDEX id_vehicule ON reservation (id_vehicule)');
        $this->addSql('ALTER TABLE utilisateur MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE utilisateur DROP id, CHANGE id_user id_user INT AUTO_INCREMENT NOT NULL, CHANGE a2f a2f TINYINT DEFAULT 0, CHANGE role role ENUM(\'client\', \'employe\', \'admin\') NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_user)');
        $this->addSql('CREATE UNIQUE INDEX mail ON utilisateur (mail)');
        $this->addSql('ALTER TABLE vehicule MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE vehicule DROP id, CHANGE id_vehicule id_vehicule INT AUTO_INCREMENT NOT NULL, CHANGE type type VARCHAR(15) NOT NULL, CHANGE indice_maintenance indice_maintenance INT DEFAULT 100, DROP PRIMARY KEY, ADD PRIMARY KEY (id_vehicule)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT `vehicule_ibfk_1` FOREIGN KEY (id_entrepot) REFERENCES entrepot (id_entrepot) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX id_entrepot ON vehicule (id_entrepot)');
    }
}
