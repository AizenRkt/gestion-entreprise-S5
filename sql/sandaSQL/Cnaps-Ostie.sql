CREATE TABLE assurance (
    id_assurance INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    minpay INT ,
    maxpay INT,
    taux FLOAT NOT NULL
);

INSERT INTO assurance (id_assurance, nom, minpay, maxpay, taux) VALUES
(1, 'Retenue CNaPS', NULL, NULL, 1),
(2, 'Retenue sanitaire', NULL, NULL, 5),
(3, 'Tranche IRSA 1', 0, 350000, 0),
(4, 'Tranche IRSA 2', 350001, 400000, 5),
(5, 'Tranche IRSA 3', 400001, 500000, 10),
(6, 'Tranche IRSA 4', 500001, 600000, 15),
(7, 'Tranche IRSA 5', 600001, 4000000, 20),
(8, 'Tranche IRSA 6', 4000001, NULL, 25);


CREATE TABLE taux_heures_sup (
    id_tauxheuresup BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type_heuresup VARCHAR(100) NOT NULL,
    heure_debut INT NOT NULL,
    heure_fin INT NOT NULL,
    taux FLOAT NOT NULL
);

INSERT INTO taux_heures_sup (type_heuresup, heure_debut, heure_fin, taux) VALUES
('Heures sup 25%', 1, 2, 25),
('Heures sup 50%', 3, 10, 50),
('Heures sup 100%', 11, 9999, 100);

CREATE TABLE prime (
    id_prime INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    montant DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    type_prime ENUM('mensuelle','annuelle','ponctuelle') DEFAULT 'mensuelle',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO prime (nom, description, montant, type_prime) VALUES
('Prime de rendement', 'Prime accordée pour performance exceptionnelle', 50000, 'mensuelle'),
('Prime d\'anciennete', 'Prime en fonction des annees d\'anciennete', 20000, 'annuelle'),
('Prime de Noel', 'Prime exceptionnelle de fin d\'annee', 100000, 'ponctuelle'),
('Prime de presence', 'Prime pour presence parfaite sur le mois', 15000, 'mensuelle'),
('Prime de projet', 'Prime pour reussite d\'un projet specifique', 30000, 'ponctuelle');


CREATE TABLE employe_prime (
    id_employe INT NOT NULL,
    id_prime INT NOT NULL,
    mois INT,
    annee INT,
    PRIMARY KEY (id_employe, id_prime, mois, annee)
);
;

CREATE TABLE pourcentage_avance (
    id_pourcentage INT AUTO_INCREMENT PRIMARY KEY,
    pourcentage FLOAT NOT NULL,
    date DATE NOT NULL DEFAULT CURRENT_DATE 
);

CREATE TABLE avance_salaire (
    id_avance INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    id_pourcentage INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    date_avance DATE NOT NULL DEFAULT CURRENT_DATE,
    statut ENUM('demandee', 'accordee', 'remboursee') DEFAULT 'demandee'
);

INSERT INTO pourcentage_avance (pourcentage, date)
VALUES
(30, '2025-12-07'),
(50, '2025-12-07');
INSERT INTO employe_prime (id_employe, id_prime, mois, annee) VALUES
(7, 1, 11, 2025),
(7, 4, 11, 2025)

INSERT INTO avance_salaire (id_employe, id_pourcentage, montant, date_avance, statut)
VALUES
(7, 1, 150000, '2025-11-20', 'accordee');

INSERT INTO avance_salaire (id_employe, id_pourcentage, montant, date_avance, statut)
VALUES
(7, 2, 250000, '2025-12-07', 'demandee');

-- Employee 7 overtime requests
INSERT INTO demande_heure_sup (id_employe, date_demande) VALUES
(7, '2025-11-05 09:00:00'),   -- November request
(7, '2025-12-03 14:30:00');   -- December request


-- Details of overtime requests
INSERT INTO detail_heure_sup (id_demande_heure_sup, heure_debut, heure_fin, date_debut, date_fin) VALUES
-- NOVEMBER (3 hours)
(LAST_INSERT_ID() - 1, '17:00:00', '20:00:00', '2025-11-10', '2025-11-10'),

-- DECEMBER (2 hours)
(LAST_INSERT_ID(),     '18:00:00', '20:00:00', '2025-12-08', '2025-12-08');


-- Validations (only validées, as requested)
INSERT INTO validation_heure_sup (id_demande_heure_sup, commentaire, statut, date_validation) VALUES
(LAST_INSERT_ID() - 1, 'Heures sup validées pour novembre.', 'valide', '2025-11-06'),
(LAST_INSERT_ID(),     'Heures sup validées pour décembre.', 'valide', '2025-12-04');



DELETE FROM contrat_employe_statut 
WHERE id_employe_statut IN (
    SELECT id_employe_statut 
    FROM employe_statut 
    WHERE id_employe = 7
);

-- Delete from employe_statut
DELETE FROM employe_statut 
WHERE id_employe = 7;

-- Delete from contrat_travail
DELETE FROM contrat_travail 
WHERE id_employe = 7;

-- Delete from employe_prime
DELETE FROM employe_prime 
WHERE id_employe = 7;

-- Delete from avance_salaire
DELETE FROM avance_salaire 
WHERE id_employe = 8;

-- Delete from demande_heure_sup
DELETE FROM demande_heure_sup 
WHERE id_employe = 8;

-- Delete from absence
DELETE FROM absence 
WHERE id_employe = 8;

-- Delete from documentation_absence
DELETE FROM documentation_absence 
WHERE id_employe = 8;

-- Delete from validation_documentation_absence
DELETE FROM validation_documentation_absence 
WHERE id_documentation_absence IN (
    SELECT id_documentation_absence 
    FROM documentation_absence 
    WHERE id_employe = 8
);

-- Delete from validation_heure_sup
DELETE FROM validation_heure_sup 
WHERE id_demande_heure_sup IN (
    SELECT id_demande_heure_sup 
    FROM demande_heure_sup 
    WHERE id_employe = 7
);

-- Delete from detail_heure_sup
DELETE FROM detail_heure_sup 
WHERE id_demande_heure_sup IN (
    SELECT id_demande_heure_sup 
    FROM demande_heure_sup 
    WHERE id_employe = 7
);

-- Delete from pointage (if applicable)
DELETE FROM pointage 
WHERE id_employe = 8;

-- Delete from checkin and checkout (if applicable)
DELETE FROM checkin 
WHERE id_employe = 8;

DELETE FROM checkout 
WHERE id_employe = 8;

-- Delete from user (if applicable)
DELETE FROM user 
WHERE id_employe = 8;

-- Step 3: Delete from employe
DELETE FROM employe 
WHERE id_employe = 8;

-- Step 4: Delete from candidat
DELETE FROM candidat 
WHERE id_candidat = 8;

-- Commit the transaction
COMMIT;