CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
CREATE TABLE anime_list (
    id SERIAL PRIMARY KEY, 
    user_id INT NOT NULL, 
    anime_name VARCHAR(255) NOT NULL, 
    category VARCHAR(50) NOT NULL CHECK (category IN (
        'Akcja', 'Przygodowe', 'Horror', 'Fantasy', 'Sci-Fi', 'Komedia', 
        'Dramat', 'Romans', 'Shounen', 'Shoujo', 'Mecha', 
        'Slice of Life', 'Mystery', 'Thriller', 'Sportowe'
    )), 
    type VARCHAR(20) NOT NULL CHECK (type IN (
        'Tv', 'Movie', 'OVA', 'Special'
    )), 
    status VARCHAR(20) NOT NULL CHECK (status IN (
        'watching', 'watched', 'planned', 'on hold', 'abandoned'
    )), 
    episodes_count INT CHECK (episodes_count >= 0), 
    FOREIGN KEY (user_id) REFERENCES users(id) 
);

INSERT INTO users (name, email, password) 
VALUES ('Admin', 'admin@admin.pl', '123');

INSERT INTO users (name, email, password) 
VALUES ('Test', 'test@test.pl', '123');

INSERT INTO users (name, email, password) 
VALUES ('Test2', 'test2@test.pl', '123');

INSERT INTO anime_list (user_id, anime_name, category, type, status, episodes_count) VALUES
(2, 'Naruto', 'Akcja', 'Tv', 'watching', 220),
(2, 'One Piece', 'Przygodowe', 'Tv', 'watching', 1040),
(2, 'Attack on Titan', 'Fantasy', 'Tv', 'watched', 75),
(2, 'My Hero Academia', 'Akcja', 'Tv', 'planned', 88),
(2, 'Death Note', 'Mystery', 'Tv', 'watched', 37),
(2, 'Fullmetal Alchemist: Brotherhood', 'Akcja', 'Tv', 'watched', 64),
(2, 'Your Name', 'Mystery', 'Movie', 'watched', 1),
(2, 'Sword Art Online', 'Sci-Fi', 'Tv', 'on hold', 25),
(2, 'Dragon Ball Z', 'Akcja', 'Tv', 'abandoned', 291),
(2, 'Steins;Gate', 'Sci-Fi', 'Tv', 'planned', 24);

INSERT INTO anime_list (user_id, anime_name, category, type, status, episodes_count) VALUES
(3, 'Naruto', 'Akcja', 'Tv', 'watching', 220),
(3, 'One Piece', 'Przygodowe', 'Tv', 'watching', 1040),
(3, 'Attack on Titan', 'Fantasy', 'Tv', 'watched', 75),
(3, 'My Hero Academia', 'Akcja', 'Tv', 'planned', 88),
(3, 'Death Note', 'Mystery', 'Tv', 'watched', 37),
(3, 'Fullmetal Alchemist: Brotherhood', 'Akcja', 'Tv', 'watched', 64),
(3, 'Your Name', 'Mystery', 'Movie', 'watched', 1),
(3, 'Sword Art Online', 'Sci-Fi', 'Tv', 'on hold', 25),
(3, 'Dragon Ball Z', 'Akcja', 'Tv', 'abandoned', 291),
(3, 'Steins;Gate', 'Sci-Fi', 'Tv', 'planned', 24);
