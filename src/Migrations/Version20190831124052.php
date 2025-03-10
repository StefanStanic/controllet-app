<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190831124052 extends AbstractMigration
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
        $this->addSql('ALTER TABLE bills CHANGE category_id category_id INT DEFAULT NULL, CHANGE subcategory_id subcategory_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE activation_key_id activation_key_id INT DEFAULT NULL, CHANGE company company VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE account ADD currency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A438248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('CREATE INDEX IDX_7D3656A438248176 ON account (currency_id)');
        $this->addSql('ALTER TABLE sub_category CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE budget CHANGE category_id category_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A438248176');
        $this->addSql('DROP INDEX IDX_7D3656A438248176 ON account');
        $this->addSql('ALTER TABLE account DROP currency_id');
        $this->addSql('ALTER TABLE bills CHANGE category_id category_id INT DEFAULT NULL, CHANGE subcategory_id subcategory_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE budget CHANGE category_id category_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_category CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE note note VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE activation_key_id activation_key_id INT DEFAULT NULL, CHANGE company company VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
