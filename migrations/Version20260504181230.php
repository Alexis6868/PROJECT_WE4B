<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504181230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
    // 1. On crée UNIQUEMENT la table user pour la sécurité Symfony
    // On l'appelle 'user' pour ne pas percuter ta table 'utilisateur' existante
    $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

    // On ne touche à RIEN d'autre. 
    // On laisse 'id_entrepot', 'id_vehicule', etc., tels quels.
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
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
        $this->addSql('CREATE INDEX id_vehicule ON reservation (id_vehicule)');
        $this->addSql('CREATE INDEX id_user ON reservation (id_user)');
        $this->addSql('ALTER TABLE utilisateur MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE utilisateur DROP id, CHANGE id_user id_user INT AUTO_INCREMENT NOT NULL, CHANGE a2f a2f TINYINT DEFAULT 0, CHANGE role role ENUM(\'client\', \'employe\', \'admin\') NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_user)');
        $this->addSql('CREATE UNIQUE INDEX mail ON utilisateur (mail)');
        $this->addSql('ALTER TABLE vehicule MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE vehicule DROP id, CHANGE id_vehicule id_vehicule INT AUTO_INCREMENT NOT NULL, CHANGE type type VARCHAR(15) NOT NULL, CHANGE indice_maintenance indice_maintenance INT DEFAULT 100, DROP PRIMARY KEY, ADD PRIMARY KEY (id_vehicule)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT `vehicule_ibfk_1` FOREIGN KEY (id_entrepot) REFERENCES entrepot (id_entrepot) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX id_entrepot ON vehicule (id_entrepot)');
    }
}
