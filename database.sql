-- ============================================
-- GameTracker - SQL skript pre vytvorenie DB
-- ============================================

-- Vytvorenie databazy
CREATE DATABASE IF NOT EXISTS gametracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gametracker;

-- ============================================
-- Tabulka: genres (zaner hier)
-- ============================================
CREATE TABLE IF NOT EXISTS genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(255)
);

-- ============================================
-- Tabulka: games (hry)
-- ============================================
CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    genre_id INT NOT NULL,
    platform VARCHAR(50) NOT NULL,
    release_year YEAR,
    status ENUM('chcem hrat', 'hrám', 'dohraná', 'nedohraná') NOT NULL DEFAULT 'chcem hrat',
    rating TINYINT DEFAULT NULL CHECK (rating >= 1 AND rating <= 10),
    note TEXT,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE RESTRICT
);

-- ============================================
-- Dummy data - zanre
-- ============================================
INSERT INTO genres (name, description) VALUES
('RPG', 'Roleplaying hry s príbehom a vývojom postavy'),
('FPS', 'Strieľačky z pohľadu prvej osoby'),
('Strategy', 'Strategické hry, real-time aj turn-based'),
('Action', 'Akčné hry s rýchlym gameplayom'),
('Sports', 'Športové simulátory'),
('Horror', 'Hororové a survival hry'),
('Platformer', 'Skákačky a plošinovky');

-- ============================================
-- Dummy data - hry
-- ============================================
INSERT INTO games (title, genre_id, platform, release_year, status, rating, note) VALUES
('The Witcher 3', 1, 'PC', 2015, 'dohraná', 10, 'Najlepšia hra akú som hral, príbeh je fenomenálny'),
('Cyberpunk 2077', 1, 'PC', 2020, 'hrám', 8, 'Po patchoch už celkom ok, Phantom Liberty je dobrý'),
('CS2', 2, 'PC', 2023, 'hrám', 7, 'Stale hratelne, ale chybaju mi staré mapy'),
('Valorant', 2, 'PC', 2020, 'nedohraná', 5, 'Nudilo ma to po 20 hodinach'),
('Age of Empires IV', 3, 'PC', 2021, 'dohraná', 8, 'Skvelá stratégia, multiplayerka s kamosom'),
('Resident Evil 4 Remake', 6, 'PC', 2023, 'chcem hrat', NULL, 'Všetci o tom hovoria, musím si to zahrať'),
('Hollow Knight', 7, 'PC', 2017, 'dohraná', 9, 'Ťažké ale krásne, boss fighty sú mega'),
('FIFA 24', 5, 'PC', 2023, 'nedohraná', 4, 'Každý rok to isté, viac nekupujem'),
('Elden Ring', 1, 'PS5', 2022, 'chcem hrat', NULL, 'Priatelia odporúčajú, vyzerá brutálne'),
('God of War Ragnarök', 4, 'PS5', 2022, 'dohraná', 10, 'Kratos je boh, príbeh ma dojal');
