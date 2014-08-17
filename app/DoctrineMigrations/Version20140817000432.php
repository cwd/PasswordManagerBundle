<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140817000432 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE keystore (id INT AUTO_INCREMENT NOT NULL, private LONGTEXT NOT NULL, public LONGTEXT NOT NULL, createdAt DATETIME NOT NULL, updateAt DATETIME DEFAULT NULL, userId INT NOT NULL, UNIQUE INDEX UNIQ_2475F3A564B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE keystore ADD CONSTRAINT FK_2475F3A564B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Store ADD envkey LONGTEXT NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE keystore");
        $this->addSql("ALTER TABLE Group CHANGE name name VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE Store DROP envkey");
    }
}
