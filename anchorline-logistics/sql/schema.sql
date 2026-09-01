-- Anchorline Logistics - database schema and seed data.
-- Import: C:\xampp\mysql\bin\mysql.exe -u root anchorline < sql/schema.sql
-- (create the database first: CREATE DATABASE anchorline CHARACTER SET utf8mb4;)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS scan_events;
DROP TABLE IF EXISTS enquiries;
DROP TABLE IF EXISTS consignments;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------
-- users
-- -----------------------------------------------------------------
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)  NOT NULL,
    email         VARCHAR(190)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('admin', 'member', 'normal') NOT NULL DEFAULT 'normal',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------
-- consignments
-- -----------------------------------------------------------------
CREATE TABLE consignments (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    waybill      VARCHAR(20)  NOT NULL UNIQUE,   -- format ANC-0000-STATE
    sender_name  VARCHAR(100) NOT NULL,
    receiver_name VARCHAR(100) NOT NULL,
    origin       VARCHAR(100) NOT NULL,
    destination  VARCHAR(100) NOT NULL,
    service_type ENUM('road', 'warehouse', 'cold', 'international') NOT NULL,
    status       ENUM('booked', 'collected', 'in_transit', 'held', 'delivered') NOT NULL DEFAULT 'booked',
    eta          VARCHAR(60)  NULL,              -- free text, e.g. "19 Jul 2026, 14:00 AEST"
    created_by   INT UNSIGNED NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_consignments_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------
-- scan_events  (one consignment has many scans -> the tracking rail)
-- -----------------------------------------------------------------
CREATE TABLE scan_events (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    consignment_id  INT UNSIGNED NOT NULL,
    event_text      VARCHAR(255) NOT NULL,
    location        VARCHAR(100) NOT NULL,
    scanned_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by      INT UNSIGNED NULL,
    CONSTRAINT fk_scan_events_consignment FOREIGN KEY (consignment_id) REFERENCES consignments(id) ON DELETE CASCADE,
    CONSTRAINT fk_scan_events_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------
-- enquiries  (contact/quote form submissions)
-- -----------------------------------------------------------------
CREATE TABLE enquiries (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name  VARCHAR(100) NOT NULL,
    email      VARCHAR(190) NOT NULL,
    phone      VARCHAR(20)  NOT NULL,
    service    ENUM('road', 'warehouse', 'cold', 'international', 'other') NOT NULL,
    message    TEXT NOT NULL,
    consent    TINYINT(1) NOT NULL DEFAULT 0,
    user_id    INT UNSIGNED NULL,
    status     ENUM('new', 'contacted', 'closed') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_enquiries_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------
-- Seed data
-- -----------------------------------------------------------------

-- Passwords: admin@anchorline.example / Admin@12345
--            member@anchorline.example / Member@12345
-- Change these after import if this ever leaves a local dev machine.
INSERT INTO users (name, email, password_hash, role) VALUES
('Site Admin',   'admin@anchorline.example',  '$2y$10$5vxtXbNSdMRxgvhFwrPt3OtMSy8zDMSGibEyVwXqXfyqbUtpylR1S', 'admin'),
('Depot Staff',  'member@anchorline.example', '$2y$10$k6vcVA3b1XBHTAl.5v4rbeoD9WwFUm.BY77JJLhrMO4ycIlwX0r1W', 'member');

INSERT INTO consignments (waybill, sender_name, receiver_name, origin, destination, service_type, status, eta, created_by) VALUES
('ANC-4471-QLD', 'Kettle & Co Wholesale',   'Eagle Farm Distribution', 'Port Botany, NSW', 'Eagle Farm, QLD',     'road',          'in_transit', '19 Jul 2026, 14:00 AEST',        1),
('ANC-7726-VIC', 'Harbourfield Foods',      'Dandenong Cold Store',   'Alexandria, NSW',  'Dandenong South, VIC', 'cold',          'delivered',  'Delivered 15 Jul 2026, 11:22 AEST', 1),
('ANC-1039-WA',  'Anchorline Sea Freight',  'Fremantle Importers',    'Port Botany, NSW', 'Fremantle, WA',       'international', 'held',       'Awaiting customs release',        1);

INSERT INTO scan_events (consignment_id, event_text, location, scanned_at, created_by) VALUES
(1, 'Collected from consignor', 'Port Botany, NSW', '2026-07-16 08:12:00', 2),
(1, 'Scanned into sortation hub', 'Chullora, NSW', '2026-07-16 19:40:00', 2),
(1, 'Departed on line-haul B214', 'Chullora, NSW', '2026-07-17 06:05:00', 2),
(1, 'Arrived changeover depot', 'Coffs Harbour, NSW', '2026-07-18 05:30:00', 2),
(2, 'Collected from consignor', 'Alexandria, NSW', '2026-07-14 07:50:00', 2),
(2, 'Temperature check passed at 4.1C', 'Alexandria, NSW', '2026-07-14 15:10:00', 2),
(2, 'Arrived at destination depot', 'Dandenong South, VIC', '2026-07-15 06:44:00', 2),
(2, 'Delivered, signed by R. Okafor', 'Dandenong South, VIC', '2026-07-15 11:22:00', 2),
(3, 'Container loaded, vessel MV Corella', 'Port Botany, NSW', '2026-07-02 09:00:00', 2),
(3, 'Vessel berthed', 'Fremantle, WA', '2026-07-11 16:30:00', 2),
(3, 'Held for customs inspection', 'Fremantle, WA', '2026-07-12 10:15:00', 2);
