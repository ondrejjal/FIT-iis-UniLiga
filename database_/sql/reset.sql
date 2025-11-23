-- ============================================================
-- DATABASE INITIALIZATION 
-- ============================================================
USE xmrkviv00;

-- Drop existing tables
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS tournamentcontestantsteams,  tournamentcontestantssingle,  prize, matchesusers,  tournaments, Teams,  Users;
SET FOREIGN_KEY_CHECKS = 1;


-- === USERS ===
CREATE TABLE Users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    phone_number VARCHAR(14)
) ENGINE=InnoDB;


-- === TEAMS ===
CREATE TABLE Teams (
    id_team INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    logo LONGBLOB,
    FOREIGN KEY (id_user) REFERENCES Users(id_user) ON DELETE CASCADE -- The Captian 
) ENGINE=InnoDB;

-- === TEAM AND USERS == 
CREATE TABLE  TeamsUsers (
    id_team INT NOT NULL,
    id_user INT NOT NULL,
    PRIMARY KEY (id_team, id_user),
    FOREIGN KEY (id_team) REFERENCES Teams(id_team)
    FOREIGN KEY (id_user) REFERENCES Users(id_user)
)

-- === TOURNAMENTS ===
CREATE TABLE Tournaments (
    id_tournament INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    pending BOOLEAN NOT NULL,
    name VARCHAR(150) NOT NULL,
    date DATE NOT NULL,
    starting_time TIME NOT NULL,
    description TEXT,
	FOREIGN KEY (id_user) REFERENCES Users(id_user)
)ENGINE=InnoDB;

-- === MATCHES===
CREATE TABLE Matches (
    id_match INT PRIMARY KEY AUTO_INCREMENT,
    id_tournament INT NOT NULL,
    id_team1 INT DEFAULT NULL,
    id_team2 INT DEFAULT NULL,
    id_user1 INT DEFAULT NULL,
	id_user2 INT DEFAULT NULL,
    id_next_match INT DEFAULT NULL,
    date DATE NOT NULL,
    starting_time TIME NOT NULL,
    finishing_time TIME,
    result VARCHAR(100),
    FOREIGN KEY (id_next_match) REFERENCES Matches(id_next_match),
    FOREIGN KEY (id_tournament) REFERENCES Tournaments(id_tournament),
    FOREIGN KEY (id_user1) REFERENCES Users(id_user),
    FOREIGN KEY (id_user2) REFERENCES Users(id_user),
    FOREIGN KEY (id_team1) REFERENCES Teams(id_team),
    FOREIGN KEY (id_team2) REFERENCES Teams(id_team),
        CHECK (
        (id_user1 IS NOT NULL AND id_user2 IS NOT NULL AND id_team1 IS NULL AND id_team2 IS NULL)
        OR
        (id_team1 IS NOT NULL AND id_team2 IS NOT NULL AND id_user1 IS NULL AND id_user2 IS NULL)
    )
)ENGINE=InnoDB;

-- === PRIZES ===
CREATE TABLE Prize (
    id_tournament INT NOT NULL,
    prize_index INT NOT NULL,
    description TEXT,
    PRIMARY KEY (id_tournament, prize_index), -- weak entitiy
    FOREIGN KEY (id_tournament) REFERENCES Tournaments(id_tournament) ON DELETE CASCADE 
);

-- === TOURNAMENT CONTESTANTS ===
CREATE TABLE TournamentContestantsTeams (
    id_team INT NOT NULL,
    id_tournament INT NOT NULL,
    pending BOOLEAN NOT NULL,
    PRIMARY KEY (id_team, id_tournament),
    FOREIGN KEY (id_team) REFERENCES Teams(id_team) ON DELETE CASCADE,
    FOREIGN KEY (id_tournament) REFERENCES Tournaments(id_tournament)
);

-- === TOURNAMENT CONTESTANTS ===
CREATE TABLE TournamentContestantsSingle (
    id_user INT NOT NULL,
    id_tournament INT NOT NULL,
    pending BOOLEAN NOT NULL,
    PRIMARY KEY (id_user, id_tournament),
    FOREIGN KEY (id_user) REFERENCES Users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_tournament) REFERENCES Tournaments(id_tournament)
);


-- ============================================================
-- SAMPLE DATA
-- ============================================================
-- Insert some users
INSERT INTO Users (username, email, password_hash, name, surname)
VALUES 
('alice', 'alice@example.com', 'hash1', 'Alice', 'Smith'),
('bob', 'bob@example.com', 'hash2', 'Bob', 'Jones'),
('charlie', 'charlie@example.com', 'hash3', 'Charlie', 'Brown');

-- Insert some teams
INSERT INTO Teams (id_user, name) VALUES
(1, 'TeamAlpha'),
(2, 'TeamBeta');

-- Insert a tournament
INSERT INTO Tournaments (id_user, name, date, starting_time, description)
VALUES (1, 'Chess Tournament', '2025-11-20', '10:00:00', 'Friendly chess tournament');

-- Attempt a valid individual match
INSERT INTO MatchesUsers (id_tournament, id_user1, id_user2, date, starting_time)
VALUES (1, 1, 2, '2025-11-20', '10:30:00');

-- Attempt a valid team match
INSERT INTO MatchesUsers (id_tournament, id_team1, id_team2, date, starting_time)
VALUES (1, 1, 2, '2025-11-20', '10:30:00');

-- Attempt a valid team match
INSERT INTO MatchesUsers (id_tournament, id_team1, id_team2, date, starting_time)
VALUES (1, 1, 2, '2025-11-20', '11:30:00');

-- Attempt an invalid match (both users AND teams filled)
INSERT INTO MatchesUsers (id_tournament, id_user1, id_user2, id_team1, id_team2, date, starting_time)
VALUES (1, 1, 2, 1, 2, '2025-11-20', '12:30:00');

-- Attempt an invalid match (only one user filled)
INSERT INTO MatchesUsers (id_tournament, id_user1, date, starting_time)
VALUES (1, 1, '2025-11-20', '13:30:00');


-- ============================================================
-- END OF FILE
-- ============================================================
