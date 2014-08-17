<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140816192217 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Role (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(100) NOT NULL, name VARCHAR(150) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE UserHasRole (roleId INT NOT NULL, userId INT NOT NULL, INDEX IDX_5BB02BA1B8C2FD88 (roleId), INDEX IDX_5BB02BA164B64DCC (userId), PRIMARY KEY(roleId, userId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE UserHasRole ADD CONSTRAINT FK_5BB02BA1B8C2FD88 FOREIGN KEY (roleId) REFERENCES Role (id)");
        $this->addSql("ALTER TABLE UserHasRole ADD CONSTRAINT FK_5BB02BA164B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE UserHasRole DROP FOREIGN KEY FK_5BB02BA1B8C2FD88");
        $this->addSql("DROP TABLE Role");
        $this->addSql("DROP TABLE UserHasRole");
    }
}
