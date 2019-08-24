<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190824083757 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bills (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, subcategory_id INT DEFAULT NULL, account_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, amount INT NOT NULL, note VARCHAR(255) NOT NULL, date_added DATETIME NOT NULL, date_updated DATETIME NOT NULL, active INT NOT NULL, INDEX IDX_22775DD012469DE2 (category_id), INDEX IDX_22775DD05DC6FE57 (subcategory_id), INDEX IDX_22775DD09B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bills ADD CONSTRAINT FK_22775DD012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE bills ADD CONSTRAINT FK_22775DD05DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES sub_category (id)');
        $this->addSql('ALTER TABLE bills ADD CONSTRAINT FK_22775DD09B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transaction CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE note note VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE activation_key_id activation_key_id INT DEFAULT NULL, CHANGE company company VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE sub_category CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE budget CHANGE category_id category_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cron_report CHANGE job_id job_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE bills');
        $this->addSql('ALTER TABLE budget CHANGE category_id category_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cron_report CHANGE job_id job_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_category CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE note note VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE activation_key_id activation_key_id INT DEFAULT NULL, CHANGE company company VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
