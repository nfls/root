<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180102015713 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE auth_code (token VARCHAR(255) NOT NULL, client_id INT DEFAULT NULL, redirect_uri VARCHAR(255) NOT NULL, expiry_time DATETIME NOT NULL, uid INT NOT NULL, scopes VARCHAR(1024) NOT NULL, UNIQUE INDEX UNIQ_5933D02C5F37A13B (token), UNIQUE INDEX UNIQ_5933D02C19EB6921 (client_id), PRIMARY KEY(token)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scope (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('DROP TABLE launch_screen');
        $this->addSql('ALTER TABLE access_token CHANGE expiry_time expiry_time DATETIME NOT NULL, CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6A2DD6819EB6921 ON access_token (client_id)');
        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE refresh_token DROP access_token, CHANGE expiry_time expiry_time DATETIME NOT NULL, CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F219519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74F219519EB6921 ON refresh_token (client_id)');
        $this->addSql('ALTER TABLE user CHANGE join_time join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE launch_screen (id INT AUTO_INCREMENT NOT NULL, time DATETIME NOT NULL, text VARCHAR(1024) NOT NULL COLLATE utf8_unicode_ci, portrait_url VARCHAR(1024) NOT NULL COLLATE utf8_unicode_ci, landscape_url VARCHAR(1024) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE auth_code');
        $this->addSql('DROP TABLE scope');
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD6819EB6921');
        $this->addSql('DROP INDEX UNIQ_B6A2DD6819EB6921 ON access_token');
        $this->addSql('ALTER TABLE access_token CHANGE client_id client_id VARCHAR(128) NOT NULL COLLATE utf8_unicode_ci, CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F219519EB6921');
        $this->addSql('DROP INDEX UNIQ_C74F219519EB6921 ON refresh_token');
        $this->addSql('ALTER TABLE refresh_token ADD access_token VARCHAR(128) NOT NULL COLLATE utf8_unicode_ci, CHANGE client_id client_id VARCHAR(128) NOT NULL COLLATE utf8_unicode_ci, CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE join_time join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
