<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180503003312 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, ativo TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE imagem (id INT AUTO_INCREMENT NOT NULL, produto_id INT NOT NULL, caminho VARCHAR(255) NOT NULL, principal TINYINT(1) NOT NULL, INDEX IDX_1A108309105CFD56 (produto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produto (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, nome VARCHAR(255) NOT NULL, descricao LONGTEXT NOT NULL, preco NUMERIC(10, 2) NOT NULL, visivel TINYINT(1) NOT NULL, INDEX IDX_5CAC49D73397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE imagem ADD CONSTRAINT FK_1A108309105CFD56 FOREIGN KEY (produto_id) REFERENCES produto (id)');
        $this->addSql('ALTER TABLE produto ADD CONSTRAINT FK_5CAC49D73397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE produto DROP FOREIGN KEY FK_5CAC49D73397707A');
        $this->addSql('ALTER TABLE imagem DROP FOREIGN KEY FK_1A108309105CFD56');
        $this->addSql('DROP TABLE categoria');
        $this->addSql('DROP TABLE imagem');
        $this->addSql('DROP TABLE produto');
    }
}
