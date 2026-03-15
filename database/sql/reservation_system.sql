-- Rezervasyon Sistemi (Premium) - Bölgeler, Masalar, Rezervasyonlar
-- Çalıştırmadan önce: tenants tablosu mevcut olmalı.
-- MySQL / MariaDB

-- 1) Bölgeler (Cam kenarı, Orta bölge, vb.)
CREATE TABLE IF NOT EXISTS reservation_zones (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_reservation_zones_tenant_sort (tenant_id, sort_order),
    CONSTRAINT fk_reservation_zones_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

-- 2) Masalar (her masa bir bölgeye bağlı)
CREATE TABLE IF NOT EXISTS reservation_tables (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    zone_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(80) NOT NULL,
    capacity SMALLINT UNSIGNED NOT NULL DEFAULT 2 COMMENT 'Kişi kapasitesi',
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_reservation_tables_tenant_zone (tenant_id, zone_id),
    CONSTRAINT fk_reservation_tables_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_reservation_tables_zone
        FOREIGN KEY (zone_id) REFERENCES reservation_zones(id) ON DELETE CASCADE
);

-- 3) Rezervasyonlar (saat bazlı dolu/boş için)
CREATE TABLE IF NOT EXISTS reservations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    table_id BIGINT UNSIGNED NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    customer_phone VARCHAR(30) NOT NULL,
    customer_email VARCHAR(120) NULL,
    reservation_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, confirmed, cancelled',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_reservations_tenant_date (tenant_id, reservation_date),
    INDEX idx_reservations_table_date_time (table_id, reservation_date, start_time, end_time),
    CONSTRAINT fk_reservations_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_reservations_table
        FOREIGN KEY (table_id) REFERENCES reservation_tables(id) ON DELETE CASCADE
);
