CREATE DATABASE IF NOT EXISTS doremitendry;
USE doremitendry;
CREATE TABLE instruments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    instrument_id INT,
    niveau VARCHAR(100),
    FOREIGN KEY (instrument_id) REFERENCES instruments(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cours_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    ordre INT DEFAULT 1,
    image_hero VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (cours_id) REFERENCES cours(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE module_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    ordre INT DEFAULT 1,
    FOREIGN KEY (module_id) REFERENCES modules(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE section_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    url_image VARCHAR(255) NOT NULL,
    texte_alternatif VARCHAR(255) DEFAULT '',
    classe_css VARCHAR(100) DEFAULT 'shadow',
    ordre INT DEFAULT 1,
    FOREIGN KEY (section_id) REFERENCES module_sections(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE lecons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    FOREIGN KEY (module_id) REFERENCES modules(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE exercices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lecon_id INT NOT NULL,
    question TEXT NOT NULL,
    correction TEXT,
    FOREIGN KEY (lecon_id) REFERENCES lecons(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE partitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    fichier VARCHAR(255) NOT NULL,
    cours_id INT NOT NULL,
    FOREIGN KEY (cours_id) REFERENCES cours(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telephone VARCHAR(50),
    photo VARCHAR(255) DEFAULT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'apprenant'
);