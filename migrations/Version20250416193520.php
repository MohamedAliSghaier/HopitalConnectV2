<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416193520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP FOREIGN KEY FK_C09A9BA8C5DE8585
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C09A9BA8C5DE8585 ON rendezvous
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous DROP idpatient
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD idpatient INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendezvous ADD CONSTRAINT FK_C09A9BA8C5DE8585 FOREIGN KEY (idpatient) REFERENCES patient (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C09A9BA8C5DE8585 ON rendezvous (idpatient)
        SQL);
    }
}
