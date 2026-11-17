<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261117120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add customer phone to appointments and orders, delivery address to orders';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appointments ADD customer_phone VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD customer_phone VARCHAR(30) DEFAULT NULL, ADD delivery_address LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appointments DROP customer_phone');
        $this->addSql('ALTER TABLE orders DROP customer_phone, DROP delivery_address');
    }
}
