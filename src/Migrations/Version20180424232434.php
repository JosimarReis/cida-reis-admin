<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180424232434 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, ativo TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produto ADD caregoria_id INT NOT NULL');
        $this->addSql('ALTER TABLE produto ADD CONSTRAINT FK_5CAC49D73E89003D FOREIGN KEY (caregoria_id) REFERENCES categoria (id)');
        $this->addSql('CREATE INDEX IDX_5CAC49D73E89003D ON produto (caregoria_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE produto DROP FOREIGN KEY FK_5CAC49D73E89003D');
        $this->addSql('DROP TABLE categoria');
        $this->addSql('DROP INDEX IDX_5CAC49D73E89003D ON produto');
        $this->addSql('ALTER TABLE produto DROP caregoria_id');
    }
}
