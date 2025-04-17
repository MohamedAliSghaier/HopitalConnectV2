<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416195131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP FOREIGN KEY FK_rendezvous_patient
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP FOREIGN KEY FK_rendezvous_medecin
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX FK_rendezvous_patient ON rendezvous
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX FK_rendezvous_medecin ON rendezvous
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP PatientId, DROP medecinId
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD PatientId INT NOT NULL, ADD medecinId INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD CONSTRAINT FK_rendezvous_patient FOREIGN KEY (PatientId) REFERENCES patient (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD CONSTRAINT FK_rendezvous_medecin FOREIGN KEY (medecinId) REFERENCES medecin (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX FK_rendezvous_patient ON rendezvous (PatientId)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX FK_rendezvous_medecin ON rendezvous (medecinId)
        SQL);
    }
}
