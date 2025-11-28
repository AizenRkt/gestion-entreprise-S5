-- Poste: Développeur Backend (id_poste = 2)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(2, 1),  -- Programmation
(2, 3),  -- Gestion de Projet
(2, 30); -- Travail en Équipe

-- Poste: Développeur Frontend (id_poste = 3)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(3, 1),   -- Programmation
(3, 9),   -- Communication
(3, 30);  -- Travail en Équipe

-- Poste: Chef de Projet IT (id_poste = 4)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(4, 3),   -- Gestion de Projet
(4, 10),  -- Leadership
(4, 30);  -- Travail en Équipe

-- Poste: Technicien Support N1 (id_poste = 5)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(5, 2),   -- Administration Systèmes
(5, 16),  -- Maintenance Industrielle / Informatique
(5, 17);  -- Support Technique

-- Poste: Administrateur Systèmes (id_poste = 6)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(6, 2),  -- Administration Systèmes
(6, 5),  -- Cybersécurité
(6, 10); -- Leadership

-- Poste: Inspecteur Qualité (id_poste = 9)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(9, 8),  -- Contrôle Qualité
(9, 4),  -- Analyse de Données
(9, 30); -- Travail en Équipe

-- Poste: Responsable Qualité (id_poste = 10)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(10, 8),  -- Contrôle Qualité
(10, 10), -- Leadership
(10, 9);  -- Communication

-- Poste: Ingénieur R&D (id_poste = 11)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(11, 1),  -- Programmation
(11, 19), -- Innovation & Créativité
(11, 30); -- Travail en Équipe

-- Poste: Chef de Projet Innovation (id_poste = 12)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(12, 3),  -- Gestion de Projet
(12, 10), -- Leadership
(12, 19); -- Innovation & Créativité

-- Poste: Dessinateur Industriel (id_poste = 13)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(13, 7),  -- Conception Mécanique
(13, 14), -- Rédaction Technique
(13, 30); -- Travail en Équipe

-- Poste: Ingénieur Conception (id_poste = 14)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(14, 7),  -- Conception Mécanique
(14, 10), -- Leadership
(14, 19); -- Innovation & Créativité

-- Poste: Comptable (id_poste = 15)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(15, 12), -- Comptabilité
(15, 13), -- Analyse Financière
(15, 30); -- Travail en Équipe

-- Poste: Responsable Formation (id_poste = 20)
INSERT INTO poste_competence (id_poste, id_competence) VALUES
(20, 24), -- Gestion des compétences et relations employé
(20, 25), -- Planification Logistique
(20, 30); -- Travail en Équipe


-- =========================
-- Assigning competences to employees
-- =========================

-- Mamy Ravatomanga (id_employe = 1)
INSERT INTO employe_competence (id_employe, id_competence) VALUES
(1, 1),  -- Programmation
(1, 3),  -- Gestion de Projet
(1, 10), -- Leadership
(1, 12); -- Comptabilité

-- Andry Rajoelina (id_employe = 2)
INSERT INTO employe_competence (id_employe, id_competence) VALUES
(2, 2),  -- Administration Systèmes
(2, 17), -- Support Technique
(2, 5),  -- Cybersécurité
(2, 18); -- Service Client

-- Alice Dupont (id_employe = 3)
INSERT INTO employe_competence (id_employe, id_competence) VALUES
(3, 1),  -- Programmation
(3, 23), -- Planification Stratégique
(3, 19), -- Marketing Digital
(3, 24); -- Gestion des Risques

-- Zo Lalaina (id_employe = 4)
INSERT INTO employe_competence (id_employe, id_competence) VALUES
(4, 1),  -- Programmation
(4, 3),  -- Gestion de Projet
(4, 4),  -- Analyse de Données
(4, 20); -- Travail en Équipe

-- Andry George (id_employe = 5)
INSERT INTO employe_competence (id_employe, id_competence) VALUES
(5, 1),  -- Programmation
(5, 3),  -- Gestion de Projet
(5, 10), -- Leadership
(5, 17); -- Support Technique


-- Add a new candidate
INSERT INTO candidat (nom, prenom, email, telephone, genre, date_naissance) VALUES
('Rakoto', 'Jean', 'jean.rakoto@gmail.com', '0321234567', 'M', '1995-07-10');

-- Add the candidate as an employee
INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(LAST_INSERT_ID(), 'Rakoto', 'Jean', 'jean.rakoto@gmail.com', '0321234567', 'M', '2025-11-28');

-- Assign the employee to a poste: Développeur Backend (id_poste = 2)
INSERT INTO employe_statut (id_employe, id_poste, activite) VALUES
(LAST_INSERT_ID(), 2, 1);

-- Assign some competences, but leave one missing
-- Développeur Backend requires: 1 (Programmation), 3 (Gestion de Projet), 30 (Travail en Équipe)
-- We give only 1 and 3, missing 30
INSERT INTO employe_competence (id_employe, id_competence) VALUES
(LAST_INSERT_ID(), 1),  -- Programmation
(LAST_INSERT_ID(), 3);  -- Gestion de Projet
