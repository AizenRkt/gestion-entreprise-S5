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

CREATE TABLE statut_pointage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    heure TIME,
    remarque VARCHAR(255),
    tolerance INT,
    jour INT CHECK (jour BETWEEN 1 AND 7)  -- 1 = lundi, 7 = dimanche
);

CREATE TABLE checkin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    datetime_checkin DATETIME NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE checkout (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    datetime_checkout DATETIME NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);


CREATE TABLE pointage (
    id_pointage INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    id_checkin INT,
    id_checkout INT,
    retard_min INT,
    duree_work TIME,
    date_pointage DATE NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe),
    FOREIGN KEY (id_checkin) REFERENCES checkin(id),
    FOREIGN KEY (id_checkout) REFERENCES checkout(id),
    UNIQUE KEY unique_pointage_jour (id_employe, date_pointage)
);

INSERT INTO statut_pointage (heure, remarque, tolerance, jour) VALUES
('08:00:00', 'Heure normale', 10, 1),  -- Lundi
('08:00:00', 'Heure normale', 10, 2),  -- Mardi
('08:00:00', 'Heure normale', 10, 3),  -- Mercredi
('08:00:00', 'Heure normale', 10, 4),  -- Jeudi
('07:30:00', 'Heure normale', 10, 5),  -- Vendredi
('08:00:00', 'Heure normale', 10, 6);  -- Samedi


INSERT INTO statut_pointage (heure, remarque, tolerance, jour) VALUES
('14:30:00', 'Heure normale', 10, 7); 


