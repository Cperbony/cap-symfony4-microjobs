<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180612182627 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dados_pessoais ADD moip_access_token VARCHAR(255) DEFAULT NULL, ADD moip_id_conta VARCHAR(255) DEFAULT NULL, CHANGE data_cadastro data_cadastro DATETIME DEFAULT NULL, CHANGE data_alteracao data_alteracao DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dados_pessoais DROP moip_access_token, DROP moip_id_conta, CHANGE data_cadastro data_cadastro DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE data_alteracao data_alteracao DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
