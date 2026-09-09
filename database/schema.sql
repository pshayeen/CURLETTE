-- Curlétte Hair Studio — database schema
-- Database name matches database/config.php ("curlette_db")

CREATE DATABASE IF NOT EXISTS curlette_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE curlette_db;

-- Used by actions/auth-handler.php and the nav account dropdown.
-- is_admin defaults to 0 for everyone; reserved for a future admin panel.
CREATE TABLE IF NOT EXISTS user_account (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(20)  NOT NULL,
    email         VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_admin      TINYINT(1)   NOT NULL DEFAULT 0,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_user_account_username (username),
    UNIQUE KEY uq_user_account_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Used by actions/booking-handler.php and book-appointment.php.
-- status is managed by the admin panel (upcoming | completed | cancelled).
CREATE TABLE IF NOT EXISTS appointment_booking (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id           INT UNSIGNED NOT NULL,
    service           VARCHAR(100) NOT NULL,
    appointment_date  DATE         NOT NULL,
    appointment_time  TIME         NOT NULL,
    notes             VARCHAR(500) NULL,
    status            VARCHAR(20)  NOT NULL DEFAULT 'upcoming',
    created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    KEY idx_appointment_booking_user_id (user_id),
    CONSTRAINT fk_appointment_booking_user
        FOREIGN KEY (user_id) REFERENCES user_account (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product catalog. stock_quantity decrements on order (see checkout-handler.php).
CREATE TABLE IF NOT EXISTS product (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150)   NOT NULL,
    price           DECIMAL(8,2)   NOT NULL,
    image           VARCHAR(255)   NOT NULL,
    description     VARCHAR(500)   NOT NULL,
    stock_quantity  INT UNSIGNED   NOT NULL DEFAULT 0,
    sort_order      INT UNSIGNED   NOT NULL DEFAULT 0,
    created_at      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data — 10 products, 25 units in stock each
INSERT INTO product (name, price, image, description, stock_quantity, sort_order) VALUES
('Curl Cleansing Conditioner', 380.00, 'assets/prod.png',  'A gentle, sulfate-free cleanser that lifts buildup without stripping your curls of their natural moisture.', 25, 1),
('Moisturising Conditioner',   380.00, 'assets/prod2.png', 'A rich, slip-heavy conditioner that detangles easily and deeply hydrates every curl pattern.', 25, 2),
('Curl Moisturising Treatment',450.00, 'assets/prod4.png', 'A leave-in style treatment that locks in moisture and softness between wash days.', 25, 3),
('Curl Protein Treatment',     450.00, 'assets/prod5.png', 'A strengthening treatment that helps repair damage and reduce breakage in fragile curls.', 25, 4),
('Detangling Wide-Tooth Comb', 250.00, 'assets/prod.png',  'A gentle wide-tooth comb built to work through wet curls and knots without disrupting your curl pattern.', 25, 5),
('Leave-In Conditioner',       350.00, 'assets/prod2.png', 'A lightweight, non-greasy leave-in that softens, hydrates, and preps curls for styling.', 25, 6),
('Curl Defining Gel',          420.00, 'assets/prod4.png', 'A strong-hold, non-flaking gel that locks in curl definition and keeps frizz away all day.', 25, 7),
('Silk Pillowcase',            650.00, 'assets/prod5.png', 'A smooth silk pillowcase that reduces overnight friction, protecting curls from frizz and breakage while you sleep.', 25, 8),
('Microfiber Curl Towel',      300.00, 'assets/prod.png',  'A soft microfiber towel that scrunches out excess water without roughing up the hair cuticle like regular towels do.', 25, 9),
('Curl Refresher Spray',       320.00, 'assets/prod2.png', 'A quick-mist spray that revives second- and third-day curls, cutting down on how often you need a full wash.', 25, 10);

-- One row per (user, product); adding the same product again just bumps quantity.
CREATE TABLE IF NOT EXISTS cart_item (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    product_id  INT UNSIGNED NOT NULL,
    quantity    INT UNSIGNED NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_cart_item_user_product (user_id, product_id),
    CONSTRAINT fk_cart_item_user
        FOREIGN KEY (user_id) REFERENCES user_account (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_cart_item_product
        FOREIGN KEY (product_id) REFERENCES product (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Named shop_order (not "order") since ORDER is a reserved SQL keyword.
-- order_item snapshots name/price at purchase time so later catalog edits
-- never rewrite historical orders.
CREATE TABLE IF NOT EXISTS shop_order (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED   NOT NULL,
    status      VARCHAR(20)    NOT NULL DEFAULT 'placed',
    total       DECIMAL(10,2)  NOT NULL,
    created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    KEY idx_shop_order_user_id (user_id),
    CONSTRAINT fk_shop_order_user
        FOREIGN KEY (user_id) REFERENCES user_account (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shop_order_item (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id      INT UNSIGNED   NOT NULL,
    product_id    INT UNSIGNED   NULL,
    product_name  VARCHAR(150)   NOT NULL,
    unit_price    DECIMAL(8,2)   NOT NULL,
    quantity      INT UNSIGNED   NOT NULL,

    KEY idx_shop_order_item_order_id (order_id),
    CONSTRAINT fk_shop_order_item_order
        FOREIGN KEY (order_id) REFERENCES shop_order (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_shop_order_item_product
        FOREIGN KEY (product_id) REFERENCES product (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Submissions from the Contact page form. No login required to submit.
-- is_read is managed by the admin panel.
CREATE TABLE IF NOT EXISTS contact_message (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(255)  NOT NULL,
    phone       VARCHAR(30)   NULL,
    message     TEXT          NOT NULL,
    is_read     TINYINT(1)    NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- To make a user an admin, run this after they've signed up normally:
-- UPDATE user_account SET is_admin = 1 WHERE email = 'their@email.com';
