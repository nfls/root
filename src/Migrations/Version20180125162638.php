<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180125162638 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE refresh_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE access_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE auth_code CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE join_time join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE read_time read_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE rank CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE gallery CHANGE time time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE photo CHANGE time time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE time time DATETIME NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE auth_code CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE gallery CHANGE time time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE photo CHANGE time time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE rank CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE refresh_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE read_time read_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE join_time join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
