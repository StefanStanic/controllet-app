<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190824083950 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE transaction CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE note note VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bills ADD date_due DATE NOT NULL, CHANGE category_id category_id INT DEFAULT NULL, CHANGE subcategory_id subcategory_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE activation_key_id activation_key_id INT DEFAULT NULL, CHANGE company company VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE sub_category CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE budget CHANGE category_id category_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cron_report CHANGE job_id job_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bills DROP date_due, CHANGE category_id category_id INT DEFAULT NULL, CHANGE subcategory_id subcategory_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE budget CHANGE category_id category_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cron_report CHANGE job_id job_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_category CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE note note VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE activation_key_id activation_key_id INT DEFAULT NULL, CHANGE company company VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
