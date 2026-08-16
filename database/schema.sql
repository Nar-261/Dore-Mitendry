CREATE DATABASE IF NOT EXISTS doremitendry;
USE doremitendry;

CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  telephone VARCHAR(50) DEFAULT NULL,
  photo VARCHAR(255) DEFAULT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'apprenant',
  date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS instruments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS cours (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  description TEXT,
  instrument_id INT,
  niveau VARCHAR(100) DEFAULT 'Débutant',
  FOREIGN KEY (instrument_id) REFERENCES instruments(id)
);

CREATE TABLE IF NOT EXISTS modules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cours_id INT NOT NULL,
  titre VARCHAR(255) NOT NULL,
  FOREIGN KEY (cours_id) REFERENCES cours(id)
);

CREATE TABLE IF NOT EXISTS lecons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  module_id INT NOT NULL,
  titre VARCHAR(255) NOT NULL,
  video VARCHAR(255) DEFAULT NULL,
  contenu TEXT,
  duree VARCHAR(50) DEFAULT '10 min',
  FOREIGN KEY (module_id) REFERENCES modules(id)
);

CREATE TABLE IF NOT EXISTS exercices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lecon_id INT NOT NULL,
  question TEXT NOT NULL,
  correction TEXT,
  FOREIGN KEY (lecon_id) REFERENCES lecons(id)
);

CREATE TABLE IF NOT EXISTS partitions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  fichier VARCHAR(255) DEFAULT NULL,
  cours_id INT NOT NULL,
  FOREIGN KEY (cours_id) REFERENCES cours(id)
);

