<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501101919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance ADD qr_code VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ordonnance CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE medecin_id medecin_id INT NOT NULL, CHANGE patient_id patient_id INT NOT NULL, CHANGE medicaments medicaments JSON NOT NULL COMMENT '(DC2Type:json)', CHANGE date_prescription date_prescription DATETIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pharmacien CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE role role VARCHAR(20) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur (email)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance DROP qr_code
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ordonnance CHANGE id id INT NOT NULL, CHANGE medecin_id medecin_id INT DEFAULT NULL, CHANGE patient_id patient_id INT DEFAULT NULL, CHANGE medicaments medicaments LONGTEXT NOT NULL, CHANGE date_prescription date_prescription DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pharmacien CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur CHANGE id id INT NOT NULL, CHANGE role role VARCHAR(255) NOT NULL
        SQL);
    }
}
