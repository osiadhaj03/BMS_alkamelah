# 🗄️ Database Analysis Report: BMS Production Database

**Generated:** 2025-11-24 22:17:12
**Database:** `bms`
**Host:** 145.223.98.97
**Total Tables:** 53
**Total Size:** 18.87 GB

---

## 📊 Executive Summary

- **Large Tables (>100MB):** 3 tables
- **Medium Tables (10-100MB):** 3 tables
- **Small Tables (<10MB):** 47 tables
- **Empty Tables:** 20 tables

---

## 🔝 Largest Tables (Top 20)

| # | Table Name | Rows | Data Size | Index Size | Total Size | Engine |
|---|------------|------|-----------|------------|------------|--------|
| 1 | `pages` | 5,024,544 | 16.63 GB | 1.37 GB | 17.99 GB | InnoDB |
| 2 | `chapters` | 1,638,726 | 266.83 MB | 446.69 MB | 713.52 MB | InnoDB |
| 3 | `activity_log` | 14,653 | 108.56 MB | 2.23 MB | 110.8 MB | InnoDB |
| 4 | `filament_exceptions_table` | 2,067 | 33.52 MB | 0 B | 33.52 MB | InnoDB |
| 5 | `books` | 12,009 | 10.52 MB | 4.02 MB | 14.53 MB | InnoDB |
| 6 | `authors` | 3,622 | 12.09 MB | 240 KB | 12.33 MB | InnoDB |
| 7 | `volumes` | 22,269 | 2.34 MB | 6.05 MB | 8.39 MB | InnoDB |
| 8 | `book_extracted_metadata` | 2,065 | 448 KB | 608 KB | 1.03 MB | InnoDB |
| 9 | `author_book` | 3,816 | 288 KB | 656 KB | 944 KB | InnoDB |
| 10 | `publishers` | 1,710 | 304 KB | 208 KB | 512 KB | InnoDB |
| 11 | `blog_posts` | 20 | 96 KB | 144 KB | 240 KB | InnoDB |
| 12 | `contact_us` | 100 | 112 KB | 64 KB | 176 KB | InnoDB |
| 13 | `book_indexes` | 0 | 16 KB | 144 KB | 160 KB | InnoDB |
| 14 | `banner_contents` | 22 | 16 KB | 128 KB | 144 KB | InnoDB |
| 15 | `bok_imports` | 0 | 16 KB | 96 KB | 112 KB | InnoDB |
| 16 | `blog_categories` | 12 | 16 KB | 80 KB | 96 KB | InnoDB |
| 17 | `footnotes` | 0 | 16 KB | 80 KB | 96 KB | InnoDB |
| 18 | `page_references` | 0 | 16 KB | 80 KB | 96 KB | InnoDB |
| 19 | `references` | 0 | 16 KB | 80 KB | 96 KB | InnoDB |
| 20 | `banner_categories` | 11 | 16 KB | 64 KB | 80 KB | InnoDB |

---

## 🗑️ Empty Tables (Can Be Removed)

| Table Name | Purpose/Notes |
|------------|---------------|
| `book_indexes` | Empty - 0 rows |
| `bok_imports` | Empty - 0 rows |
| `footnotes` | Empty - 0 rows |
| `page_references` | Empty - 0 rows |
| `references` | Empty - 0 rows |
| `book_metadata` | Empty - 0 rows |
| `personal_access_tokens` | Empty - 0 rows |
| `volume_links` | Empty - 0 rows |
| `failed_jobs` | Empty - 0 rows |
| `folder_has_models` | Empty - 0 rows |
| `media_has_models` | Empty - 0 rows |
| `model_has_permissions` | Empty - 0 rows |
| `notifications` | Empty - 0 rows |
| `taggables` | Empty - 0 rows |
| `borrowings` | Empty - 0 rows |
| `categories` | Empty - 0 rows |
| `password_reset_tokens` | Empty - 0 rows |
| `reservations` | Empty - 0 rows |
| `table_settings` | Empty - 0 rows |
| `tags` | Empty - 0 rows |

---

## 📋 Detailed Table Analysis

### 📄 `pages`

**Statistics:**
- **Rows:** 5,024,544
- **Data Size:** 16.63 GB
- **Index Size:** 1.37 GB
- **Total Size:** 17.99 GB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-10-06 22:57:04

**Columns:** (15)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `book_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `volume_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `chapter_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `page_number` | int(11) | NO | - | NULL | - |
| `internal_index` | varchar(50) | YES | MUL | NULL | - |
| `part` | varchar(255) | YES | - | NULL | - |
| `content` | longtext | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `original_page_number` | int(11) | YES | - | NULL | - |
| `word_count` | int(11) | YES | - | NULL | - |
| `html_content` | longtext | YES | - | NULL | - |
| `printed_missing` | tinyint(1) | YES | - | NULL | - |
| `formatted_content` | longtext | YES | - | NULL | - |

**Indexes:** (10)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `pages_book_id_page_number_index` | BTREE | book_id, page_number | ❌ |
| `pages_chapter_id_page_number_index` | BTREE | chapter_id, page_number | ❌ |
| `pages_volume_id_page_number_index` | BTREE | volume_id, page_number | ❌ |
| `pages_book_id_created_at_index` | BTREE | book_id, created_at | ❌ |
| `idx_pages_book_volume` | BTREE | book_id, volume_id | ❌ |
| `idx_pages_internal_index` | BTREE | internal_index | ❌ |
| `idx_pages_search` | BTREE | book_id, page_number, word_count | ❌ |
| `idx_pages_id_optimized` | BTREE | id | ❌ |
| `idx_pages_book_id` | BTREE | book_id | ❌ |

**Foreign Keys:** (3)

| Constraint | Column | References |
|------------|--------|------------|
| `pages_book_id_foreign` | `book_id` | `books`.`id` |
| `pages_chapter_id_foreign` | `chapter_id` | `chapters`.`id` |
| `pages_volume_id_foreign` | `volume_id` | `volumes`.`id` |

---

### 📄 `chapters`

**Statistics:**
- **Rows:** 1,638,726
- **Data Size:** 266.83 MB
- **Index Size:** 446.69 MB
- **Total Size:** 713.52 MB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-10-06 06:16:50

**Columns:** (16)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `volume_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `book_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `title` | varchar(255) | NO | - | NULL | - |
| `parent_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `level` | int(11) | NO | MUL | 1 | - |
| `order` | bigint(20) | YES | - | NULL | - |
| `page_start` | int(11) | YES | - | NULL | - |
| `page_end` | int(11) | YES | - | NULL | - |
| `estimated_reading_time` | int(11) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `internal_index_start` | int(11) | YES | - | NULL | - |
| `internal_index_end` | int(11) | YES | - | NULL | - |
| `start_page_internal_index` | int(11) | YES | - | NULL | - |
| `end_page_internal_index` | int(11) | YES | - | NULL | - |

**Indexes:** (8)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `chapters_parent_id_foreign` | BTREE | parent_id | ❌ |
| `chapters_level_index` | BTREE | level | ❌ |
| `chapters_book_id_level_order_index` | BTREE | book_id, level, order | ❌ |
| `chapters_volume_id_order_index` | BTREE | volume_id, order | ❌ |
| `chapters_book_id_order_index` | BTREE | book_id, order | ❌ |
| `chapters_book_id_parent_id_index` | BTREE | book_id, parent_id | ❌ |
| `idx_chapters_book_volume` | BTREE | book_id, volume_id | ❌ |

**Foreign Keys:** (3)

| Constraint | Column | References |
|------------|--------|------------|
| `chapters_book_id_foreign` | `book_id` | `books`.`id` |
| `chapters_parent_id_foreign` | `parent_id` | `chapters`.`id` |
| `chapters_volume_id_foreign` | `volume_id` | `volumes`.`id` |

---

### 📄 `activity_log`

**Statistics:**
- **Rows:** 14,653
- **Data Size:** 108.56 MB
- **Index Size:** 2.23 MB
- **Total Size:** 110.8 MB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39
- **Updated:** 2025-11-24 22:05:46

**Columns:** (12)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `log_name` | varchar(255) | YES | MUL | NULL | - |
| `description` | text | NO | - | NULL | - |
| `subject_type` | varchar(255) | YES | MUL | NULL | - |
| `event` | varchar(255) | YES | - | NULL | - |
| `subject_id` | char(36) | YES | - | NULL | - |
| `causer_type` | varchar(255) | YES | MUL | NULL | - |
| `causer_id` | char(36) | YES | - | NULL | - |
| `properties` | longtext | YES | - | NULL | - |
| `batch_uuid` | char(36) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (4)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `subject` | BTREE | subject_type, subject_id | ❌ |
| `causer` | BTREE | causer_type, causer_id | ❌ |
| `activity_log_log_name_index` | BTREE | log_name | ❌ |

---

### 📄 `filament_exceptions_table`

**Statistics:**
- **Rows:** 2,067
- **Data Size:** 33.52 MB
- **Index Size:** 0 B
- **Total Size:** 33.52 MB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:38
- **Updated:** 2025-11-24 22:05:46

**Columns:** (16)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `type` | varchar(255) | NO | - | NULL | - |
| `code` | varchar(255) | NO | - | NULL | - |
| `message` | longtext | NO | - | NULL | - |
| `file` | varchar(255) | NO | - | NULL | - |
| `line` | int(11) | NO | - | NULL | - |
| `trace` | text | NO | - | NULL | - |
| `method` | varchar(255) | NO | - | NULL | - |
| `path` | varchar(255) | NO | - | NULL | - |
| `query` | text | NO | - | NULL | - |
| `body` | text | NO | - | NULL | - |
| `cookies` | text | NO | - | NULL | - |
| `headers` | text | NO | - | NULL | - |
| `ip` | varchar(255) | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (1)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |

---

### 📄 `books`

**Statistics:**
- **Rows:** 12,009
- **Data Size:** 10.52 MB
- **Index Size:** 4.02 MB
- **Total Size:** 14.53 MB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-10-06 22:57:04

