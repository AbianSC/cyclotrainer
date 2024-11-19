<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241118164457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BA6CCCFC9');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BA6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workout (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BA6CCCFC9');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BA6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workout (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
