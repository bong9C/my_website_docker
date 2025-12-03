-- 오류가 나면(추가)
CREATE DATABASE IF NOT EXISTS mysite
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;
CREATE USER IF NOT EXISTS 'poly'@'%' IDENTIFIED BY '1234';
GRANT ALL PRIVILEGES ON mysite.* TO 'poly'@'%';
FLUSH PRIVILEGES;

-- DB/사용자는 .env로 이미 생성되므로 여기서는 테이블만 준비

-- 1. posts 테이블: professor_name 컬럼 추가
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. post_files 테이블: 새 테이블
CREATE TABLE IF NOT EXISTS post_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_path VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
