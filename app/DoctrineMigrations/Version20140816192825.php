<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140816192825 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Role ADD parentId INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Role ADD CONSTRAINT FK_F75B255410EE4CEE FOREIGN KEY (parentId) REFERENCES Role (id)");
        $this->addSql("CREATE INDEX IDX_F75B255410EE4CEE ON Role (parentId)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Role DROP FOREIGN KEY FK_F75B255410EE4CEE");
        $this->addSql("DROP INDEX IDX_F75B255410EE4CEE ON Role");
        $this->addSql("ALTER TABLE Role DROP parentId");
    }
}