**Columns:** (29)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `shamela_id` | varchar(50) | YES | - | NULL | - |
| `title` | varchar(255) | NO | - | NULL | - |
| `description` | text | YES | - | NULL | - |
| `slug` | varchar(200) | NO | UNI | NULL | - |
| `cover_image` | varchar(255) | YES | - | NULL | - |
| `pages_count` | int(11) | YES | - | NULL | - |
| `volumes_count` | int(11) | YES | - | 1 | - |
| `status` | enum('draft','review','published','archived') | NO | MUL | draft | - |
| `visibility` | enum('public','private','restricted') | NO | - | public | - |
| `source_url` | varchar(255) | YES | - | NULL | - |
| `book_section_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `publisher_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `created_at` | timestamp | YES | MUL | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `edition_DATA` | int(11) | YES | - | NULL | - |
| `edition` | int(11) | YES | - | NULL | - |
| `edition_number` | int(11) | YES | - | NULL | - |
| `has_original_pagination` | tinyint(1) | YES | - | NULL | - |
| `publication_year` | year(4) | YES | - | NULL | - |
| `publication_place` | varchar(255) | YES | - | NULL | - |
| `volume_count` | int(11) | YES | - | 1 | - |
| `page_count` | int(11) | YES | - | NULL | - |
| `isbn` | varchar(20) | YES | - | NULL | - |
| `author_death_year` | year(4) | YES | - | NULL | - |
| `author_role` | varchar(100) | YES | - | مؤلف | - |
| `edition_info` | text | YES | - | NULL | - |
| `additional_notes` | text | YES | - | NULL | - |
| `section_id` | int(11) | YES | MUL | NULL | - |

**Indexes:** (8)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `books_slug_unique` | BTREE | slug | ✅ |
| `books_book_section_id_status_index` | BTREE | book_section_id, status | ❌ |
| `books_publisher_id_index` | BTREE | publisher_id | ❌ |
| `books_status_visibility_index` | BTREE | status, visibility | ❌ |
| `books_created_at_index` | BTREE | created_at | ❌ |
| `books_section_id_foreign` | BTREE | section_id | ❌ |
| `idx_books_section` | BTREE | book_section_id | ❌ |

**Foreign Keys:** (2)

| Constraint | Column | References |
|------------|--------|------------|
| `books_ibfk_1` | `section_id` | `sections`.`id` |
| `books_section_id_foreign` | `section_id` | `sections`.`id` |

---

### 📄 `authors`

**Statistics:**
- **Rows:** 3,622
- **Data Size:** 12.09 MB
- **Index Size:** 240 KB
- **Total Size:** 12.33 MB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-10-07 22:07:05

**Columns:** (16)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `full_name` | varchar(255) | NO | UNI | NULL | - |
| `slug` | varchar(255) | YES | - | NULL | - |
| `biography` | text | YES | - | NULL | - |
| `image` | varchar(255) | YES | - | NULL | - |
| `madhhab` | enum('المذهب الحنفي','المذهب المالكي','المذهب الشافعي','المذهب الحنبلي','آخرون') | YES | - | NULL | - |
| `is_living` | tinyint(1) | NO | - | NULL | - |
| `birth_year_type` | enum('gregorian','hijri') | NO | - | gregorian | - |
| `birth_year` | int(11) | YES | - | NULL | - |
| `death_year_type` | enum('gregorian','hijri') | YES | - | gregorian | - |
| `death_year` | int(11) | YES | - | NULL | - |
| `birth_date` | date | YES | - | NULL | - |
| `death_date` | date | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `author_role` | varchar(100) | YES | - | مؤلف | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `authors_full_name_unique` | BTREE | full_name | ✅ |

---

### 📄 `volumes`

**Statistics:**
- **Rows:** 22,269
- **Data Size:** 2.34 MB
- **Index Size:** 6.05 MB
- **Total Size:** 8.39 MB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-15 12:16:30

