<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140816202002 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE `Group` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, parentId INT DEFAULT NULL, INDEX IDX_AC016BC110EE4CEE (parentId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Store (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(200) NOT NULL, username VARCHAR(200) NOT NULL, data LONGTEXT DEFAULT NULL, note LONGTEXT DEFAULT NULL, url VARCHAR(250) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, groupId INT NOT NULL, ownerId INT NOT NULL, INDEX IDX_3E967773ED8188B0 (groupId), INDEX IDX_3E967773E05EFD25 (ownerId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE UserHasStore (storeId INT NOT NULL, userId INT NOT NULL, INDEX IDX_F3ED9AEE2F738A52 (storeId), INDEX IDX_F3ED9AEE64B64DCC (userId), PRIMARY KEY(storeId, userId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE `Group` ADD CONSTRAINT FK_AC016BC110EE4CEE FOREIGN KEY (parentId) REFERENCES `Group` (id)");
        $this->addSql("ALTER TABLE Store ADD CONSTRAINT FK_3E967773ED8188B0 FOREIGN KEY (groupId) REFERENCES `Group` (id)");
        $this->addSql("ALTER TABLE Store ADD CONSTRAINT FK_3E967773E05EFD25 FOREIGN KEY (ownerId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE UserHasStore ADD CONSTRAINT FK_F3ED9AEE2F738A52 FOREIGN KEY (storeId) REFERENCES Store (id)");
        $this->addSql("ALTER TABLE UserHasStore ADD CONSTRAINT FK_F3ED9AEE64B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE User ADD fingerprint VARCHAR(255) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Group DROP FOREIGN KEY FK_AC016BC110EE4CEE");
        $this->addSql("ALTER TABLE Store DROP FOREIGN KEY FK_3E967773ED8188B0");
        $this->addSql("ALTER TABLE UserHasStore DROP FOREIGN KEY FK_F3ED9AEE2F738A52");
        $this->addSql("DROP TABLE `Group`");
        $this->addSql("DROP TABLE Store");
        $this->addSql("DROP TABLE UserHasStore");
        $this->addSql("ALTER TABLE User DROP fingerprint");
    }
}
