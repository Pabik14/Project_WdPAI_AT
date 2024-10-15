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
