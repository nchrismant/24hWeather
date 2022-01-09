CREATE TABLE utilisateurs (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(200) NOT NULL,
    inscription_date DATETIME NOT NULL,
    mail VARCHAR(50) NOT NULL UNIQUE,
    nom VARCHAR(50) NULL,
    telephone CHAR(10) NULL UNIQUE,
    adresse VARCHAR(60) NULL,
    description TEXT NULL,
    activate BOOLEAN NOT NULL DEFAULT FALSE,
    code VARCHAR(60) NOT NULL UNIQUE,
    recuperation CHAR(6) NULL UNIQUE
);

CREATE TABLE images_utilisateurs (
    id_img INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    img_name VARCHAR(200) NOT NULL,
    img VARCHAR(80) NOT NULL,
    id INT NOT NULL,
    FOREIGN KEY (id) REFERENCES utilisateurs(id)
);

CREATE TABLE evenements (
    id_event INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    motif TEXT NULL,
    start DATETIME NOT NULL,
    end DATETIME NOT NULL,
    id_ville VARCHAR(20) NULL,
    date_rappel DATE NULL,
    id INT NOT NULL,
    FOREIGN KEY (id) REFERENCES utilisateurs(id)
);

CREATE TABLE favoris (
    id_fav INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_ville VARCHAR(20) NOT NULL,
    pays VARCHAR(6) NOT NULL,
    ville VARCHAR(30) NOT NULL,
    latitude FLOAT NOT NULL,
    longitude FLOAT NOT NULL,
    id INT NOT NULL,
    FOREIGN KEY (id) REFERENCES utilisateurs(id)
);