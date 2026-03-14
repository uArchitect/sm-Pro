-- Blog modülü tablosu (Sipariş Masanda)
-- Çalıştırmadan önce veritabanı seçin: USE your_database_name;

CREATE TABLE IF NOT EXISTS blog_posts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    body LONGTEXT NOT NULL,
    featured_image VARCHAR(255) NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    published_at TIMESTAMP NULL,
    author_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY blog_posts_slug_unique (slug),
    KEY blog_posts_is_published_published_at_index (is_published, published_at),
    KEY blog_posts_author_id_foreign (author_id),
    CONSTRAINT blog_posts_author_id_foreign FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