**Columns:** (8)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `book_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `number` | int(11) | NO | - | NULL | - |
| `title` | varchar(255) | YES | - | NULL | - |
| `page_start` | int(11) | YES | - | NULL | - |
| `page_end` | int(11) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (5)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `volumes_book_id_number_unique` | BTREE | book_id, number | ✅ |
| `volumes_book_id_number_index` | BTREE | book_id, number | ❌ |
| `volumes_book_id_created_at_index` | BTREE | book_id, created_at | ❌ |
| `idx_volumes_book` | BTREE | book_id, number | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `volumes_book_id_foreign` | `book_id` | `books`.`id` |

---

### 📄 `book_extracted_metadata`

**Statistics:**
- **Rows:** 2,065
- **Data Size:** 448 KB
- **Index Size:** 608 KB
- **Total Size:** 1.03 MB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-10-05 07:22:05

**Columns:** (31)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `book_id` | bigint(20) unsigned | NO | UNI | NULL | - |
| `extracted_section_name` | varchar(500) | YES | - | NULL | - |
| `matched_section_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `section_match_confidence` | decimal(3,2) | NO | - | 0.00 | - |
| `extracted_author_name` | varchar(500) | YES | - | NULL | - |
| `extracted_author_death_year` | varchar(20) | YES | - | NULL | - |
| `extracted_author_madhhab` | varchar(100) | YES | - | NULL | - |
| `matched_author_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `author_match_confidence` | decimal(3,2) | NO | - | 0.00 | - |
| `extracted_publisher_name` | varchar(500) | YES | - | NULL | - |
| `extracted_publisher_city` | varchar(200) | YES | - | NULL | - |
| `matched_publisher_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `publisher_match_confidence` | decimal(3,2) | NO | - | 0.00 | - |
| `extracted_edition` | varchar(200) | YES | - | NULL | - |
| `extracted_edition_number` | varchar(50) | YES | - | NULL | - |
| `extracted_year_hijri` | varchar(10) | YES | - | NULL | - |
| `extracted_year_miladi` | varchar(10) | YES | - | NULL | - |
| `extracted_tahqeeq_name` | varchar(500) | YES | - | NULL | - |
| `matched_tahqeeq_author_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `extracted_pages_count` | int(11) | YES | - | NULL | - |
| `extracted_volumes_count` | int(11) | YES | - | NULL | - |
| `is_processed` | tinyint(1) | NO | - | NULL | - |
| `is_applied` | tinyint(1) | NO | MUL | NULL | - |
| `needs_review` | tinyint(1) | NO | MUL | 1 | - |
| `processing_status` | enum('pending','extracted','matched','applied','failed') | NO | MUL | pending | - |
| `error_message` | text | YES | - | NULL | - |
| `extracted_at` | timestamp | YES | - | NULL | - |
| `applied_at` | timestamp | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (9)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `book_extracted_metadata_book_id_unique` | BTREE | book_id | ✅ |
| `book_extracted_metadata_processing_status_index` | BTREE | processing_status | ❌ |
| `book_extracted_metadata_needs_review_index` | BTREE | needs_review | ❌ |
| `book_extracted_metadata_is_applied_index` | BTREE | is_applied | ❌ |
| `book_extracted_metadata_matched_section_id_foreign` | BTREE | matched_section_id | ❌ |
| `book_extracted_metadata_matched_author_id_foreign` | BTREE | matched_author_id | ❌ |
| `book_extracted_metadata_matched_publisher_id_foreign` | BTREE | matched_publisher_id | ❌ |
| `book_extracted_metadata_matched_tahqeeq_author_id_foreign` | BTREE | matched_tahqeeq_author_id | ❌ |

**Foreign Keys:** (5)

| Constraint | Column | References |
|------------|--------|------------|
| `book_extracted_metadata_book_id_foreign` | `book_id` | `books`.`id` |
| `book_extracted_metadata_matched_author_id_foreign` | `matched_author_id` | `authors`.`id` |
| `book_extracted_metadata_matched_publisher_id_foreign` | `matched_publisher_id` | `publishers`.`id` |
| `book_extracted_metadata_matched_section_id_foreign` | `matched_section_id` | `book_sections`.`id` |
| `book_extracted_metadata_matched_tahqeeq_author_id_foreign` | `matched_tahqeeq_author_id` | `authors`.`id` |

---

### 📄 `author_book`

**Statistics:**
- **Rows:** 3,816
- **Data Size:** 288 KB
- **Index Size:** 656 KB
- **Total Size:** 944 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-10-06 22:57:04

**Columns:** (8)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `book_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `author_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `role` | enum('author','co_author','editor','translator','reviewer','commentator') | NO | - | author | - |
| `is_main` | tinyint(1) | NO | - | NULL | - |
| `display_order` | int(11) | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (6)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `author_book_book_id_author_id_unique` | BTREE | book_id, author_id | ✅ |
| `author_book_book_id_is_main_index` | BTREE | book_id, is_main | ❌ |
| `author_book_author_id_role_index` | BTREE | author_id, role | ❌ |
| `idx_author_book_main` | BTREE | book_id, is_main | ❌ |
| `idx_author_book_author` | BTREE | author_id | ❌ |

**Foreign Keys:** (2)

| Constraint | Column | References |
|------------|--------|------------|
| `author_book_author_id_foreign` | `author_id` | `authors`.`id` |
| `author_book_book_id_foreign` | `book_id` | `books`.`id` |

---

### 📄 `publishers`

**Statistics:**
- **Rows:** 1,710
- **Data Size:** 304 KB
- **Index Size:** 208 KB
- **Total Size:** 512 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (13)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `name` | varchar(255) | NO | - | NULL | - |
| `slug` | varchar(255) | YES | UNI | NULL | - |
| `address` | varchar(255) | YES | - | NULL | - |
| `email` | varchar(255) | YES | - | NULL | - |
| `phone` | varchar(255) | YES | - | NULL | - |
| `description` | text | YES | - | NULL | - |
| `website_url` | varchar(255) | YES | - | NULL | - |
| `image` | varchar(255) | YES | - | NULL | - |
| `is_active` | tinyint(1) | NO | - | 1 | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `city` | varchar(255) | YES | - | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `publishers_slug_unique` | BTREE | slug | ✅ |

---

### 📄 `blog_posts`

**Statistics:**
- **Rows:** 20
- **Data Size:** 96 KB
- **Index Size:** 144 KB
- **Total Size:** 240 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (25)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | char(26) | NO | PRI | NULL | - |
| `blog_author_id` | char(36) | YES | MUL | NULL | - |
| `blog_category_id` | char(26) | YES | MUL | NULL | - |
| `is_featured` | tinyint(1) | NO | MUL | NULL | - |
| `title` | varchar(255) | NO | MUL | NULL | - |
| `slug` | varchar(255) | NO | MUL | NULL | - |
| `content_raw` | longtext | NO | - | NULL | - |
| `content_html` | longtext | NO | - | NULL | - |
| `content_overview` | text | YES | - | NULL | - |
| `status` | enum('draft','pending','published','archived') | NO | MUL | draft | - |
| `published_at` | date | YES | MUL | NULL | - |
| `scheduled_at` | timestamp | YES | - | NULL | - |
| `last_published_at` | timestamp | YES | MUL | NULL | - |
| `meta_title` | varchar(60) | YES | - | NULL | - |
| `meta_description` | varchar(160) | YES | - | NULL | - |
| `locale` | varchar(10) | NO | MUL | en | - |
| `options` | longtext | YES | - | NULL | - |
| `view_count` | int(11) | NO | - | NULL | - |
| `comments_count` | int(11) | NO | - | NULL | - |
| `reading_time` | int(11) | YES | - | NULL | - |
| `created_by` | char(36) | YES | - | NULL | - |
| `updated_by` | char(36) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `deleted_at` | timestamp | YES | - | NULL | - |

**Indexes:** (10)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `blog_posts_slug_locale_unique` | BTREE | slug, locale | ✅ |
| `blog_posts_blog_author_id_foreign` | BTREE | blog_author_id | ❌ |
| `blog_posts_blog_category_id_foreign` | BTREE | blog_category_id | ❌ |
| `blog_posts_is_featured_index` | BTREE | is_featured | ❌ |
| `blog_posts_status_index` | BTREE | status | ❌ |
| `blog_posts_published_at_index` | BTREE | published_at | ❌ |
| `blog_posts_last_published_at_index` | BTREE | last_published_at | ❌ |
| `blog_posts_locale_index` | BTREE | locale | ❌ |
| `blog_posts_title_content_overview_fulltext` | FULLTEXT | title, content_overview | ❌ |

**Foreign Keys:** (2)

| Constraint | Column | References |
|------------|--------|------------|
| `blog_posts_blog_author_id_foreign` | `blog_author_id` | `users`.`id` |
| `blog_posts_blog_category_id_foreign` | `blog_category_id` | `blog_categories`.`id` |

---

### 📄 `contact_us`

**Statistics:**
- **Rows:** 100
- **Data Size:** 112 KB
- **Index Size:** 64 KB
- **Total Size:** 176 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (21)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | char(26) | NO | PRI | NULL | - |
| `firstname` | varchar(100) | NO | MUL | NULL | - |
| `lastname` | varchar(100) | NO | - | NULL | - |
| `email` | varchar(255) | NO | MUL | NULL | - |
| `phone` | varchar(30) | YES | - | NULL | - |
| `company` | varchar(150) | YES | - | NULL | - |
| `employees` | enum('1-10','11-50','51-200','201-500','501-1000','1000+') | YES | - | NULL | - |
| `title` | varchar(150) | YES | - | NULL | - |
| `subject` | varchar(255) | NO | - | NULL | - |
| `message` | text | NO | - | NULL | - |
| `status` | enum('new','read','pending','responded','closed') | NO | MUL | new | - |
| `reply_subject` | varchar(255) | YES | - | NULL | - |
| `reply_message` | text | YES | - | NULL | - |
| `replied_at` | timestamp | YES | - | NULL | - |
| `replied_by_user_id` | char(36) | YES | MUL | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `deleted_at` | timestamp | YES | - | NULL | - |
| `ip_address` | varchar(45) | YES | - | NULL | - |
| `user_agent` | varchar(512) | YES | - | NULL | - |
| `metadata` | longtext | YES | - | NULL | - |

**Indexes:** (5)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `contact_us_replied_by_user_id_foreign` | BTREE | replied_by_user_id | ❌ |
| `contact_us_email_index` | BTREE | email | ❌ |
| `contact_us_status_index` | BTREE | status | ❌ |
| `search` | FULLTEXT | firstname, lastname, email, subject, message | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `contact_us_replied_by_user_id_foreign` | `replied_by_user_id` | `users`.`id` |

---

### 📄 `book_indexes`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 144 KB
- **Total Size:** 160 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (16)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `book_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `page_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `chapter_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `volume_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `keyword` | varchar(255) | NO | MUL | NULL | - |
| `normalized_keyword` | varchar(255) | NO | MUL | NULL | - |
| `page_number` | int(11) | NO | MUL | NULL | - |
| `context` | text | YES | - | NULL | - |
| `position_in_page` | int(11) | YES | - | NULL | - |
| `frequency` | int(11) | NO | - | 1 | - |
| `index_type` | enum('keyword','person','place','concept','hadith','verse') | NO | MUL | keyword | - |
| `relevance_score` | float | NO | - | 1 | - |
| `is_auto_generated` | tinyint(1) | NO | - | 1 | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (9)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `book_indexes_page_id_foreign` | BTREE | page_id | ❌ |
| `book_indexes_chapter_id_foreign` | BTREE | chapter_id | ❌ |
| `book_indexes_volume_id_foreign` | BTREE | volume_id | ❌ |
| `book_indexes_book_id_keyword_index` | BTREE | book_id, keyword | ❌ |
| `book_indexes_normalized_keyword_index` | BTREE | normalized_keyword | ❌ |
| `book_indexes_page_number_book_id_index` | BTREE | page_number, book_id | ❌ |
| `book_indexes_index_type_book_id_index` | BTREE | index_type, book_id | ❌ |
| `book_indexes_keyword_context_fulltext` | FULLTEXT | keyword, context | ❌ |

**Foreign Keys:** (4)

| Constraint | Column | References |
|------------|--------|------------|
| `book_indexes_book_id_foreign` | `book_id` | `books`.`id` |
| `book_indexes_chapter_id_foreign` | `chapter_id` | `chapters`.`id` |
| `book_indexes_page_id_foreign` | `page_id` | `pages`.`id` |
| `book_indexes_volume_id_foreign` | `volume_id` | `volumes`.`id` |

---

### 📄 `banner_contents`

**Statistics:**
- **Rows:** 22
- **Data Size:** 16 KB
- **Index Size:** 128 KB
- **Total Size:** 144 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (20)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | char(26) | NO | PRI | NULL | - |
| `banner_category_id` | char(26) | YES | MUL | NULL | - |
| `sort` | smallint(6) | NO | MUL | NULL | - |
| `is_active` | tinyint(1) | NO | MUL | NULL | - |
| `title` | varchar(255) | YES | MUL | NULL | - |
| `description` | varchar(500) | YES | - | NULL | - |
| `click_url` | varchar(255) | YES | - | NULL | - |
| `click_url_target` | varchar(20) | YES | - | _self | - |
| `start_date` | datetime | YES | MUL | NULL | - |
| `end_date` | datetime | YES | MUL | NULL | - |
| `published_at` | datetime | YES | MUL | NULL | - |
| `locale` | varchar(10) | NO | MUL | en | - |
| `options` | longtext | YES | - | NULL | - |
| `impression_count` | bigint(20) unsigned | NO | - | NULL | - |
| `click_count` | bigint(20) unsigned | NO | - | NULL | - |
| `created_by` | char(36) | YES | - | NULL | - |
| `updated_by` | char(36) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `deleted_at` | timestamp | YES | - | NULL | - |

**Indexes:** (9)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `banner_contents_banner_category_id_index` | BTREE | banner_category_id | ❌ |
| `banner_contents_sort_index` | BTREE | sort | ❌ |
| `banner_contents_is_active_index` | BTREE | is_active | ❌ |
| `banner_contents_title_index` | BTREE | title | ❌ |
| `banner_contents_start_date_index` | BTREE | start_date | ❌ |
| `banner_contents_end_date_index` | BTREE | end_date | ❌ |
| `banner_contents_published_at_index` | BTREE | published_at | ❌ |
| `banner_contents_locale_index` | BTREE | locale | ❌ |

---

### 📄 `bok_imports`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 96 KB
- **Total Size:** 112 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (32)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `original_filename` | varchar(255) | NO | - | NULL | - |
| `file_path` | varchar(255) | YES | - | NULL | - |
| `file_size` | bigint(20) | NO | - | NULL | - |
| `file_hash` | varchar(64) | NO | UNI | NULL | - |
| `title` | varchar(255) | NO | - | NULL | - |
| `author` | varchar(255) | YES | - | NULL | - |
| `description` | text | YES | - | NULL | - |
| `language` | varchar(10) | NO | MUL | ar | - |
| `volumes_count` | int(11) | NO | - | NULL | - |
| `chapters_count` | int(11) | NO | - | NULL | - |
| `pages_count` | int(11) | NO | - | NULL | - |
| `estimated_words` | int(11) | NO | - | NULL | - |
| `status` | enum('pending','processing','completed','failed','cancelled') | NO | MUL | pending | - |
| `conversion_options` | longtext | YES | - | NULL | - |
| `analysis_result` | longtext | YES | - | NULL | - |
| `conversion_log` | longtext | YES | - | NULL | - |
| `error_message` | text | YES | - | NULL | - |
| `book_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `is_featured` | tinyint(1) | NO | - | NULL | - |
| `allow_download` | tinyint(1) | NO | - | 1 | - |
| `allow_search` | tinyint(1) | NO | - | 1 | - |
| `is_public` | tinyint(1) | NO | - | 1 | - |
| `started_at` | timestamp | YES | - | NULL | - |
| `completed_at` | timestamp | YES | - | NULL | - |
| `processing_time` | int(11) | YES | - | NULL | - |
| `backup_path` | varchar(255) | YES | - | NULL | - |
| `backup_created` | tinyint(1) | NO | - | NULL | - |
| `user_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `import_source` | varchar(255) | NO | - | web | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (7)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `bok_imports_file_hash_unique` | BTREE | file_hash | ✅ |
| `bok_imports_status_created_at_index` | BTREE | status, created_at | ❌ |
| `bok_imports_user_id_status_index` | BTREE | user_id, status | ❌ |
| `bok_imports_book_id_index` | BTREE | book_id | ❌ |
| `bok_imports_file_hash_index` | BTREE | file_hash | ❌ |
| `bok_imports_language_status_index` | BTREE | language, status | ❌ |

---

### 📄 `blog_categories`

**Statistics:**
- **Rows:** 12
- **Data Size:** 16 KB
- **Index Size:** 80 KB
- **Total Size:** 96 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (15)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | char(26) | NO | PRI | NULL | - |
| `parent_id` | char(26) | YES | MUL | NULL | - |
| `name` | varchar(255) | NO | - | NULL | - |
| `slug` | varchar(255) | NO | UNI | NULL | - |
| `description` | longtext | YES | - | NULL | - |
| `is_active` | tinyint(1) | NO | MUL | NULL | - |
| `meta_title` | varchar(60) | YES | - | NULL | - |
| `meta_description` | varchar(160) | YES | - | NULL | - |
| `locale` | varchar(10) | NO | MUL | en | - |
| `options` | longtext | YES | - | NULL | - |
| `created_by` | char(36) | YES | - | NULL | - |
| `updated_by` | char(36) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `deleted_at` | timestamp | YES | - | NULL | - |

**Indexes:** (6)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `blog_categories_slug_locale_unique` | BTREE | slug, locale | ✅ |
| `blog_categories_slug_unique` | BTREE | slug | ✅ |
| `blog_categories_parent_id_foreign` | BTREE | parent_id | ❌ |
| `blog_categories_is_active_index` | BTREE | is_active | ❌ |
| `blog_categories_locale_index` | BTREE | locale | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `blog_categories_parent_id_foreign` | `parent_id` | `blog_categories`.`id` |

---

### 📄 `footnotes`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 80 KB
- **Total Size:** 96 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (14)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `book_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `page_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `chapter_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `volume_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `footnote_number` | int(11) | YES | MUL | NULL | - |
| `content` | longtext | NO | - | NULL | - |
| `position_in_page` | text | YES | - | NULL | - |
| `reference_text` | text | YES | - | NULL | - |
| `type` | enum('footnote','endnote','margin_note','commentary') | NO | - | footnote | - |
| `order_in_page` | int(11) | NO | - | NULL | - |
| `is_original` | tinyint(1) | NO | - | 1 | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (6)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `footnotes_page_id_foreign` | BTREE | page_id | ❌ |
| `footnotes_volume_id_foreign` | BTREE | volume_id | ❌ |
| `footnotes_book_id_page_id_index` | BTREE | book_id, page_id | ❌ |
| `footnotes_chapter_id_type_index` | BTREE | chapter_id, type | ❌ |
| `footnotes_footnote_number_page_id_index` | BTREE | footnote_number, page_id | ❌ |

**Foreign Keys:** (4)

| Constraint | Column | References |
|------------|--------|------------|
| `footnotes_book_id_foreign` | `book_id` | `books`.`id` |
| `footnotes_chapter_id_foreign` | `chapter_id` | `chapters`.`id` |
| `footnotes_page_id_foreign` | `page_id` | `pages`.`id` |
| `footnotes_volume_id_foreign` | `volume_id` | `volumes`.`id` |

---

### 📄 `page_references`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 80 KB
- **Total Size:** 96 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (11)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `page_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `reference_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `chapter_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `citation_text` | text | YES | - | NULL | - |
| `position_in_page` | int(11) | YES | - | NULL | - |
| `citation_type` | enum('direct_quote','paraphrase','reference','see_also') | NO | MUL | reference | - |
| `context` | text | YES | - | NULL | - |
| `is_primary_source` | tinyint(1) | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (6)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `page_references_page_id_reference_id_position_in_page_unique` | BTREE | page_id, reference_id, position_in_page | ✅ |
| `page_references_reference_id_foreign` | BTREE | reference_id | ❌ |
| `page_references_chapter_id_foreign` | BTREE | chapter_id | ❌ |
| `page_references_page_id_reference_id_index` | BTREE | page_id, reference_id | ❌ |
| `page_references_citation_type_index` | BTREE | citation_type | ❌ |

