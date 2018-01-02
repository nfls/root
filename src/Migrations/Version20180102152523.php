<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180102152523 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE access_token (token VARCHAR(255) NOT NULL, user_id INT DEFAULT NULL, client_id INT DEFAULT NULL, expiry_time DATETIME NOT NULL, scopes VARCHAR(1024) NOT NULL, UNIQUE INDEX UNIQ_B6A2DD68A76ED395 (user_id), UNIQUE INDEX UNIQ_B6A2DD6819EB6921 (client_id), PRIMARY KEY(token)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_code (token VARCHAR(255) NOT NULL, user_id INT DEFAULT NULL, client_id INT DEFAULT NULL, redirect_uri VARCHAR(255) NOT NULL, expiry_time DATETIME NOT NULL, scopes VARCHAR(1024) NOT NULL, UNIQUE INDEX UNIQ_5933D02CA76ED395 (user_id), UNIQUE INDEX UNIQ_5933D02C19EB6921 (client_id), PRIMARY KEY(token)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(1024) NOT NULL, client_id VARCHAR(128) NOT NULL, client_secret VARCHAR(128) NOT NULL, grant_type VARCHAR(128) NOT NULL, redirect_url VARCHAR(1024) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE code (id INT AUTO_INCREMENT NOT NULL, time DATETIME NOT NULL, expired DATETIME NOT NULL, destination VARCHAR(1024) NOT NULL, code VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, model VARCHAR(1024) NOT NULL, user INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, time DATETIME NOT NULL, `group` INT NOT NULL, type INT NOT NULL, place INT NOT NULL, title VARCHAR(1024) NOT NULL, detail VARCHAR(1024) NOT NULL, image VARCHAR(1024) NOT NULL, url VARCHAR(1024) NOT NULL, priority INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (token VARCHAR(255) NOT NULL, expiry_time DATETIME NOT NULL, PRIMARY KEY(token)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scope (token VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(token)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, phone BIGINT DEFAULT NULL, token VARCHAR(255) NOT NULL, fa VARCHAR(255) DEFAULT NULL, card INT UNSIGNED DEFAULT 0 NOT NULL, permission BIGINT UNSIGNED DEFAULT 1 NOT NULL, join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649444F97DD (phone), UNIQUE INDEX UNIQ_8D93D6495F37A13B (token), UNIQUE INDEX UNIQ_8D93D64948CB8F10 (fa), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD6819EB6921');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02C19EB6921');
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD68A76ED395');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02CA76ED395');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE auth_code');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE code');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE scope');
        $this->addSql('DROP TABLE user');
    }
}
