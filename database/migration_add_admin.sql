-- Migration: adds everything the admin panel needs to a database that was
-- already set up before these features existed.
--
-- Run each statement ONE AT A TIME (select just that statement and run it),
-- not the whole file at once. If a statement errors with something like
-- "Duplicate column name", that just means you already have it — skip that
-- one and move to the next.

USE curlette_db;

-- Adds the admin flag to existing accounts (defaults to 0 = not admin)
ALTER TABLE user_account
    ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0;

-- Adds appointment status tracking (defaults existing rows to 'upcoming')
ALTER TABLE appointment_booking
    ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'upcoming';

-- Creates the contact_message table if the Contact page was added after
-- your database was first set up (no-op if it already exists)
CREATE TABLE IF NOT EXISTS contact_message (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(255)  NOT NULL,
    phone       VARCHAR(30)   NULL,
    message     TEXT          NOT NULL,
    is_read     TINYINT(1)    NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Only run this next one if contact_message already existed WITHOUT
-- is_read (i.e. the CREATE TABLE above said "already exists" / did nothing).
-- If contact_message was just newly created above, skip this statement.
ALTER TABLE contact_message
    ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0;

-- Now make your account an admin — replace with your real email
UPDATE user_account SET is_admin = 1 WHERE email = 'your@email.com';