**Foreign Keys:** (3)

| Constraint | Column | References |
|------------|--------|------------|
| `page_references_chapter_id_foreign` | `chapter_id` | `chapters`.`id` |
| `page_references_page_id_foreign` | `page_id` | `pages`.`id` |
| `page_references_reference_id_foreign` | `reference_id` | `references`.`id` |

---

### 📄 `references`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 80 KB
- **Total Size:** 96 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (17)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `book_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `title` | varchar(500) | NO | MUL | NULL | - |
| `author` | varchar(255) | YES | MUL | NULL | - |
| `publisher` | varchar(255) | YES | - | NULL | - |
| `publication_year` | year(4) | YES | MUL | NULL | - |
| `page_reference` | varchar(100) | YES | - | NULL | - |
| `reference_type` | enum('book','article','website','manuscript','hadith_collection','tafsir','fatwa') | NO | - | book | - |
| `isbn` | varchar(20) | YES | - | NULL | - |
| `url` | text | YES | - | NULL | - |
| `notes` | text | YES | - | NULL | - |
| `edition` | varchar(100) | YES | - | NULL | - |
| `volume_info` | varchar(100) | YES | - | NULL | - |
| `citation_count` | int(11) | NO | - | NULL | - |
| `is_verified` | tinyint(1) | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (5)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `references_book_id_reference_type_index` | BTREE | book_id, reference_type | ❌ |
| `references_author_index` | BTREE | author | ❌ |
| `references_publication_year_index` | BTREE | publication_year | ❌ |
| `references_title_author_publisher_fulltext` | FULLTEXT | title, author, publisher | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `references_book_id_foreign` | `book_id` | `books`.`id` |

---

### 📄 `banner_categories`

**Statistics:**
- **Rows:** 11
- **Data Size:** 16 KB
- **Index Size:** 64 KB
- **Total Size:** 80 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (15)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | char(26) | NO | PRI | NULL | - |
| `parent_id` | char(26) | YES | MUL | NULL | - |
| `name` | varchar(255) | NO | - | NULL | - |
| `slug` | varchar(255) | NO | UNI | NULL | - |
| `description` | longtext | YES | - | NULL | - |
| `is_active` | tinyint(1) | NO | MUL | NULL | - |
| `meta_title` | varchar(255) | YES | - | NULL | - |
| `meta_description` | varchar(255) | YES | - | NULL | - |
| `locale` | varchar(10) | NO | MUL | en | - |
| `options` | longtext | YES | - | NULL | - |
| `created_by` | char(36) | YES | - | NULL | - |
| `updated_by` | char(36) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `deleted_at` | timestamp | YES | - | NULL | - |

**Indexes:** (5)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `banner_categories_slug_unique` | BTREE | slug | ✅ |
| `banner_categories_parent_id_index` | BTREE | parent_id | ❌ |
| `banner_categories_is_active_index` | BTREE | is_active | ❌ |
| `banner_categories_locale_index` | BTREE | locale | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `banner_categories_parent_id_foreign` | `parent_id` | `banner_categories`.`id` |

---

### 📄 `book_metadata`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 64 KB
- **Total Size:** 80 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (12)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `book_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `metadata_key` | varchar(100) | NO | - | NULL | - |
| `metadata_value` | text | NO | - | NULL | - |
| `metadata_type` | enum('dublin_core','custom','shamela_specific','islamic_metadata') | NO | MUL | custom | - |
| `data_type` | varchar(50) | NO | - | string | - |
| `description` | text | YES | - | NULL | - |
| `is_searchable` | tinyint(1) | NO | MUL | 1 | - |
| `is_public` | tinyint(1) | NO | - | 1 | - |
| `display_order` | int(11) | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (5)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `book_metadata_book_id_metadata_key_unique` | BTREE | book_id, metadata_key | ✅ |
| `book_metadata_book_id_metadata_key_index` | BTREE | book_id, metadata_key | ❌ |
| `book_metadata_metadata_type_index` | BTREE | metadata_type | ❌ |
| `book_metadata_is_searchable_is_public_index` | BTREE | is_searchable, is_public | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `book_metadata_book_id_foreign` | `book_id` | `books`.`id` |

---

### 📄 `feedback_complaints`

**Statistics:**
- **Rows:** 4
- **Data Size:** 16 KB
- **Index Size:** 64 KB
- **Total Size:** 80 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-10-09 11:37:01
- **Updated:** 2025-11-22 22:49:14

**Columns:** (13)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `name` | varchar(255) | YES | - | NULL | - |
| `email` | varchar(255) | YES | - | NULL | - |
| `type` | enum('feedback','complaint') | NO | MUL | NULL | - |
| `subject` | varchar(255) | NO | - | NULL | - |
| `message` | text | NO | - | NULL | - |
| `status` | enum('pending','in_progress','resolved') | NO | MUL | pending | - |
| `priority` | enum('low','medium','high') | NO | MUL | medium | - |
| `admin_notes` | text | YES | - | NULL | - |
| `ip_address` | varchar(45) | YES | - | NULL | - |
| `user_agent` | text | YES | - | NULL | - |
| `created_at` | timestamp | YES | MUL | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (5)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `feedback_complaints_type_index` | BTREE | type | ❌ |
| `feedback_complaints_status_index` | BTREE | status | ❌ |
| `feedback_complaints_priority_index` | BTREE | priority | ❌ |
| `feedback_complaints_created_at_index` | BTREE | created_at | ❌ |

**Sample Data:**

