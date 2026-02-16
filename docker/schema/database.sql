-- charset / collation
SET NAMES utf8mb4;
SET SESSION collation_connection = 'utf8mb4_0900_ai_ci';

-- database
CREATE DATABASE IF NOT EXISTS backend
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_0900_ai_ci;

USE backend;

-- =========================================
-- users
-- =========================================
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    role ENUM ('client', 'designer', 'manager') NOT NULL DEFAULT 'client',
    gender VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    birth DATE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO users (account, password, user_name, role, gender, phone, birth)
VALUES
('designer1',SHA2('1111',256), '디자이너1', 'designer', 'M', '010-3333-3333', '1995-03-03'),
('designer2',SHA2('2222',256), '디자이너2', 'designer', 'F', '010-5555-5555', '1993-05-05');

-- =========================================
-- salons
-- =========================================
CREATE TABLE IF NOT EXISTS salons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    image_key VARCHAR(255) NOT NULL,
    introduction TEXT NOT NULL,
    information JSON NOT NULL,
    map VARCHAR(255) NOT NULL,
    traffic JSON NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO salons (image, image_key, introduction, information, map, traffic) VALUES (
    "https://pub-08298820ca884cc49d536c1b0ce8b7c4.r2.dev/salon/1.jpg",
    "salon/1.jpg",
    "저희 살롱은 고객 개개인의 스타일을 존중하며 맞춤형 서비스를 제공합니다.",
    JSON_OBJECT(
        "address", "대구광역시 북구 복현로 35",
        "opening_hour", "10:00 - 19:00",
        "holiday", "일요일",
        "phone", "010-4819-7975"
    ),
    "https://pub-08298820ca884cc49d536c1b0ce8b7c4.r2.dev/salon/1.png",
    JSON_OBJECT(
        "bus", "706, 719, 730, 북구2",
        "parking", "영진전문대 정문 주차장 이용 가능 (방문객 30분 무료)",
        "directions", "대구 1호선 칠곡경대병원역 3번 출구 기준 도보 10분"
    )
);

-- =========================================
-- services
-- =========================================
CREATE TABLE IF NOT EXISTS services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_min INT NOT NULL
);

INSERT INTO services (service_name, price, duration_min) VALUES
('MEN CUT', 12000, 50),
('WOMEN CUT', 15000, 60),
('DRY CUT', 15000, 60),
('KIDS CUT', 8000, 40),
('BASIC PERM', 50000, 90),
('DIGITAL PERM', 80000, 120),
('SETTING PERM', 90000, 120),
('VOLUME PERM', 70000, 100),
('DOWN PERM', 30000, 40),
('COLOR BASIC', 50000, 90),
('COLOR FULL', 70000, 100),
('BLEACHING', 90000, 120),
('RETOUCH COLOR', 40000, 70),
('GRAY COVER COLOR', 50000, 80),
('KERATIN TREATMENT', 60000, 60),
('PROTEIN CARE', 40000, 50),
('MOISTURE CARE', 35000, 45),
('SCALP CARE', 30000, 40),
('BLOW DRY', 15000, 30),
('IRON STYLING', 20000, 40),
('UP STYLE', 30000, 60);

-- =========================================
-- hair_styles
-- =========================================
CREATE TABLE IF NOT EXISTS hair_styles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    image_key VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

-- =========================================
-- designers
-- =========================================
CREATE TABLE IF NOT EXISTS designers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    image VARCHAR(255) NOT NULL,
    image_key VARCHAR(255) NOT NULL,
    experience INT NOT NULL,
    good_at VARCHAR(255) NOT NULL,
    personality VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_designers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================================
-- news
-- =========================================
CREATE TABLE IF NOT EXISTS news (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    file VARCHAR(255),
    file_key VARCHAR(255),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO news (title, content) VALUES
('haechan', 'hi'),
('mark','hello'),
('jisung', 'hi'),
('haechan', 'hello'),
('mark', 'hi'),
('jisung', 'hello'),
('haechan', 'hi');

-- =========================================
-- reservations
--  - client_id -> users.id
--  - designer_id -> designers.id  ★ここがあなたの希望
-- =========================================
CREATE TABLE IF NOT EXISTS reservations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED NOT NULL,
    designer_id BIGINT UNSIGNED NOT NULL,
    requirement TEXT,
    day DATE NOT NULL,
    start_at TIME NOT NULL,
    end_at TIME NOT NULL,
    status ENUM('pending','confirmed', 'checked_in', 'completed', 'cancelled', 'no_show')
      NOT NULL DEFAULT 'pending',
    cancelled_at DATETIME,
    cancel_reason TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservations_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_reservations_designer FOREIGN KEY (designer_id) REFERENCES designers(id) ON DELETE CASCADE
);

-- =========================================
-- reservation_service (pivot)
-- =========================================
CREATE TABLE IF NOT EXISTS reservation_service (
    reservation_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY(reservation_id, service_id),
    CONSTRAINT fk_rs_reservation FOREIGN KEY (reservation_id) REFERENCES reservations(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_rs_service FOREIGN KEY (service_id) REFERENCES services(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- =========================================
-- time_offs
--  - designer_id -> designers.id
-- =========================================
CREATE TABLE IF NOT EXISTS time_offs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    designer_id BIGINT UNSIGNED NOT NULL,
    start_at DATE NOT NULL,
    end_at DATE NOT NULL,
    CONSTRAINT fk_timeoffs_designer FOREIGN KEY (designer_id) REFERENCES designers(id) ON DELETE CASCADE
);

-- =========================================
-- event: delete old time_offs
-- =========================================
DROP EVENT IF EXISTS delete_old_time_offs;

CREATE EVENT delete_old_time_offs
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
    DELETE FROM time_offs
    WHERE end_at < CURDATE();
