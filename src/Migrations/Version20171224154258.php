<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171224154258 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE code (id INT AUTO_INCREMENT NOT NULL, time DATETIME NOT NULL, expired DATETIME NOT NULL, destination VARCHAR(1024) NOT NULL, code VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, model VARCHAR(1024) NOT NULL, user INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE launch_screen (id INT AUTO_INCREMENT NOT NULL, time DATETIME NOT NULL, text VARCHAR(1024) NOT NULL, portrait_url VARCHAR(1024) NOT NULL, landscape_url VARCHAR(1024) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, time DATETIME NOT NULL, `group` INT NOT NULL, type INT NOT NULL, place INT NOT NULL, title VARCHAR(1024) NOT NULL, detail VARCHAR(1024) NOT NULL, image VARCHAR(1024) NOT NULL, url VARCHAR(1024) NOT NULL, priority INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE code');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE launch_screen');
        $this->addSql('DROP TABLE message');
    }
}
