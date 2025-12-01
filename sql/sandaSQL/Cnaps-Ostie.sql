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

INSERT INTO employe_prime (id_employe, id_prime, mois, annee) VALUES
(7, 1, 11, 2025),
(7, 4, 11, 2025);