```json
[
    {
        "id": 3,
        "name": null,
        "email": null,
        "type": "feedback",
        "subject": "Good presentation",
        "message": "Very nice website presentation",
        "status": "pending",
        "priority": "medium",
        "admin_notes": null,
        "ip_address": "98.221.177.135",
        "user_agent": "Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/141.0.0.0 Safari\/537.36",
        "created_at": "2025-10-11 15:37:36",
        "updated_at": "2025-10-11 15:37:36"
    },
    {
        "id": 4,
        "name": null,
        "email": null,
        "type": "feedback",
        "subject": "لماذا لا يوجد خدمة تنزيل الكتب",
        "message": "لماذا لا يوجد خدمة تنزيل الكتب",
        "status": "pending",
        "priority": "medium",
        "admin_notes": null,
        "ip_address": "109.107.226.91",
        "user_agent": "Mozilla\/5.0 (Linux; Android 10; K) AppleWebKit\/537.36 (KHTML, like Gecko) SamsungBrowser\/28.0 Chrome\/130.0.0.0 Mobile Safari\/537.36",
        "created_at": "2025-10-18 10:40:39",
        "updated_at": "2025-10-18 10:40:39"
    },
    {
        "id": 5,
        "name": null,
        "email": null,
        "type": "feedback",
        "subject": "التطبيق",
        "message": "كيف نسجل تطبيق المكتبة الكاملة",
        "status": "pending",
        "priority": "medium",
        "admin_notes": null,
        "ip_address": "109.107.251.209",
        "user_agent": "Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/141.0.0.0 Safari\/537.36 Edg\/141.0.0.0",
        "created_at": "2025-10-22 09:43:51",
        "updated_at": "2025-10-22 09:43:51"
    },
    {
        "id": 6,
        "name": null,
        "email": null,
        "type": "feedback",
        "subject": "القراءة",
        "message": "كأن القراءة فيها صعبة لو كان الكتاب يملي الشاشة كاملا لكان أريح من الآن",
        "status": "pending",
        "priority": "medium",
        "admin_notes": null,
        "ip_address": "176.29.226.144",
        "user_agent": "Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/142.0.0.0 Safari\/537.36",
        "created_at": "2025-11-22 22:49:14",
        "updated_at": "2025-11-22 22:49:14"
    }
]
```

---

### 📄 `book_sections`

**Statistics:**
- **Rows:** 41
- **Data Size:** 32 KB
- **Index Size:** 32 KB
- **Total Size:** 64 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-10-01 11:21:53

**Columns:** (17)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `name` | varchar(255) | NO | - | NULL | - |
| `description` | text | YES | - | NULL | - |
| `parent_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `sort_order` | int(11) | NO | - | NULL | - |
| `is_active` | tinyint(1) | NO | - | 1 | - |
| `slug` | varchar(255) | YES | UNI | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `logo_path` | varchar(500) | YES | - | NULL | - |
| `icon_type` | enum('upload','url','library','color') | YES | - | NULL | - |
| `icon_url` | varchar(255) | YES | - | NULL | - |
| `icon_name` | varchar(255) | YES | - | NULL | - |
| `icon_color` | varchar(7) | YES | - | NULL | - |
| `icon_size` | enum('sm','md','lg','xl') | NO | - | md | - |
| `icon_custom_size` | int(11) | YES | - | NULL | - |
| `icon_library` | enum('heroicons','fontawesome','custom') | YES | - | NULL | - |

**Indexes:** (3)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `book_sections_slug_unique` | BTREE | slug | ✅ |
| `book_sections_parent_id_foreign` | BTREE | parent_id | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `book_sections_parent_id_foreign` | `parent_id` | `book_sections`.`id` |

---

### 📄 `media`

**Statistics:**
- **Rows:** 5
- **Data Size:** 16 KB
- **Index Size:** 48 KB
- **Total Size:** 64 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (18)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `model_type` | varchar(255) | NO | MUL | NULL | - |
| `model_id` | char(36) | NO | - | NULL | - |
| `uuid` | char(36) | YES | UNI | NULL | - |
| `collection_name` | varchar(255) | NO | - | NULL | - |
| `name` | varchar(255) | NO | - | NULL | - |
| `file_name` | varchar(255) | NO | - | NULL | - |
| `mime_type` | varchar(255) | YES | - | NULL | - |
| `disk` | varchar(255) | NO | - | NULL | - |
| `conversions_disk` | varchar(255) | YES | - | NULL | - |
| `size` | bigint(20) unsigned | NO | - | NULL | - |
| `manipulations` | longtext | NO | - | NULL | - |
| `custom_properties` | longtext | NO | - | NULL | - |
| `generated_conversions` | longtext | NO | - | NULL | - |
| `responsive_images` | longtext | NO | - | NULL | - |
| `order_column` | int(10) unsigned | YES | MUL | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (4)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `media_uuid_unique` | BTREE | uuid | ✅ |
| `media_model_type_model_id_index` | BTREE | model_type, model_id | ❌ |
| `media_order_column_index` | BTREE | order_column | ❌ |

**Sample Data:**

```json
[
    {
        "id": 8,
        "model_type": "App\\Models\\Blog\\Post",
        "model_id": "01K1G4V6NHC6Y1PHKV1PW3YZPD",
        "uuid": "ff863b7d-7d30-4568-bcdb-5d88956d6825",
        "collection_name": "featured",
        "name": "خلفيات-اسلامية-13",
        "file_name": "01K2AZ7JJETVKB97K4ZWPXHNJR.jpg",
        "mime_type": "image\/jpeg",
        "disk": "public",
        "conversions_disk": "public",
        "size": 164248,
        "manipulations": "[]",
        "custom_properties": "[]",
        "generated_conversions": "{\"preview\":true,\"thumbnail\":true,\"medium\":true,\"large\":true}",
        "responsive_images": "{\"media_library_original\":{\"urls\":[\"01K2AZ7JJETVKB97K4ZWPXHNJR___media_library_original_1200_675.jpg\",\"01K2AZ7JJETVKB97K4ZWPXHNJR___media_library_original_1003_564.jpg\",\"01K2AZ7JJETVKB97K4ZWPXHNJR___media_library_original_839_472.jpg\",\"01K2AZ7JJETVKB97K4ZWPXHNJR___media_library_original_702_395.jpg\",\"01K2AZ7JJETVKB97K4ZWPXHNJR___media_library_original_587_330.jpg\",\"01K2AZ7JJETVKB97K4ZWPXHNJR___media_library_original_491_276.jpg\",\"01K2AZ7JJETVKB97K4ZWPXHNJR___media_library_original_411_231.jpg\",\"01K2AZ7JJETVKB97K4ZWPXHNJR___media_library_original_344_194.jpg\"],\"base64svg\":\"data:image\\\/svg+xml;base64,PCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHg9IjAiCiB5PSIwIiB2aWV3Qm94PSIwIDAgMTIwMCA2NzUiPgoJPGltYWdlIHdpZHRoPSIxMjAwIiBoZWlnaHQ9IjY3NSIgeGxpbms6aHJlZj0iZGF0YTppbWFnZS9qcGVnO2Jhc2U2NCwvOWovNEFBUVNrWkpSZ0FCQVFFQVlBQmdBQUQvL2dBK1ExSkZRVlJQVWpvZ1oyUXRhbkJsWnlCMk1TNHdJQ2gxYzJsdVp5QkpTa2NnU2xCRlJ5QjJOaklwTENCa1pXWmhkV3gwSUhGMVlXeHBkSGtLLzlzQVF3QUlCZ1lIQmdVSUJ3Y0hDUWtJQ2d3VURRd0xDd3daRWhNUEZCMGFIeDRkR2h3Y0lDUXVKeUFpTENNY0hDZzNLU3d3TVRRME5COG5PVDA0TWp3dU16UXkvOXNBUXdFSkNRa01Dd3dZRFEwWU1pRWNJVEl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeS84QUFFUWdBRWdBZ0F3RWlBQUlSQVFNUkFmL0VBQjhBQUFFRkFRRUJBUUVCQUFBQUFBQUFBQUFCQWdNRUJRWUhDQWtLQy8vRUFMVVFBQUlCQXdNQ0JBTUZCUVFFQUFBQmZRRUNBd0FFRVFVU0lURkJCaE5SWVFjaWNSUXlnWkdoQ0NOQ3NjRVZVdEh3SkROaWNvSUpDaFlYR0JrYUpTWW5LQ2txTkRVMk56ZzVPa05FUlVaSFNFbEtVMVJWVmxkWVdWcGpaR1ZtWjJocGFuTjBkWFozZUhsNmc0U0Zob2VJaVlxU2s1U1ZscGVZbVpxaW82U2xwcWVvcWFxeXM3UzF0cmU0dWJyQ3c4VEZ4c2ZJeWNyUzA5VFYxdGZZMmRyaDR1UGs1ZWJuNk9ucThmTHo5UFgyOS9qNSt2L0VBQjhCQUFNQkFRRUJBUUVCQVFFQUFBQUFBQUFCQWdNRUJRWUhDQWtLQy8vRUFMVVJBQUlCQWdRRUF3UUhCUVFFQUFFQ2R3QUJBZ01SQkFVaE1RWVNRVkVIWVhFVElqS0JDQlJDa2FHeHdRa2pNMUx3RldKeTBRb1dKRFRoSmZFWEdCa2FKaWNvS1NvMU5qYzRPVHBEUkVWR1IwaEpTbE5VVlZaWFdGbGFZMlJsWm1kb2FXcHpkSFYyZDNoNWVvS0RoSVdHaDRpSmlwS1RsSldXbDVpWm1xS2pwS1dtcDZpcHFyS3p0TFcydDdpNXVzTER4TVhHeDhqSnl0TFQxTlhXMTlqWjJ1TGo1T1htNStqcDZ2THo5UFgyOS9qNSt2L2FBQXdEQVFBQ0VRTVJBRDhBOE1RWk5YN1d5ZTRJQ2lxa1ZiMmwzc2RxTXNBVFV5YlMwTTc2Nm1mZWFmSmE0M2pyVkJoaXQvVkw1THNackVjVVFiYTFCdEo2RVVkV2tQRkZGVVJJUnFqYWlpZ1NQLy9aIj4KCTwvaW1hZ2U+Cjwvc3ZnPg==\"}}",
        "order_column": 1,
        "created_at": "2025-08-10 21:47:19",
        "updated_at": "2025-08-10 21:47:19"
    },
    {
        "id": 9,
        "model_type": "App\\Models\\User",
        "model_id": "dc83b713-5160-4cd3-bb15-522ebef2f172",
        "uuid": "bd511484-583d-4952-8fe1-a3abdb4fa11a",
        "collection_name": "avatars",
        "name": "photo_2025-09-04_13-39-57",
        "file_name": "01K4PQRETMFMQHAVQ9WGNF7WFM.jpg",
        "mime_type": "image\/jpeg",
        "disk": "public",
        "conversions_disk": "public",
        "size": 44389,
        "manipulations": "[]",
        "custom_properties": "[]",
        "generated_conversions": "{\"thumb\":true}",
        "responsive_images": "[]",
        "order_column": 1,
        "created_at": "2025-09-09 07:59:00",
        "updated_at": "2025-09-09 07:59:00"
    },
    {
        "id": 10,
        "model_type": "App\\Models\\Banner\\Content",
        "model_id": "01K1G4V56PXJ8AD06YPY5QG870",
        "uuid": "ef2c866a-41a5-48c5-8a54-4dbad5e73676",
        "collection_name": "banners",
        "name": "WhatsApp Image 2025-03-20 at 1.58.04 PM",
        "file_name": "01K4PQY054QMSH1XT909Q4GGF4.png",
        "mime_type": "image\/png",
        "disk": "public",
        "conversions_disk": "public",
        "size": 2055559,
        "manipulations": "[]",
        "custom_properties": "[]",
        "generated_conversions": "{\"preview\":true,\"thumbnail\":true,\"medium\":true,\"large\":true}",
        "responsive_images": "[]",
        "order_column": 1,
        "created_at": "2025-09-09 08:02:02",
        "updated_at": "2025-09-09 08:02:02"
    },
    {
        "id": 11,
        "model_type": "App\\Models\\Banner\\Content",
        "model_id": "01k1jfgps31j5jzdsbxkyy4ejh",
        "uuid": "4dbae53a-4b02-4956-80b9-be41782af1f3",
        "collection_name": "banners",
        "name": "unsplash_2JIvboGLeho",
        "file_name": "01K4PQZ2VZMD68THJ6NABHM78G.png",
        "mime_type": "image\/png",
        "disk": "public",
        "conversions_disk": "public",
        "size": 2389181,
        "manipulations": "[]",
        "custom_properties": "[]",
        "generated_conversions": "{\"preview\":true,\"thumbnail\":true,\"medium\":true,\"large\":true}",
        "responsive_images": "[]",
        "order_column": 1,
        "created_at": "2025-09-09 08:02:37",
        "updated_at": "2025-09-09 08:02:37"
    },
    {
        "id": 12,
        "model_type": "App\\Models\\Banner\\Content",
        "model_id": "01k1jfjkb9pkbd7s2wtfjb784j",
        "uuid": "7f1e677d-cde3-4f63-849b-341a54cae02b",
        "collection_name": "banners",
        "name": "WhatsApp Image 2025-03-20 at 1.58.04 PM (1)",
        "file_name": "01K4PQZXPCVJ4BG1F7XVF5H98S.png",
        "mime_type": "image\/png",
        "disk": "public",
        "conversions_disk": "public",
        "size": 2113322,
        "manipulations": "[]",
        "custom_properties": "[]",
        "generated_conversions": "{\"preview\":true,\"thumbnail\":true,\"medium\":true,\"large\":true}",
        "responsive_images": "[]",
        "order_column": 1,
        "created_at": "2025-09-09 08:03:05",
        "updated_at": "2025-09-09 08:03:05"
    }
]
```

---

### 📄 `menu_items`

**Statistics:**
- **Rows:** 18
- **Data Size:** 16 KB
- **Index Size:** 48 KB
- **Total Size:** 64 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (11)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `menu_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `parent_id` | bigint(20) unsigned | YES | MUL | NULL | - |
| `linkable_type` | varchar(255) | YES | MUL | NULL | - |
| `linkable_id` | bigint(20) unsigned | YES | - | NULL | - |
| `title` | varchar(255) | NO | - | NULL | - |
| `url` | varchar(255) | YES | - | NULL | - |
| `target` | varchar(10) | NO | - | _self | - |
| `order` | int(11) | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (4)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `menu_items_menu_id_foreign` | BTREE | menu_id | ❌ |
| `menu_items_linkable_type_linkable_id_index` | BTREE | linkable_type, linkable_id | ❌ |
| `menu_items_parent_id_foreign` | BTREE | parent_id | ❌ |

