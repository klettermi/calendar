CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100),
  role ENUM('user', 'admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS schedules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  type ENUM('일반','교육','세미나','회식') NOT NULL,
  title VARCHAR(255) NOT NULL,
  location VARCHAR(255),
  start_time DATETIME NOT NULL,
  end_time DATETIME NOT NULL,
  participants TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$YCfrCDaZbaKRhmOLGa.JeuHvfQE5k1gX7.j9.RGTUH16Nyqcr0PK2', 'admin@example.com', 'admin');