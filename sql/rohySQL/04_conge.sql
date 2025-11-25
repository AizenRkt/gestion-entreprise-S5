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


-- Inserting types of leave
INSERT INTO type_conge (nom, description, remuneree, nb_jours_max) VALUES
('Congé payé', 'Congé avec salaire', 1, 30),  -- Paid leave
('Congé sans solde', 'Congé sans rémunération', 0, NULL),  -- Unpaid leave
('Congé maladie', 'Congé pour raisons médicales', 1, 15);  -- Sick leave

-- Inserting leave requests
INSERT INTO demande_conge (id_type_conge, id_employe, date_debut, date_fin, nb_jours) VALUES
(1, 1, '2023-11-01', '2023-11-10', 10),  -- Paid leave request
(2, 2, '2023-11-15', '2023-11-20', 5),  -- Unpaid leave request
(3, 3, '2023-12-01', '2023-12-05', 5);  -- Sick leave request

-- Inserting validation of leave requests
INSERT INTO validation_conge (id_demande_conge, statut, date_validation) VALUES
(1, 'valider', '2023-10-28');  -- Approved leave


CREATE OR REPLACE VIEW view_conge_details AS
SELECT 
    d.id_demande_conge,
    e.id_employe,
    e.nom AS employe_nom,
    e.prenom AS employe_prenom,
    d.date_debut,
    d.date_fin,
    d.nb_jours,
    t.nom AS type_conge_nom,
    CASE 
        WHEN v.id_demande_conge IS NOT NULL THEN 'Validé'
        ELSE 'En attente'
    END AS validation_statut
FROM 
    demande_conge d
JOIN 
    employe e ON d.id_employe = e.id_employe
JOIN 
    type_conge t ON d.id_type_conge = t.id_type_conge
LEFT JOIN 
    validation_conge v ON d.id_demande_conge = v.id_demande_conge;