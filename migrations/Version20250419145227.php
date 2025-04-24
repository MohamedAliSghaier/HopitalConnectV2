<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419145227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE administrateur (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE analyse (id INT NOT NULL, id_patient INT DEFAULT NULL, id_medecin INT DEFAULT NULL, id_rendezvous INT DEFAULT NULL, date DATE NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_351B0C7EC4477E9B (id_patient), INDEX IDX_351B0C7EC547FAB6 (id_medecin), INDEX IDX_351B0C7E65101B7F (id_rendezvous), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE assurance (id_assurance INT NOT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, Id_PatientAs INT DEFAULT NULL, INDEX IDX_386829AEDE7FDECB (Id_PatientAs), PRIMARY KEY(id_assurance)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE avis (id INT NOT NULL, patient_id INT DEFAULT NULL, commentaire LONGTEXT NOT NULL, note INT NOT NULL, date_avis DATETIME NOT NULL, INDEX IDX_8F91ABF06B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE dossiermedicale (id INT NOT NULL, id_patient INT DEFAULT NULL, taille DOUBLE PRECISION NOT NULL, poids DOUBLE PRECISION NOT NULL, maladies VARCHAR(255) NOT NULL, antecedents_cardiovasculaires_familiaux VARCHAR(255) NOT NULL, asthmatique VARCHAR(255) NOT NULL, suivi_dentaire_regulier VARCHAR(255) NOT NULL, antecedents_chirurgicaux VARCHAR(255) NOT NULL, allergies VARCHAR(255) NOT NULL, profession VARCHAR(255) NOT NULL, niveau_de_stress VARCHAR(255) NOT NULL, qualite_de_sommeil VARCHAR(255) NOT NULL, activite_physique VARCHAR(255) NOT NULL, situation_familiale VARCHAR(255) NOT NULL, INDEX IDX_520111F2C4477E9B (id_patient), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE medecin (id INT NOT NULL, specialite VARCHAR(100) NOT NULL, num_rdv_max INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE medicament (id INT NOT NULL, pharmacien_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, stock INT NOT NULL, INDEX IDX_9A9C723ACFDB96BE (pharmacien_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE ordonnance (id INT NOT NULL, patient_id INT DEFAULT NULL, medicaments LONGTEXT NOT NULL, date_prescription DATETIME NOT NULL, instructions LONGTEXT NOT NULL, statut VARCHAR(50) NOT NULL, INDEX IDX_924B326C6B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, date_naissance DATE NOT NULL, adresse VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1ADAD7EBFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pharmacien (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reclamation (id INT NOT NULL, utilisateur_id INT DEFAULT NULL, medecin_id INT DEFAULT NULL, sujet VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_reclamation DATETIME NOT NULL, INDEX IDX_CE606404FB88E14F (utilisateur_id), INDEX IDX_CE6064044F31A84 (medecin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rendezvous (id INT NOT NULL, date DATE NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, type_consultation_id INT NOT NULL, PatientId INT DEFAULT NULL, medecinId INT DEFAULT NULL, INDEX IDX_C09A9BA8D71B6DB (PatientId), INDEX IDX_C09A9BA8457F28AE (medecinId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, role VARCHAR(20) NOT NULL, genre VARCHAR(10) DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E8BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE analyse ADD CONSTRAINT FK_351B0C7EC4477E9B FOREIGN KEY (id_patient) REFERENCES rendezvous (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE analyse ADD CONSTRAINT FK_351B0C7EC547FAB6 FOREIGN KEY (id_medecin) REFERENCES rendezvous (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE analyse ADD CONSTRAINT FK_351B0C7E65101B7F FOREIGN KEY (id_rendezvous) REFERENCES rendezvous (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance ADD CONSTRAINT FK_386829AEDE7FDECB FOREIGN KEY (Id_PatientAs) REFERENCES patient (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF06B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossiermedicale ADD CONSTRAINT FK_520111F2C4477E9B FOREIGN KEY (id_patient) REFERENCES patient (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medecin ADD CONSTRAINT FK_1BDA53C6BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medicament ADD CONSTRAINT FK_9A9C723ACFDB96BE FOREIGN KEY (pharmacien_id) REFERENCES pharmacien (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ordonnance ADD CONSTRAINT FK_924B326C6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pharmacien ADD CONSTRAINT FK_59E396F3BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064044F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD CONSTRAINT FK_C09A9BA8D71B6DB FOREIGN KEY (PatientId) REFERENCES patient (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD CONSTRAINT FK_C09A9BA8457F28AE FOREIGN KEY (medecinId) REFERENCES medecin (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E8BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE analyse DROP FOREIGN KEY FK_351B0C7EC4477E9B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE analyse DROP FOREIGN KEY FK_351B0C7EC547FAB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE analyse DROP FOREIGN KEY FK_351B0C7E65101B7F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance DROP FOREIGN KEY FK_386829AEDE7FDECB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF06B899279
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossiermedicale DROP FOREIGN KEY FK_520111F2C4477E9B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medecin DROP FOREIGN KEY FK_1BDA53C6BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medicament DROP FOREIGN KEY FK_9A9C723ACFDB96BE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ordonnance DROP FOREIGN KEY FK_924B326C6B899279
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBFB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pharmacien DROP FOREIGN KEY FK_59E396F3BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064044F31A84
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP FOREIGN KEY FK_C09A9BA8D71B6DB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP FOREIGN KEY FK_C09A9BA8457F28AE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE administrateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE analyse
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE assurance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE avis
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dossiermedicale
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE medecin
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE medicament
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ordonnance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE patient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pharmacien
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rendezvous
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
