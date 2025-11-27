-- 오류가 나면(추가)
CREATE DATABASE IF NOT EXISTS mysite
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;
CREATE USER IF NOT EXISTS 'mysiteuser'@'%' IDENTIFIED BY 'StrongPassw0rd!';
GRANT ALL PRIVILEGES ON mysite.* TO 'mysiteuser'@'%';
FLUSH PRIVILEGES;

-- DB/사용자는 .env로 이미 생성되므로 여기서는 테이블만 준비
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ⭐️ 새로운 post_files 테이블 추가: 게시글과 첨부 파일을 연결합니다.
CREATE TABLE IF NOT EXISTS post_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_path VARCHAR(255) NOT NULL, -- 서버에 저장된 파일의 고유 경로 (예: uploads/abcdef123.jpg)
    mime_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- post_id가 삭제되면 이 파일 메타데이터도 함께 삭제됩니다.
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
