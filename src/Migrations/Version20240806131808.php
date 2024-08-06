<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240806131808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE monsieurbiz_case_study (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) DEFAULT 1 NOT NULL, image VARCHAR(255) DEFAULT NULL, imageThumbnail VARCHAR(255) DEFAULT NULL, state VARCHAR(255) NOT NULL, publishedAt DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_case_study_tags (case_study_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_7444DFB670CD7994 (case_study_id), INDEX IDX_7444DFB6BAD26311 (tag_id), PRIMARY KEY(case_study_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_case_study_channels (case_study_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_649BC36A70CD7994 (case_study_id), INDEX IDX_649BC36A72F5A1AA (channel_id), PRIMARY KEY(case_study_id, channel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_case_study_tag (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) DEFAULT 1 NOT NULL, position INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_case_study_tag_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_1B127E732C2AC5D3 (translatable_id), UNIQUE INDEX monsieurbiz_case_study_tag_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_case_study_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_C16202FE2C2AC5D3 (translatable_id), UNIQUE INDEX monsieurbiz_case_study_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_tags ADD CONSTRAINT FK_7444DFB670CD7994 FOREIGN KEY (case_study_id) REFERENCES monsieurbiz_case_study (id)');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_tags ADD CONSTRAINT FK_7444DFB6BAD26311 FOREIGN KEY (tag_id) REFERENCES monsieurbiz_case_study_tag (id)');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_channels ADD CONSTRAINT FK_649BC36A70CD7994 FOREIGN KEY (case_study_id) REFERENCES monsieurbiz_case_study (id)');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_channels ADD CONSTRAINT FK_649BC36A72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_tag_translation ADD CONSTRAINT FK_1B127E732C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES monsieurbiz_case_study_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_translation ADD CONSTRAINT FK_C16202FE2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES monsieurbiz_case_study (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE monsieurbiz_case_study_tags DROP FOREIGN KEY FK_7444DFB670CD7994');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_tags DROP FOREIGN KEY FK_7444DFB6BAD26311');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_channels DROP FOREIGN KEY FK_649BC36A70CD7994');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_channels DROP FOREIGN KEY FK_649BC36A72F5A1AA');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_tag_translation DROP FOREIGN KEY FK_1B127E732C2AC5D3');
        $this->addSql('ALTER TABLE monsieurbiz_case_study_translation DROP FOREIGN KEY FK_C16202FE2C2AC5D3');
        $this->addSql('DROP TABLE monsieurbiz_case_study');
        $this->addSql('DROP TABLE monsieurbiz_case_study_tags');
        $this->addSql('DROP TABLE monsieurbiz_case_study_channels');
        $this->addSql('DROP TABLE monsieurbiz_case_study_tag');
        $this->addSql('DROP TABLE monsieurbiz_case_study_tag_translation');
        $this->addSql('DROP TABLE monsieurbiz_case_study_translation');
    }
}
