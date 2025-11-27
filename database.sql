-- Base de données pour le Quiz Interactif
CREATE DATABASE IF NOT EXISTS quiz_interactif CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quiz_interactif;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des quiz (thèmes)
CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_key VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_quiz_key (quiz_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des questions
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('qcm', 'truefalse') NOT NULL,
    time_limit INT DEFAULT 30,
    explanation TEXT,
    difficulty ENUM('facile', 'moyen', 'difficile') DEFAULT 'moyen',
    points INT DEFAULT 10,
    display_order INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des choix de réponse
CREATE TABLE IF NOT EXISTS choices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    choice_text VARCHAR(255) NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_question_id (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des scores/résultats
CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    time_spent INT NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_user_quiz (user_id, quiz_id),
    INDEX idx_percentage (percentage),
    INDEX idx_completed (completed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réponses utilisateur (pour statistiques détaillées)
CREATE TABLE IF NOT EXISTS user_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    score_id INT NOT NULL,
    question_id INT NOT NULL,
    choice_id INT,
    is_correct TINYINT(1) NOT NULL,
    time_spent INT NOT NULL,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (score_id) REFERENCES scores(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (choice_id) REFERENCES choices(id) ON DELETE SET NULL,
    INDEX idx_score_id (score_id),
    INDEX idx_question_id (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des quiz (thèmes)
INSERT INTO quizzes (quiz_key, title, description, icon) VALUES
('informatique', '💻 Quiz Informatique', 'Testez vos connaissances en informatique et technologies', 'fi-rr-laptop'),
('scientifique', '🔬 Quiz Scientifique', 'Découvrez les mystères de la science', 'fi-rr-flask'),
('histoire', '🏛️ Quiz Histoire', 'Voyagez à travers les grandes périodes historiques', 'fi-rr-landmark'),
('culture', '🎭 Quiz Culture Générale', 'Testez votre culture générale dans tous les domaines', 'fi-rr-brain');

-- Insertion des questions du Quiz Informatique
INSERT INTO questions (quiz_id, question_text, question_type, time_limit, explanation, difficulty, display_order) VALUES
(1, 'Que signifie l''acronyme CPU ?', 'qcm', 30, 'CPU signifie Central Processing Unit, le processeur principal d''un ordinateur.', 'facile', 1),
(1, 'Linux est un système d''exploitation open source.', 'truefalse', 15, 'Linux est effectivement un système d''exploitation open source très populaire.', 'facile', 2),
(1, 'Quelle est l''unité de base de stockage en informatique ?', 'qcm', 25, 'Le bit (binary digit) est l''unité fondamentale en informatique.', 'facile', 3),
(1, 'Le protocole HTTPS est sécurisé.', 'truefalse', 15, 'HTTPS utilise le chiffrement SSL/TLS pour sécuriser les communications.', 'facile', 4),
(1, 'Qu''est-ce que la RAM ?', 'qcm', 30, 'RAM = Random Access Memory, mémoire volatile pour les données en cours d''utilisation.', 'moyen', 5),
(1, 'JavaScript est le langage principal utilisé pour l''IA.', 'truefalse', 15, 'Python est le langage principal pour l''IA, JavaScript est pour le web.', 'moyen', 6),
(1, 'Que signifie DNS ?', 'qcm', 25, 'DNS convertit les noms de domaine en adresses IP.', 'moyen', 7),
(1, 'Un octet contient toujours 8 bits.', 'truefalse', 15, 'Un octet est par définition composé de 8 bits.', 'facile', 8),
(1, 'Quel type de cyberattaque utilise des emails frauduleux ?', 'qcm', 30, 'Le phishing utilise des emails frauduleux pour voler des informations.', 'moyen', 9),
(1, 'Le Cloud Computing nécessite un disque dur local.', 'truefalse', 15, 'Le cloud computing utilise des serveurs distants, pas de stockage local obligatoire.', 'facile', 10);

-- Choix pour question 1 (CPU)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(1, 'Central Processing Unit', 1, 1),
(1, 'Computer Personal Unit', 0, 2),
(1, 'Central Program Utility', 0, 3),
(1, 'Core Processing Unit', 0, 4);

-- Choix pour question 2 (Linux)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(2, 'Vrai', 1, 1),
(2, 'Faux', 0, 2);

-- Choix pour question 3 (Unité de stockage)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(3, 'Bit', 1, 1),
(3, 'Byte', 0, 2),
(3, 'Megabyte', 0, 3),
(3, 'Kilobyte', 0, 4);

-- Choix pour question 4 (HTTPS)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(4, 'Vrai', 1, 1),
(4, 'Faux', 0, 2);

-- Choix pour question 5 (RAM)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(5, 'Mémoire de stockage permanente', 0, 1),
(5, 'Mémoire vive temporaire', 1, 2),
(5, 'Un processeur', 0, 3),
(5, 'Un système d''exploitation', 0, 4);

-- Choix pour question 6 (JavaScript IA)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(6, 'Vrai', 0, 1),
(6, 'Faux', 1, 2);

-- Choix pour question 7 (DNS)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(7, 'Data Network System', 0, 1),
(7, 'Domain Name System', 1, 2),
(7, 'Digital Network Service', 0, 3),
(7, 'Domain Network Security', 0, 4);

-- Choix pour question 8 (Octet)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(8, 'Vrai', 1, 1),
(8, 'Faux', 0, 2);

-- Choix pour question 9 (Phishing)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(9, 'Malware', 0, 1),
(9, 'DDoS', 0, 2),
(9, 'Phishing', 1, 3),
(9, 'Ransomware', 0, 4);

-- Choix pour question 10 (Cloud Computing)
INSERT INTO choices (question_id, choice_text, is_correct, display_order) VALUES
(10, 'Vrai', 0, 1),
(10, 'Faux', 1, 2);

-- Insertion d'un utilisateur de test (mot de passe: test123)
INSERT INTO users (username, email, password_hash) VALUES
('joueur_test', 'test@quiz.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Vue pour le classement global
CREATE OR REPLACE VIEW leaderboard AS
SELECT 
    u.username,
    q.title as quiz_title,
    s.score,
    s.total_questions,
    s.percentage,
    s.time_spent,
    s.completed_at,
    RANK() OVER (PARTITION BY s.quiz_id ORDER BY s.percentage DESC, s.time_spent ASC) as rank_in_quiz
FROM scores s
JOIN users u ON s.user_id = u.id
JOIN quizzes q ON s.quiz_id = q.id
ORDER BY s.percentage DESC, s.time_spent ASC;

-- Vue pour les statistiques utilisateur
CREATE OR REPLACE VIEW user_statistics AS
SELECT 
    u.id as user_id,
    u.username,
    COUNT(DISTINCT s.quiz_id) as quizzes_completed,
    COUNT(s.id) as total_attempts,
    AVG(s.percentage) as avg_percentage,
    MAX(s.percentage) as best_percentage,
    SUM(s.time_spent) as total_time_spent
FROM users u
LEFT JOIN scores s ON u.id = s.user_id
GROUP BY u.id, u.username;