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

-- partie heure sup
CREATE TABLE max_heure_sup (
    id_max_heure_sup INT AUTO_INCREMENT PRIMARY KEY,
    nb_heures_max_par_semaine INT NOT NULL,
    date_application DATE NOT NULL
);

CREATE TABLE demande_heure_sup (
    id_demande_heure_sup INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    date_demande DATETIME NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE detail_heure_sup (
    id_detail_heure_sup INT AUTO_INCREMENT PRIMARY KEY,
    id_demande_heure_sup INT NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    FOREIGN KEY (id_demande_heure_sup) REFERENCES demande_heure_sup(id_demande_heure_sup)
);

CREATE TABLE validation_heure_sup (
    id_validation_heure_sup INT AUTO_INCREMENT PRIMARY KEY,
    id_demande_heure_sup INT NOT NULL,
    commentaire TEXT,
    statut ENUM('valide', 'refuse') NOT NULL,
    date_validation DATE NOT NULL,
    FOREIGN KEY (id_demande_heure_sup) REFERENCES demande_heure_sup(id_demande_heure_sup)
);
