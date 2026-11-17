<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261117130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add saved delivery address to user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD delivery_address VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP delivery_address');
    }
}
