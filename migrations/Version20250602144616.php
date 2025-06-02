<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602144616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_mouvement ADD stock_id INT NOT NULL');
        $this->addSql('ALTER TABLE stock_mouvement ADD CONSTRAINT FK_C3CC1AD6DCD6110 FOREIGN KEY (stock_id) REFERENCES `stock` (id)');
        $this->addSql('CREATE INDEX IDX_C3CC1AD6DCD6110 ON stock_mouvement (stock_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_mouvement DROP FOREIGN KEY FK_C3CC1AD6DCD6110');
        $this->addSql('DROP INDEX IDX_C3CC1AD6DCD6110 ON stock_mouvement');
        $this->addSql('ALTER TABLE stock_mouvement DROP stock_id');
    }
}
