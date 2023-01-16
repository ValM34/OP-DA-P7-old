<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230115205741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09F603EE73');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09F603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vendor ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52233F6E7927C74 ON vendor (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09F603EE73');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09F603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id)');
        $this->addSql('DROP INDEX UNIQ_F52233F6E7927C74 ON vendor');
        $this->addSql('ALTER TABLE vendor DROP roles, DROP password, CHANGE email email VARCHAR(255) NOT NULL');
    }
}
