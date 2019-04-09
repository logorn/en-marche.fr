<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20190412151004 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE managed_area_senator (id INT UNSIGNED AUTO_INCREMENT NOT NULL, tag_id INT UNSIGNED DEFAULT NULL, since DATE DEFAULT NULL, INDEX IDX_E41F872CBAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE managed_area_senator ADD CONSTRAINT FK_E41F872CBAD26311 FOREIGN KEY (tag_id) REFERENCES referent_tags (id)');
        $this->addSql('ALTER TABLE adherents ADD senator_managed_area_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE adherents ADD CONSTRAINT FK_562C7DA38C271B0B FOREIGN KEY (senator_managed_area_id) REFERENCES managed_area_senator (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_562C7DA38C271B0B ON adherents (senator_managed_area_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE adherents DROP FOREIGN KEY FK_562C7DA38C271B0B');
        $this->addSql('DROP TABLE managed_area_senator');
        $this->addSql('DROP INDEX UNIQ_562C7DA38C271B0B ON adherents');
        $this->addSql('ALTER TABLE adherents DROP senator_managed_area_id');
    }
}