**Foreign Keys:** (2)

| Constraint | Column | References |
|------------|--------|------------|
| `menu_items_menu_id_foreign` | `menu_id` | `menus`.`id` |
| `menu_items_parent_id_foreign` | `parent_id` | `menus`.`id` |

---

### 📄 `settings`

**Statistics:**
- **Rows:** 105
- **Data Size:** 48 KB
- **Index Size:** 16 KB
- **Total Size:** 64 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:38

**Columns:** (7)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `group` | varchar(255) | NO | MUL | NULL | - |
| `name` | varchar(255) | NO | - | NULL | - |
| `locked` | tinyint(1) | NO | - | NULL | - |
| `payload` | longtext | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `settings_group_name_unique` | BTREE | group, name | ✅ |

---

### 📄 `folders`

**Statistics:**
- **Rows:** 1
- **Data Size:** 16 KB
- **Index Size:** 32 KB
- **Total Size:** 48 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (18)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `model_type` | varchar(255) | YES | - | NULL | - |
| `model_id` | varchar(255) | YES | - | NULL | - |
| `name` | varchar(255) | NO | MUL | NULL | - |
| `collection` | varchar(255) | YES | MUL | NULL | - |
| `description` | varchar(255) | YES | - | NULL | - |
| `icon` | varchar(255) | YES | - | NULL | - |
| `color` | varchar(255) | YES | - | NULL | - |
| `is_protected` | tinyint(1) | YES | - | NULL | - |
| `password` | varchar(255) | YES | - | NULL | - |
| `is_hidden` | tinyint(1) | YES | - | NULL | - |
| `is_favorite` | tinyint(1) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `is_public` | tinyint(1) | YES | - | 1 | - |
| `has_user_access` | tinyint(1) | YES | - | NULL | - |
| `user_id` | char(36) | YES | - | NULL | - |
| `user_type` | varchar(255) | YES | - | NULL | - |

**Indexes:** (3)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `folders_name_index` | BTREE | name | ❌ |
| `folders_collection_index` | BTREE | collection | ❌ |

**Sample Data:**

```json
[
    {
        "id": 1,
        "model_type": null,
        "model_id": null,
        "name": "test",
        "collection": "test",
        "description": "test",
        "icon": "heroicon-c-arrow-down-tray",
        "color": "#000000",
        "is_protected": 0,
        "password": null,
        "is_hidden": 0,
        "is_favorite": 0,
        "created_at": "2025-08-12 06:40:26",
        "updated_at": "2025-08-12 06:40:26",
        "is_public": 1,
        "has_user_access": 0,
        "user_id": null,
        "user_type": null
    }
]
```

---

### 📄 `menu_locations`

**Statistics:**
- **Rows:** 5
- **Data Size:** 16 KB
- **Index Size:** 32 KB
- **Total Size:** 48 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (5)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `menu_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `location` | varchar(255) | NO | UNI | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (3)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `menu_locations_location_unique` | BTREE | location | ✅ |
| `menu_locations_menu_id_foreign` | BTREE | menu_id | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `menu_locations_menu_id_foreign` | `menu_id` | `menus`.`id` |

**Sample Data:**

```json
[
    {
        "id": 1,
        "menu_id": 1,
        "location": "header",
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    },
    {
        "id": 2,
        "menu_id": 3,
        "location": "footer",
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    },
    {
        "id": 3,
        "menu_id": 2,
        "location": "footer-2",
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    },
    {
        "id": 4,
        "menu_id": 4,
        "location": "footer-3",
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    },
    {
        "id": 5,
        "menu_id": 5,
        "location": "footer-4",
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    }
]
```

---

### 📄 `personal_access_tokens`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 32 KB
- **Total Size:** 48 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (10)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `tokenable_type` | varchar(255) | NO | MUL | NULL | - |
| `tokenable_id` | char(36) | NO | - | NULL | - |
| `name` | varchar(255) | NO | - | NULL | - |
| `token` | varchar(64) | NO | UNI | NULL | - |
| `abilities` | text | YES | - | NULL | - |
| `last_used_at` | timestamp | YES | - | NULL | - |
| `expires_at` | timestamp | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (3)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `personal_access_tokens_token_unique` | BTREE | token | ✅ |
| `personal_access_tokens_tokenable_type_tokenable_id_index` | BTREE | tokenable_type, tokenable_id | ❌ |

---

### 📄 `users`

**Statistics:**
- **Rows:** 2
- **Data Size:** 16 KB
- **Index Size:** 32 KB
- **Total Size:** 48 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (11)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | char(36) | NO | PRI | NULL | - |
| `username` | varchar(255) | NO | UNI | NULL | - |
| `firstname` | varchar(255) | NO | - | NULL | - |
| `lastname` | varchar(255) | NO | - | NULL | - |
| `email` | varchar(255) | NO | UNI | NULL | - |
| `email_verified_at` | timestamp | YES | - | NULL | - |
| `password` | varchar(255) | NO | - | NULL | - |
| `remember_token` | varchar(100) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `deleted_at` | timestamp | YES | - | NULL | - |

**Indexes:** (3)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `users_username_unique` | BTREE | username | ✅ |
| `users_email_unique` | BTREE | email | ✅ |

**Sample Data:**

```json
[
    {
        "id": "a00d1604-e664-465c-9d68-744be9cba175",
        "username": "salahhaj74",
        "firstname": "الدكتور صلاح",
        "lastname": "أبو الحاج",
        "email": "salahhaj74@yahoo.com",
        "email_verified_at": "2025-10-06 19:57:29",
        "password": "$2y$12$edZSSp87Pz2KPrThAQ0DC.N4O\/l\/iAzKlvyhb\/EnOjC6xSmUujyUa",
        "remember_token": "w2Erx5FkksR9foH9j0vEyJi5Oj63OHtbSCI8YL36EKWKZXRNgqx05W8lFyVg",
        "created_at": "2025-10-06 19:57:24",
        "updated_at": "2025-10-06 19:57:29",
        "deleted_at": null
    },
    {
        "id": "dc83b713-5160-4cd3-bb15-522ebef2f172",
        "username": "superadmin",
        "firstname": "Super",
        "lastname": "Admin",
        "email": "superadmin@starter-kit.com",
        "email_verified_at": "2025-07-31 11:45:36",
        "password": "$2y$12$1Vt1PBrXyW15gzwSAy2YoePvIVWKHmH3bl4pbrH3kSnQpDO8eErhC",
        "remember_token": "lVEaMFG6vTuUqdoJRuiYQxnIckGrT5Dw3U8WzJVANOgqX1Sgs0BRqj3kyTP6",
        "created_at": "2025-07-31 11:45:36",
        "updated_at": "2025-07-31 11:45:36",
        "deleted_at": null
    }
]
```

---

### 📄 `volume_links`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 32 KB
- **Total Size:** 48 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:38

**Columns:** (9)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `book_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `volume_number` | int(11) | NO | - | NULL | - |
| `title` | varchar(255) | NO | - | NULL | - |
| `url` | varchar(500) | NO | - | NULL | - |
| `page_start` | int(11) | YES | - | NULL | - |
| `page_end` | int(11) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | current_timestamp() | - |
| `updated_at` | timestamp | YES | - | current_timestamp() | on update current_timestamp() |

