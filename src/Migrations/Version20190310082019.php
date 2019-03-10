<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190310082019 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oauth_scope (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_87ACBFC277153098 (code), INDEX code_idx (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_access_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, client_id INT NOT NULL, expiry_date_time DATETIME NOT NULL, INDEX IDX_F7FA86A4A76ED395 (user_id), INDEX IDX_F7FA86A419EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_access_token_oauth_scope (oauth_access_token_id INT NOT NULL, oauth_scope_id INT NOT NULL, INDEX IDX_C84CCF84888114B4 (oauth_access_token_id), INDEX IDX_C84CCF844857DA2D (oauth_scope_id), PRIMARY KEY(oauth_access_token_id, oauth_scope_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_granted_scope (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, client_id INT NOT NULL, scope_id INT NOT NULL, INDEX IDX_98A620819EB6921 (client_id), INDEX IDX_98A6208682B5931 (scope_id), INDEX user_idx (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_client (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, secret VARCHAR(255) NOT NULL, redirect VARCHAR(255) NOT NULL, INDEX IDX_AD73274DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oauth_access_token ADD CONSTRAINT FK_F7FA86A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE oauth_access_token ADD CONSTRAINT FK_F7FA86A419EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id)');
        $this->addSql('ALTER TABLE oauth_access_token_oauth_scope ADD CONSTRAINT FK_C84CCF84888114B4 FOREIGN KEY (oauth_access_token_id) REFERENCES oauth_access_token (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth_access_token_oauth_scope ADD CONSTRAINT FK_C84CCF844857DA2D FOREIGN KEY (oauth_scope_id) REFERENCES oauth_scope (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth_granted_scope ADD CONSTRAINT FK_98A6208A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE oauth_granted_scope ADD CONSTRAINT FK_98A620819EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id)');
        $this->addSql('ALTER TABLE oauth_granted_scope ADD CONSTRAINT FK_98A6208682B5931 FOREIGN KEY (scope_id) REFERENCES oauth_scope (id)');
        $this->addSql('ALTER TABLE oauth_client ADD CONSTRAINT FK_AD73274DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oauth_access_token_oauth_scope DROP FOREIGN KEY FK_C84CCF844857DA2D');
        $this->addSql('ALTER TABLE oauth_granted_scope DROP FOREIGN KEY FK_98A6208682B5931');
        $this->addSql('ALTER TABLE oauth_access_token DROP FOREIGN KEY FK_F7FA86A4A76ED395');
        $this->addSql('ALTER TABLE oauth_granted_scope DROP FOREIGN KEY FK_98A6208A76ED395');
        $this->addSql('ALTER TABLE oauth_client DROP FOREIGN KEY FK_AD73274DA76ED395');
        $this->addSql('ALTER TABLE oauth_access_token_oauth_scope DROP FOREIGN KEY FK_C84CCF84888114B4');
        $this->addSql('ALTER TABLE oauth_access_token DROP FOREIGN KEY FK_F7FA86A419EB6921');
        $this->addSql('ALTER TABLE oauth_granted_scope DROP FOREIGN KEY FK_98A620819EB6921');
        $this->addSql('DROP TABLE oauth_scope');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE oauth_access_token');
        $this->addSql('DROP TABLE oauth_access_token_oauth_scope');
        $this->addSql('DROP TABLE oauth_granted_scope');
        $this->addSql('DROP TABLE oauth_client');
    }
}
