/*
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
*/

-- partie congé
CREATE TABLE type_conge (
    id_type_conge INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    remuneree TINYINT(1) DEFAULT 0,
    nb_jours_max INT DEFAULT NULL
);

CREATE TABLE demande_conge (
    id_demande_conge INT AUTO_INCREMENT PRIMARY KEY,
    id_type_conge INT NOT NULL,
    id_employe INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    nb_jours INT NOT NULL,
    FOREIGN KEY (id_type_conge) REFERENCES type_conge(id_type_conge),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE validation_conge (
    id_validation_conge INT AUTO_INCREMENT PRIMARY KEY,
    id_demande_conge INT NOT NULL,
    statut ENUM('valider', 'refuser'),
    date_validation DATE,
    FOREIGN KEY (id_demande_conge) REFERENCES demande_conge(id_demande_conge)
);