**Indexes:** (3)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `volume_links_book_volume_unique` | BTREE | book_id, volume_number | ✅ |
| `volume_links_book_id_index` | BTREE | book_id | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `volume_links_ibfk_1` | `book_id` | `books`.`id` |

---

### 📄 `book_section`

**Statistics:**
- **Rows:** 2
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb3_general_ci
- **Created:** 2025-09-18 09:16:35

**Columns:** (9)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | int(11) | NO | PRI | NULL | auto_increment |
| `name` | varchar(255) | NO | - | NULL | - |
| `description` | text | YES | - | NULL | - |
| `parent_id` | int(11) | YES | MUL | NULL | - |
| `sort_order` | int(11) | YES | - | NULL | - |
| `is_active` | tinyint(1) | YES | - | NULL | - |
| `slug` | varchar(255) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | current_timestamp() | - |
| `updated_at` | timestamp | YES | - | current_timestamp() | on update current_timestamp() |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `parent_id` | BTREE | parent_id | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `book_section_ibfk_1` | `parent_id` | `book_section`.`id` |

**Sample Data:**

```json
[
    {
        "id": 1,
        "name": "الحنفي",
        "description": null,
        "parent_id": null,
        "sort_order": null,
        "is_active": null,
        "slug": null,
        "created_at": "2025-09-18 07:46:26",
        "updated_at": "2025-09-18 07:46:26"
    },
    {
        "id": 2,
        "name": "محمود",
        "description": null,
        "parent_id": null,
        "sort_order": null,
        "is_active": null,
        "slug": null,
        "created_at": "2025-09-18 07:59:29",
        "updated_at": "2025-09-18 07:59:29"
    }
]
```

---

### 📄 `failed_jobs`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (7)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `uuid` | varchar(255) | NO | UNI | NULL | - |
| `connection` | text | NO | - | NULL | - |
| `queue` | text | NO | - | NULL | - |
| `payload` | longtext | NO | - | NULL | - |
| `exception` | longtext | NO | - | NULL | - |
| `failed_at` | timestamp | NO | - | current_timestamp() | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `failed_jobs_uuid_unique` | BTREE | uuid | ✅ |

---

