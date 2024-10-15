CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
CREATE TABLE anime_list (
    id SERIAL PRIMARY KEY, -- Unikalne ID dla każdego dodanego anime
    user_id INT NOT NULL, -- ID użytkownika dodającego anime, połączenie z drugą tabelą użytkowników
    anime_name VARCHAR(255) NOT NULL, -- Nazwa anime
    category VARCHAR(50) NOT NULL CHECK (category IN (
        'Akcja', 'Przygodowe', 'Horror', 'Fantasy', 'Sci-Fi', 'Komedia', 
        'Dramat', 'Romans', 'Shounen', 'Shoujo', 'Mecha', 
        'Slice of Life', 'Mystery', 'Thriller', 'Sportowe'
    )), -- Kategoria anime, ograniczona do wybranych wartości
    type VARCHAR(20) NOT NULL CHECK (type IN (
        'Tv', 'Movie', 'OVA', 'Special'
    )), -- Typ anime, ograniczony do wybranych wartości
    status VARCHAR(20) NOT NULL CHECK (status IN (
        'watching', 'watched', 'planned', 'on hold', 'abandoned'
    )), -- Status oglądania anime
    episodes_count INT CHECK (episodes_count >= 0), -- Liczba odcinków, nie może być ujemna
    FOREIGN KEY (user_id) REFERENCES users(id) -- Połączenie z tabelą users
);