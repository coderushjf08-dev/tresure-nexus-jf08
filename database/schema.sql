-- Treasure Nexus Database Schema
-- Compatible with phpMyAdmin

CREATE DATABASE IF NOT EXISTS treasure_nexus;
USE treasure_nexus;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    level INT DEFAULT 1,
    total_score INT DEFAULT 0,
    hunts_completed INT DEFAULT 0,
    avg_time VARCHAR(10) DEFAULT '00:00',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hunts table
CREATE TABLE hunts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    creator_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    difficulty ENUM('Easy', 'Medium', 'Hard', 'Extreme') NOT NULL,
    privacy ENUM('public', 'private') DEFAULT 'public',
    access_code VARCHAR(20) NULL,
    estimated_duration INT NOT NULL, -- in minutes
    total_puzzles INT DEFAULT 0,
    total_players INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Clues/Puzzles table
CREATE TABLE clues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hunt_id INT NOT NULL,
    clue_number INT NOT NULL,
    type ENUM('text', 'image', 'audio') NOT NULL,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL, -- puzzle content/question
    content_file VARCHAR(255) NULL, -- for image/audio files
    answer VARCHAR(255) NOT NULL,
    hint TEXT NULL,
    hint_penalty INT DEFAULT 300, -- time penalty in seconds
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hunt_id) REFERENCES hunts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_hunt_clue (hunt_id, clue_number)
);

-- Hunt attempts/sessions
CREATE TABLE attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    hunt_id INT NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    current_clue INT DEFAULT 1,
    hints_used INT DEFAULT 0,
    is_completed BOOLEAN DEFAULT FALSE,
    total_time INT NULL, -- in seconds
    score INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hunt_id) REFERENCES hunts(id) ON DELETE CASCADE
);

-- Individual clue attempts
CREATE TABLE clue_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    clue_id INT NOT NULL,
    user_answer VARCHAR(255) NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    time_taken INT NOT NULL, -- in seconds
    hint_used BOOLEAN DEFAULT FALSE,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (clue_id) REFERENCES clues(id) ON DELETE CASCADE
);

-- Leaderboard (computed view for performance)
CREATE TABLE leaderboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    hunt_id INT NOT NULL,
    total_time INT NOT NULL,
    score INT NOT NULL,
    rank_position INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hunt_id) REFERENCES hunts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_hunt (user_id, hunt_id)
);

-- Hunt ratings
CREATE TABLE hunt_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    hunt_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hunt_id) REFERENCES hunts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_hunt_rating (user_id, hunt_id)
);

-- Insert sample data
INSERT INTO users (username, email, password, level, total_score, hunts_completed, avg_time) VALUES
('QuantumSolver', 'quantum@nexus.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 25, 15420, 87, '12:34'),
('CyberNinja', 'cyber@nexus.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 24, 14890, 82, '13:45'),
('CodeBreaker', 'code@nexus.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 23, 14356, 79, '14:12'),
('MatrixMaster', 'matrix@nexus.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 22, 13987, 75, '15:23');

INSERT INTO hunts (creator_id, title, description, difficulty, estimated_duration, total_puzzles, total_players, average_rating) VALUES
(1, 'Cyber Labyrinth', 'Navigate through digital mazes and decode encrypted messages in this mind-bending cyber adventure.', 'Hard', 45, 12, 234, 4.8),
(2, 'Neon Mysteries', 'Uncover secrets hidden in plain sight across a futuristic cityscape filled with holographic clues.', 'Medium', 30, 8, 156, 4.6),
(3, 'Matrix Protocol', 'Hack into the mainframe and solve algorithmic puzzles to prevent a digital apocalypse.', 'Extreme', 90, 20, 89, 4.9),
(4, 'Quantum Paradox', 'Experience puzzles that exist in multiple states simultaneously in this quantum-themed hunt.', 'Easy', 20, 6, 345, 4.4);

INSERT INTO clues (hunt_id, clue_number, type, title, content, answer) VALUES
(1, 1, 'text', 'Entry Protocol', 'In the realm of zeros and ones, what comes after 1010?', '1011'),
(1, 2, 'text', 'Cyber Lock', 'I am the key that unlocks digital doors, but I have no physical form. What am I?', 'password'),
(2, 1, 'text', 'Neon Riddle', 'I glow in the dark but cast no shadow. In the city of lights, I guide your way. What am I?', 'hologram'),
(3, 1, 'text', 'Matrix Entry', 'There is no spoon, but there is a...', 'choice');