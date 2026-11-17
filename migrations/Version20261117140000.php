<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261117140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add fulfillment method (pickup or delivery) to orders';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE orders ADD fulfillment_method VARCHAR(20) DEFAULT 'delivery' NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders DROP fulfillment_method');
    }
}
