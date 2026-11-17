<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261117012854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointments ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A9395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6A41727A9395C3F3 ON appointments (customer_id)');
        $this->addSql('ALTER TABLE orders ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_E52FFDEE9395C3F3 ON orders (customer_id)');
        $this->addSql('ALTER TABLE user ADD is_active TINYINT NOT NULL, ADD created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A9395C3F3');
        $this->addSql('DROP INDEX IDX_6A41727A9395C3F3 ON appointments');
        $this->addSql('ALTER TABLE appointments DROP customer_id');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9395C3F3');
        $this->addSql('DROP INDEX IDX_E52FFDEE9395C3F3 ON orders');
        $this->addSql('ALTER TABLE orders DROP customer_id');
        $this->addSql('ALTER TABLE user DROP is_active, DROP created_at');
    }
}
