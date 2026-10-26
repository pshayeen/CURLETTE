<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261026014406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointments (id INT AUTO_INCREMENT NOT NULL, customer_name VARCHAR(100) NOT NULL, customer_email VARCHAR(180) NOT NULL, service VARCHAR(100) NOT NULL, appointment_date DATE NOT NULL, appointment_time TIME NOT NULL, notes LONGTEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, deposit_amount NUMERIC(10, 2) NOT NULL, payment_reference VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, sender_name VARCHAR(100) NOT NULL, sender_email VARCHAR(180) NOT NULL, sender_phone VARCHAR(30) DEFAULT NULL, message_text LONGTEXT NOT NULL, is_read TINYINT NOT NULL, created_at DATE NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, product_name VARCHAR(255) NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, customer_order_id INT NOT NULL, INDEX IDX_62809DB0A15A2E17 (customer_order_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, customer_name VARCHAR(100) NOT NULL, customer_email VARCHAR(180) NOT NULL, status VARCHAR(20) NOT NULL, total NUMERIC(10, 2) NOT NULL, payment_method VARCHAR(20) NOT NULL, payment_reference VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, product_name VARCHAR(255) NOT NULL, product_description LONGTEXT NOT NULL, stock_quantity INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES orders (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB0A15A2E17');
        $this->addSql('DROP TABLE appointments');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
