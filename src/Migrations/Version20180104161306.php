<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180104161306 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, post_user_id INT DEFAULT NULL, reply_to_id INT DEFAULT NULL, content VARCHAR(150) NOT NULL, INDEX IDX_9474526C4E7AF8F (gallery_id), INDEX IDX_9474526C9A8664A6 (post_user_id), INDEX IDX_9474526CFFDF7169 (reply_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(150) NOT NULL, description VARCHAR(1024) NOT NULL, is_public TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, type SMALLINT NOT NULL, is_external TINYINT(1) NOT NULL, url VARCHAR(512) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, thumb VARCHAR(256) NOT NULL, hd VARCHAR(256) NOT NULL, origin VARCHAR(256) DEFAULT NULL, is_visible TINYINT(1) NOT NULL, is_public TINYINT(1) NOT NULL, INDEX IDX_14B784184E7AF8F (gallery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9A8664A6 FOREIGN KEY (post_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFFDF7169 FOREIGN KEY (reply_to_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784184E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE access_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE alumni ADD chinese_name VARCHAR(255) NOT NULL, ADD english_name VARCHAR(255) NOT NULL, ADD birthday DATE NOT NULL, ADD gender SMALLINT NOT NULL, ADD junior_school SMALLINT NOT NULL, ADD junior_registration INT DEFAULT NULL, ADD junior_class SMALLINT DEFAULT NULL, ADD junior_remark VARCHAR(150) DEFAULT NULL, ADD senior_school SMALLINT NOT NULL, ADD senior_registration INT DEFAULT NULL, ADD senior_class SMALLINT DEFAULT NULL, ADD senior_remark VARCHAR(150) DEFAULT NULL, ADD university JSON DEFAULT NULL, ADD major VARCHAR(255) DEFAULT NULL, ADD work_info VARCHAR(500) DEFAULT NULL, ADD personal_info VARCHAR(500) DEFAULT NULL, ADD online_contact VARCHAR(100) NOT NULL, ADD location JSON NOT NULL');
        $this->addSql('ALTER TABLE auth_code CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE refresh_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE join_time join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE read_time read_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CFFDF7169');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4E7AF8F');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784184E7AF8F');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE photo');
        $this->addSql('ALTER TABLE access_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE alumni DROP chinese_name, DROP english_name, DROP birthday, DROP gender, DROP junior_school, DROP junior_registration, DROP junior_class, DROP junior_remark, DROP senior_school, DROP senior_registration, DROP senior_class, DROP senior_remark, DROP university, DROP major, DROP work_info, DROP personal_info, DROP online_contact, DROP location');
        $this->addSql('ALTER TABLE auth_code CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE code CHANGE time time DATETIME NOT NULL, CHANGE expired expired DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE refresh_token CHANGE expiry_time expiry_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE read_time read_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE join_time join_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
