-- Create database
CREATE DATABASE IF NOT EXISTS rew_wrestling;
USE rew_wrestling;

-- Admin users table
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Wrestlers table
CREATE TABLE IF NOT EXISTS wrestlers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    real_name VARCHAR(100),
    bio TEXT,
    height VARCHAR(20),
    weight VARCHAR(20),
    signature_moves TEXT,
    image VARCHAR(255),
    twitter VARCHAR(100),
    instagram VARCHAR(100),
    facebook VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    poster_image VARCHAR(255),
    status ENUM('upcoming', 'past', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Match types table
CREATE TABLE IF NOT EXISTS match_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Matches table
CREATE TABLE IF NOT EXISTS matches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    match_type_id INT NOT NULL,
    stipulations TEXT,
    winner_id INT,
    match_order INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (match_type_id) REFERENCES match_types(id),
    FOREIGN KEY (winner_id) REFERENCES wrestlers(id)
);

-- Match participants table
CREATE TABLE IF NOT EXISTS match_participants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT NOT NULL,
    wrestler_id INT NOT NULL,
    team_number INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (wrestler_id) REFERENCES wrestlers(id)
);

-- Belts/Titles table
CREATE TABLE IF NOT EXISTS belts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Title history table
CREATE TABLE IF NOT EXISTS title_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    belt_id INT NOT NULL,
    wrestler_id INT NOT NULL,
    won_date DATETIME NOT NULL,
    lost_date DATETIME,
    reign_number INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (belt_id) REFERENCES belts(id),
    FOREIGN KEY (wrestler_id) REFERENCES wrestlers(id)
);

-- Homepage slider table
CREATE TABLE IF NOT EXISTS sliders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    order_number INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admins (username, password, email, full_name) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@rew-wrestling.com', 'System Administrator');

-- Insert some default match types
INSERT INTO match_types (name, description) VALUES
('Singles Match', 'One-on-one wrestling match'),
('Tag Team Match', 'Two-on-two wrestling match'),
('Triple Threat', 'Three wrestlers compete against each other'),
('Fatal Four Way', 'Four wrestlers compete against each other'),
('Ladder Match', 'Match where wrestlers must climb a ladder to retrieve a prize'),
('Tables Match', 'Match where wrestlers must put their opponent through a table'),
('Steel Cage Match', 'Match contested inside a steel cage'),
('Royal Rumble', 'Multiple wrestlers enter at timed intervals'); 