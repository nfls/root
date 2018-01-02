<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180102164721 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_token DROP INDEX UNIQ_B6A2DD68A76ED395, ADD INDEX IDX_B6A2DD68A76ED395 (user_id)');
        $this->addSql('ALTER TABLE access_token DROP INDEX UNIQ_B6A2DD6819EB6921, ADD INDEX IDX_B6A2DD6819EB6921 (client_id)');
        $this->addSql('ALTER TABLE access_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE auth_code DROP INDEX UNIQ_5933D02CA76ED395, ADD INDEX IDX_5933D02CA76ED395 (user_id)');
        $this->addSql('ALTER TABLE auth_code DROP INDEX UNIQ_5933D02C19EB6921, ADD INDEX IDX_5933D02C19EB6921 (client_id)');
        $this->addSql('ALTER TABLE auth_code CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE refresh_token ADD accessToken VARCHAR(255) DEFAULT NULL, CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195350A9822 FOREIGN KEY (accessToken) REFERENCES access_token (token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74F2195350A9822 ON refresh_token (accessToken)');
        $this->addSql('ALTER TABLE user CHANGE join_time join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_token DROP INDEX IDX_B6A2DD68A76ED395, ADD UNIQUE INDEX UNIQ_B6A2DD68A76ED395 (user_id)');
        $this->addSql('ALTER TABLE access_token DROP INDEX IDX_B6A2DD6819EB6921, ADD UNIQUE INDEX UNIQ_B6A2DD6819EB6921 (client_id)');
        $this->addSql('ALTER TABLE access_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE auth_code DROP INDEX IDX_5933D02CA76ED395, ADD UNIQUE INDEX UNIQ_5933D02CA76ED395 (user_id)');
        $this->addSql('ALTER TABLE auth_code DROP INDEX IDX_5933D02C19EB6921, ADD UNIQUE INDEX UNIQ_5933D02C19EB6921 (client_id)');
        $this->addSql('ALTER TABLE auth_code CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F2195350A9822');
        $this->addSql('DROP INDEX UNIQ_C74F2195350A9822 ON refresh_token');
        $this->addSql('ALTER TABLE refresh_token DROP accessToken, CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE join_time join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
