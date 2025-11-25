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


-- partie absence 
CREATE TABLE type_absence (
    id_type_absence INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    isAutorise TINYINT(1) DEFAULT 0
);

CREATE TABLE absence (
    id_absence INT AUTO_INCREMENT PRIMARY KEY,
    id_type_absence INT NOT NULL,
    id_employe INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    FOREIGN KEY (id_type_absence) REFERENCES type_absence(id_type_absence),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE documentation_absence (
    id_documentation_absence INT AUTO_INCREMENT PRIMARY KEY,
    type_documentation ENUM('justification', 'demande') NOT NULL,
    id_employe INT NOT NULL,
    motif TEXT,
    date_debut DATE,
    date_fin DATE,
    date_documentation DATE NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE validation_documentation_absence (
    id_validation_documentation_absence INT AUTO_INCREMENT PRIMARY KEY,
    id_documentation_absence INT NOT NULL,
    id_absence INT NOT NULL,
    FOREIGN KEY (id_documentation_absence) REFERENCES documentation_absence(id_documentation_absence),
    FOREIGN KEY (id_absence) REFERENCES absence(id_absence)
);

-- Inserting types of absence
INSERT INTO type_absence (nom, description, isAutorise) VALUES
('Maladie', 'Absence due to sickness', 1),
('Congé Annuel', 'Annual leave', 1),
('Congé Maternel', 'Maternity leave', 1),
('Congé Paternité', 'Paternity leave', 1),
('Absentéisme', 'Unauthorized absence', 0);

-- Inserting absences
INSERT INTO absence (id_type_absence, id_employe, date_debut, date_fin) VALUES
(1, 1, '2025-11-01', '2025-11-05'), -- Maladie for employé 1
(2, 2, '2025-11-10', '2025-11-15'), -- Congé Annuel for employé 2
(3, 3, '2025-11-20', '2025-11-30'), -- Congé Maternel for employé 3
(4, 4, '2025-12-01', '2025-12-10'), -- Congé Paternité for employé 4
(5, 5, '2025-11-25', '2025-11-27'); -- Absentéisme for employé 5

-- Inserting documentation for absence
INSERT INTO documentation_absence (type_documentation, id_employe, motif, date_debut, date_fin, date_documentation) VALUES
('justification', 1, 'Sickness', '2025-11-01', '2025-11-05', '2025-11-01'),
('demande', 2, 'Vacation', '2025-11-10', '2025-11-15', '2025-11-05'),
('justification', 3, 'Maternity leave', '2025-11-20', '2025-11-30', '2025-11-20'),
('demande', 4, 'Paternity leave', '2025-12-01', '2025-12-10', '2025-11-30'),
('justification', 5, 'Unauthorized absence', '2025-11-25', '2025-11-27', '2025-11-25');

-- Inserting validation of documentation for absence
INSERT INTO validation_documentation_absence (id_documentation_absence, id_absence) VALUES
(1, 1);  -- Validation for employé 1's sick leave


CREATE OR REPLACE VIEW view_absence_details AS
SELECT 
    e.id_employe,
    e.nom AS employe_nom,
    e.prenom AS employe_prenom,
    ta.nom AS type_absence,
    a.date_debut AS absence_date_debut,
    a.date_fin AS absence_date_fin,
    da.type_documentation,
    da.motif,
    da.date_documentation
FROM 
    employe e
JOIN 
    absence a ON e.id_employe = a.id_employe
JOIN 
    type_absence ta ON a.id_type_absence = ta.id_type_absence
JOIN 
    documentation_absence da ON e.id_employe = da.id_employe;