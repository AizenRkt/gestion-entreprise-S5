CREATE TABLE employe (
    id_employe INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    genre VARCHAR(1),
    date_embauche DATE,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat)
);

CREATE TABLE employe_statut (
    id_employe_statut INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    id_poste INT NOT NULL,
    activite INT NOT NULL, -- 0 (pas actif) et 1 (actif)
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe),
    FOREIGN KEY (id_poste) REFERENCES poste(id_poste)
);

CREATE TABLE role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    pwd VARCHAR(255) NOT NULL,
    id_employe INT UNIQUE,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE poste_role (
    id_poste_role INT AUTO_INCREMENT PRIMARY KEY,
    id_poste INT NOT NULL,
    id_role INT NOT NULL,
    date_role DATE NOT NULL,
    FOREIGN KEY (id_role) REFERENCES role(id_role)
);
