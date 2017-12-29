<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171229024400 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE launch_screen CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D6494049BA4A ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D6496806549E ON user');
        $this->addSql('ALTER TABLE user ADD phone BIGINT DEFAULT NULL, ADD backup_phone BIGINT DEFAULT NULL, DROP domestic_phone, DROP international_phone, CHANGE join_time join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649444F97DD ON user (phone)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649BD6D4837 ON user (backup_phone)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE launch_screen CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649444F97DD ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649BD6D4837 ON user');
        $this->addSql('ALTER TABLE user ADD domestic_phone BIGINT DEFAULT NULL, ADD international_phone BIGINT DEFAULT NULL, DROP phone, DROP backup_phone, CHANGE join_time join_time DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494049BA4A ON user (domestic_phone)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496806549E ON user (international_phone)');
    }
}
