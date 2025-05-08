<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430150346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBFB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1ADAD7EBFB88E14F ON patient
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP utilisateur_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP FOREIGN KEY FK_C09A9BA8457F28AE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP FOREIGN KEY FK_C09A9BA8D71B6DB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD video_link VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD CONSTRAINT FK_C09A9BA8457F28AE FOREIGN KEY (medecinId) REFERENCES medecin (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD CONSTRAINT FK_C09A9BA8D71B6DB FOREIGN KEY (PatientId) REFERENCES patient (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD utilisateur_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1ADAD7EBFB88E14F ON patient (utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP FOREIGN KEY FK_C09A9BA8D71B6DB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP FOREIGN KEY FK_C09A9BA8457F28AE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP video_link
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD CONSTRAINT FK_C09A9BA8D71B6DB FOREIGN KEY (PatientId) REFERENCES patient (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD CONSTRAINT FK_C09A9BA8457F28AE FOREIGN KEY (medecinId) REFERENCES medecin (id)
        SQL);
    }
}