### 📄 `folder_has_models`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (6)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `model_type` | varchar(255) | NO | - | NULL | - |
| `model_id` | varchar(255) | NO | - | NULL | - |
| `folder_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `folder_has_models_folder_id_foreign` | BTREE | folder_id | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `folder_has_models_folder_id_foreign` | `folder_id` | `folders`.`id` |

---

### 📄 `media_has_models`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:38

**Columns:** (6)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `model_type` | varchar(255) | NO | - | NULL | - |
| `model_id` | varchar(255) | NO | - | NULL | - |
| `media_id` | bigint(20) unsigned | NO | MUL | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `media_has_models_media_id_foreign` | BTREE | media_id | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `media_has_models_media_id_foreign` | `media_id` | `media`.`id` |

---

### 📄 `model_has_permissions`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (3)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `permission_id` | bigint(20) unsigned | NO | PRI | NULL | - |
| `model_type` | varchar(255) | NO | PRI | NULL | - |
| `model_id` | char(36) | NO | PRI | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | permission_id, model_id, model_type | ✅ |
| `model_has_permissions_model_id_model_type_index` | BTREE | model_id, model_type | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `model_has_permissions_permission_id_foreign` | `permission_id` | `permissions`.`id` |

---

### 📄 `model_has_roles`

**Statistics:**
- **Rows:** 2
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (3)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `role_id` | bigint(20) unsigned | NO | PRI | NULL | - |
| `model_type` | varchar(255) | NO | PRI | NULL | - |
| `model_id` | char(36) | NO | PRI | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | role_id, model_id, model_type | ✅ |
| `model_has_roles_model_id_model_type_index` | BTREE | model_id, model_type | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `model_has_roles_role_id_foreign` | `role_id` | `roles`.`id` |

**Sample Data:**

```json
[
    {
        "role_id": 1,
        "model_type": "App\\Models\\User",
        "model_id": "dc83b713-5160-4cd3-bb15-522ebef2f172"
    },
    {
        "role_id": 2,
        "model_type": "App\\Models\\User",
        "model_id": "a00d1604-e664-465c-9d68-744be9cba175"
    }
]
```

---

### 📄 `notifications`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (8)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | char(36) | NO | PRI | NULL | - |
| `type` | varchar(255) | NO | - | NULL | - |
| `notifiable_type` | varchar(255) | NO | MUL | NULL | - |
| `notifiable_id` | char(36) | NO | - | NULL | - |
| `data` | text | NO | - | NULL | - |
| `read_at` | timestamp | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `notifications_notifiable_type_notifiable_id_index` | BTREE | notifiable_type, notifiable_id | ❌ |

---

### 📄 `permissions`

**Statistics:**
- **Rows:** 242
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (5)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `name` | varchar(255) | NO | MUL | NULL | - |
| `guard_name` | varchar(255) | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `permissions_name_guard_name_unique` | BTREE | name, guard_name | ✅ |

---

### 📄 `role_has_permissions`

**Statistics:**
- **Rows:** 394
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (2)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `permission_id` | bigint(20) unsigned | NO | PRI | NULL | - |
| `role_id` | bigint(20) unsigned | NO | PRI | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | permission_id, role_id | ✅ |
| `role_has_permissions_role_id_foreign` | BTREE | role_id | ❌ |

**Foreign Keys:** (2)

| Constraint | Column | References |
|------------|--------|------------|
| `role_has_permissions_permission_id_foreign` | `permission_id` | `permissions`.`id` |
| `role_has_permissions_role_id_foreign` | `role_id` | `roles`.`id` |

---

### 📄 `roles`

**Statistics:**
- **Rows:** 5
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (5)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `name` | varchar(255) | NO | MUL | NULL | - |
| `guard_name` | varchar(255) | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `roles_name_guard_name_unique` | BTREE | name, guard_name | ✅ |

**Sample Data:**

```json
[
    {
        "id": 1,
        "name": "super_admin",
        "guard_name": "web",
        "created_at": "2025-07-31 11:45:34",
        "updated_at": "2025-07-31 11:45:34"
    },
    {
        "id": 2,
        "name": "admin",
        "guard_name": "web",
        "created_at": "2025-07-31 11:45:34",
        "updated_at": "2025-07-31 11:45:34"
    },
    {
        "id": 3,
        "name": "editor",
        "guard_name": "web",
        "created_at": "2025-07-31 11:45:35",
        "updated_at": "2025-07-31 11:45:35"
    },
    {
        "id": 4,
        "name": "author",
        "guard_name": "web",
        "created_at": "2025-07-31 11:45:35",
        "updated_at": "2025-07-31 11:45:35"
    },
    {
        "id": 5,
        "name": "user",
        "guard_name": "web",
        "created_at": "2025-08-06 07:51:59",
        "updated_at": "2025-08-06 07:51:59"
    }
]
```

---

### 📄 `sections`

**Statistics:**
- **Rows:** 3
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb3_general_ci
- **Created:** 2025-09-18 09:16:35

**Columns:** (5)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | int(11) | NO | PRI | NULL | auto_increment |
| `name` | varchar(255) | NO | UNI | NULL | - |
| `description` | text | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | current_timestamp() | - |
| `updated_at` | timestamp | YES | - | current_timestamp() | on update current_timestamp() |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |
| `name` | BTREE | name | ✅ |

**Sample Data:**

```json
[
    {
        "id": 1,
        "name": "الحنفي",
        "description": null,
        "created_at": "2025-09-18 07:26:57",
        "updated_at": "2025-09-18 07:26:57"
    },
    {
        "id": 2,
        "name": "محمود",
        "description": null,
        "created_at": "2025-09-18 08:23:17",
        "updated_at": "2025-09-18 08:23:17"
    },
    {
        "id": 3,
        "name": "أجزاء الرسائل الجامعية الناقصة",
        "description": null,
        "created_at": "2025-09-18 08:45:09",
        "updated_at": "2025-09-18 08:45:09"
    }
]
```

---

### 📄 `taggables`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 16 KB
- **Total Size:** 32 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (3)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `tag_id` | bigint(20) unsigned | NO | PRI | NULL | - |
| `taggable_type` | varchar(255) | NO | PRI | NULL | - |
| `taggable_id` | char(26) | NO | PRI | NULL | - |

**Indexes:** (2)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `taggables_tag_id_taggable_id_taggable_type_unique` | BTREE | tag_id, taggable_id, taggable_type | ✅ |
| `taggables_taggable_type_taggable_id_index` | BTREE | taggable_type, taggable_id | ❌ |

**Foreign Keys:** (1)

| Constraint | Column | References |
|------------|--------|------------|
| `taggables_tag_id_foreign` | `tag_id` | `tags`.`id` |

---

### 📄 `books_backup`

**Statistics:**
- **Rows:** 2
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (17)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | - | NULL | - |
| `shamela_id` | varchar(50) | YES | - | NULL | - |
| `title` | varchar(255) | NO | - | NULL | - |
| `description` | text | YES | - | NULL | - |
| `slug` | varchar(200) | NO | - | NULL | - |
| `cover_image` | varchar(255) | YES | - | NULL | - |
| `pages_count` | int(11) | YES | - | NULL | - |
| `volumes_count` | int(11) | YES | - | 1 | - |
| `status` | enum('draft','review','published','archived') | NO | - | draft | - |
| `visibility` | enum('public','private','restricted') | NO | - | public | - |
| `source_url` | varchar(255) | YES | - | NULL | - |
| `book_section_id` | bigint(20) unsigned | YES | - | NULL | - |
| `publisher_id` | bigint(20) unsigned | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |
| `edition_DATA` | int(11) | YES | - | NULL | - |
| `edition` | int(11) | YES | - | NULL | - |

**Sample Data:**

```json
[
    {
        "id": 47,
        "shamela_id": "39462",
        "title": "مواهب الرحمن في مذهب أبي حنيفة النعمان ت هداية (922)",
        "description": "الكتاب: مواهب الرحمن في مذهب أبي حنيفة النعمان المؤلف: برهان الدين إبراهيم بن موسى بن أبي بكر بن علي الطرابلسي الحنفي (ت922ه) اعتنى به: لجنة الهداية للبحوث للدراسات. الناشر: دار الهداية للبحوث للدراسات، القدس، ساحة المسجد الأقصى. الطبعة الرقمية: الأولى، 1446 هـ. [ترقيم الكتاب موافق للطبعة] الكتاب كامل",
        "slug": "مواهب-الرحمن-في-مذهب-أبي-حنيفة-النعمان-ت-هداية-922",
        "cover_image": null,
        "pages_count": null,
        "volumes_count": 1,
        "status": "published",
        "visibility": "public",
        "source_url": null,
        "book_section_id": null,
        "publisher_id": 13,
        "created_at": "2025-08-14 14:10:54",
        "updated_at": "2025-08-14 14:10:54",
        "edition_DATA": null,
        "edition": null
    },
    {
        "id": 48,
        "shamela_id": "39299",
        "title": "عمدة الرعاية على شرح الوقاية (1304)",
        "description": "الكتاب: عمدة الرعاية على شرح الوقاية المؤلف: عبد الحي بن عبد الحليم اللكنوي الحنفي (ت1304هـ) تنبيه متن الوقاية لبرهان الشريعة (ت673هـ) في الأعلى ويليه شرح الوح الوقاية لصدر الشريعة (ت747هـ) ويليه عمدة الرعاية على شرح الوقاية لمحمد عبد الحي اللكنوي ويليه غاية العناية على عمدة الرعاية للأستاذ الدكتور صلاح أبو الحاج تحقيق وتعليق: الأستاذ الدكتور صلاح بو الحاج الناشر: دار الهداية للبحوث للدراسات، القدس، ساحة المسجد الأقصى. الطبعة الرقمية: الأولى، 1446 هـ. [ترقيم الكتاب موافق للطبعة]",
        "slug": "عمدة-الرعاية-على-شرح-الوقاية-1304",
        "cover_image": null,
        "pages_count": null,
        "volumes_count": 1,
        "status": "published",
        "visibility": "public",
        "source_url": null,
        "book_section_id": null,
        "publisher_id": 13,
        "created_at": "2025-08-14 14:32:25",
        "updated_at": "2025-08-14 14:32:25",
        "edition_DATA": null,
        "edition": null
    }
]
```

---

### 📄 `borrowings`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (3)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (1)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |

---

### 📄 `categories`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:38

**Columns:** (3)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (1)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |

---

### 📄 `menus`

**Statistics:**
- **Rows:** 5
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:38

**Columns:** (5)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `name` | varchar(255) | NO | - | NULL | - |
| `is_visible` | tinyint(1) | NO | - | 1 | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (1)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |

**Sample Data:**

```json
[
    {
        "id": 1,
        "name": "Main",
        "is_visible": 1,
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    },
    {
        "id": 2,
        "name": "Sample Pages",
        "is_visible": 1,
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    },
    {
        "id": 3,
        "name": "Main Footer",
        "is_visible": 1,
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    },
    {
        "id": 4,
        "name": "Resources",
        "is_visible": 1,
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    },
    {
        "id": 5,
        "name": "Community",
        "is_visible": 1,
        "created_at": "2025-07-31 11:46:15",
        "updated_at": "2025-07-31 11:46:15"
    }
]
```

---

### 📄 `migrations`

**Statistics:**
- **Rows:** 69
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (3)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | int(10) unsigned | NO | PRI | NULL | auto_increment |
| `migration` | varchar(255) | NO | - | NULL | - |
| `batch` | int(11) | NO | - | NULL | - |

**Indexes:** (1)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |

---

### 📄 `password_reset_tokens`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:39

**Columns:** (3)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `email` | varchar(255) | NO | PRI | NULL | - |
| `token` | varchar(255) | NO | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |

**Indexes:** (1)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | email | ✅ |

---

### 📄 `reservations`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:38

**Columns:** (3)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (1)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |

---

### 📄 `table_settings`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-15 10:11:28

**Columns:** (6)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `user_id` | bigint(20) | NO | - | NULL | - |
| `resource` | varchar(255) | NO | - | NULL | - |
| `styles` | longtext | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (1)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |

---

### 📄 `tags`

**Statistics:**
- **Rows:** 0
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:38

**Columns:** (7)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| `name` | longtext | NO | - | NULL | - |
| `slug` | longtext | NO | - | NULL | - |
| `type` | varchar(255) | YES | - | NULL | - |
| `order_column` | int(11) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Indexes:** (1)

| Index Name | Type | Columns | Unique |
|------------|------|---------|--------|
| `PRIMARY` | BTREE | id | ✅ |

---

### 📄 `volumes_backup`

**Statistics:**
- **Rows:** 9
- **Data Size:** 16 KB
- **Index Size:** 0 B
- **Total Size:** 16 KB
- **Engine:** InnoDB
- **Collation:** utf8mb4_unicode_ci
- **Created:** 2025-09-04 19:36:40

**Columns:** (8)

| Column | Type | Null | Key | Default | Extra |
|--------|------|------|-----|---------|-------|
| `id` | bigint(20) unsigned | NO | - | NULL | - |
| `book_id` | bigint(20) unsigned | NO | - | NULL | - |
| `number` | int(11) | NO | - | NULL | - |
| `title` | varchar(255) | YES | - | NULL | - |
| `page_start` | int(11) | YES | - | NULL | - |
| `page_end` | int(11) | YES | - | NULL | - |
| `created_at` | timestamp | YES | - | NULL | - |
| `updated_at` | timestamp | YES | - | NULL | - |

**Sample Data:**

```json
[
    {
        "id": 76,
        "book_id": 47,
        "number": 1,
        "title": "المجلد 1",
        "page_start": 7,
        "page_end": 227,
        "created_at": "2025-08-14 14:10:54",
        "updated_at": "2025-08-14 14:10:54"
    },
    {
        "id": 77,
        "book_id": 48,
        "number": 1,
        "title": "المجلد 1",
        "page_start": null,
        "page_end": null,
        "created_at": "2025-08-14 14:32:25",
        "updated_at": "2025-08-14 14:32:25"
    },
    {
        "id": 78,
        "book_id": 48,
        "number": 2,
        "title": "المجلد 2",
        "page_start": null,
        "page_end": null,
        "created_at": "2025-08-14 14:32:25",
        "updated_at": "2025-08-14 14:32:25"
    },
    {
        "id": 79,
        "book_id": 48,
        "number": 3,
        "title": "المجلد 3",
        "page_start": null,
        "page_end": null,
        "created_at": "2025-08-14 14:32:25",
        "updated_at": "2025-08-14 14:32:25"
    },
    {
        "id": 80,
        "book_id": 48,
        "number": 4,
        "title": "المجلد 4",
        "page_start": null,
        "page_end": null,
        "created_at": "2025-08-14 14:32:25",
        "updated_at": "2025-08-14 14:32:25"
    }
]
```

---

## 💡 Recommendations for New Project

### ✅ Tables to Keep (Core Data)

Based on the BMS v1 analysis, these are essential tables:

- ✅ `books` - 12,009 rows, 14.53 MB
- ✅ `authors` - 3,622 rows, 12.33 MB
- ✅ `author_book` - 3,816 rows, 944 KB
- ✅ `pages` - 5,024,544 rows, 17.99 GB
- ✅ `chapters` - 1,638,726 rows, 713.52 MB
- ✅ `volumes` - 22,269 rows, 8.39 MB
- ✅ `book_sections` - 41 rows, 64 KB
- ✅ `publishers` - 1,710 rows, 512 KB
- ✅ `users` - 2 rows, 48 KB
- ✅ `roles` - 5 rows, 32 KB
- ✅ `permissions` - 242 rows, 32 KB
- ✅ `model_has_roles` - 2 rows, 32 KB
- ✅ `model_has_permissions` - 0 rows, 32 KB
- ✅ `role_has_permissions` - 394 rows, 32 KB

### ⚠️ Tables to Review

Tables that might need cleanup or optimization:

- ⚠️ `activity_log` - Consider archiving old records (14,653 rows)

### 🗑️ Tables to Consider Removing

Empty or unnecessary tables for fresh rebuild:

- 🗑️ `book_indexes` (0 rows)
- 🗑️ `bok_imports` (0 rows)
- 🗑️ `footnotes` (0 rows)
- 🗑️ `page_references` (0 rows)
- 🗑️ `references` (0 rows)
- 🗑️ `book_metadata` (0 rows)
- 🗑️ `personal_access_tokens` (0 rows)
- 🗑️ `volume_links` (0 rows)
- 🗑️ `failed_jobs` (0 rows)
- 🗑️ `folder_has_models` (0 rows)
- 🗑️ `media_has_models` (0 rows)
- 🗑️ `model_has_permissions` (0 rows)
- 🗑️ `notifications` (0 rows)
- 🗑️ `taggables` (0 rows)
- 🗑️ `borrowings` (0 rows)
- 🗑️ `categories` (0 rows)
- 🗑️ `password_reset_tokens` (0 rows)
- 🗑️ `reservations` (0 rows)
- 🗑️ `table_settings` (0 rows)
- 🗑️ `tags` (0 rows)

### 📊 Migration Strategy

**Approach 1: Keep Existing Database (Recommended)**
1. Clean up unnecessary tables
2. Create fresh migrations matching existing schema
3. Keep all production data intact
4. Add new tables as needed for Laravel 12 + Filament v4

**Approach 2: Fresh Database with Data Import**
1. Create new database with optimized schema
2. Export core data (books, authors, pages)
3. Import into new structure
4. Reindex Elasticsearch

---

## 🚀 Next Steps

1. Review this report
2. Identify tables to keep/remove
3. Create Laravel migrations for existing schema
4. Build Filament resources for core tables
5. Migrate any schema improvements

---

*Report generated by automated database analyzer*
