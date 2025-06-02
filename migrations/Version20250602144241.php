<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602144241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock_mouvement (id INT AUTO_INCREMENT NOT NULL, manager_id INT NOT NULL, type VARCHAR(255) NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', comments VARCHAR(255) DEFAULT NULL, INDEX IDX_C3CC1AD6783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stock_mouvement ADD CONSTRAINT FK_C3CC1AD6783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_mouvement DROP FOREIGN KEY FK_C3CC1AD6783E3463');
        $this->addSql('DROP TABLE stock_mouvement');
    }
}
