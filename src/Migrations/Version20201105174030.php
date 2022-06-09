<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201105174030 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE programs (id INT AUTO_INCREMENT NOT NULL, cp_program1 VARCHAR(255) DEFAULT NULL, cp_program2 VARCHAR(255) DEFAULT NULL, ce1_program1 VARCHAR(255) DEFAULT NULL, ce1_program2 VARCHAR(255) DEFAULT NULL, ce1_program3 VARCHAR(255) DEFAULT NULL, ce2_program1 VARCHAR(255) DEFAULT NULL, ce2_program2 VARCHAR(255) DEFAULT NULL, cm1_program1 VARCHAR(255) DEFAULT NULL, cm1_program2 VARCHAR(255) DEFAULT NULL, cm2_program1 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE homepage DROP cp_program, DROP ce1_program, DROP image_cp, DROP image_ce1');
        
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE programs');
        $this->addSql('ALTER TABLE homepage ADD cp_program VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD ce1_program VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD image_cp VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD image_ce1 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        
    }
}
