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

-- Inserting maximum allowable overtime hours
INSERT INTO max_heure_sup (nb_heures_max_par_semaine, date_application) VALUES
(8, '2025-01-01'),  
(12, '2026-01-01'); 


-- Inserting overtime requests
INSERT INTO demande_heure_sup (id_employe, date_demande) VALUES
(1, '2023-10-01 10:00:00'),  -- Mamy Ravatomanga's request
(2, '2023-10-02 11:00:00'),  -- Andry Rajoelina's request
(3, '2023-10-03 12:30:00');  -- Alice Dupont's request

-- Inserting details of overtime requests
INSERT INTO detail_heure_sup (id_demande_heure_sup, heure_debut, heure_fin, date_debut, date_fin) VALUES
(1, '17:00:00', '20:00:00', '2023-10-05', '2023-10-05'),  -- Mamy's overtime
(2, '18:00:00', '21:00:00', '2023-10-06', '2023-10-06'),  -- Andry's overtime
(3, '16:00:00', '19:00:00', '2023-10-07', '2023-10-07');  -- Alice's overtime

-- Inserting validation of overtime requests
INSERT INTO validation_heure_sup (id_demande_heure_sup, commentaire, statut, date_validation) VALUES
(1, 'Demande acceptée pour le 5 octobre.', 'valide', '2023-10-02');  -- Mamy's request validated

CREATE OR REPLACE VIEW view_heure_sup_details AS
SELECT 
    d.id_demande_heure_sup,
    e.id_employe,
    e.nom AS employe_nom,
    e.prenom AS employe_prenom,
    d.date_demande,
    dh.heure_debut,
    dh.heure_fin,
    dh.date_debut AS date_heure_debut,
    dh.date_fin AS date_heure_fin,
    CASE 
        WHEN v.id_validation_heure_sup IS NOT NULL THEN 'Validé'
        ELSE 'En attente'
    END AS validation_statut
FROM 
    demande_heure_sup d
JOIN 
    employe e ON d.id_employe = e.id_employe
JOIN 
    detail_heure_sup dh ON d.id_demande_heure_sup = dh.id_demande_heure_sup
LEFT JOIN 
    validation_heure_sup v ON d.id_demande_heure_sup = v.id_demande_heure_sup;