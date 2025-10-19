-- ======================
-- Ajout de deux nouveaux employés de test (liés aux données existantes)
-- ======================

-- 1. Ajouter deux candidats
INSERT INTO candidat (nom, prenom, email, telephone, genre) VALUES
('Fenitra', 'Rakoto', 'fenitra.rakoto@example.com', '0321234567', 'M'),
('Sitraka', 'Andrian', 'sitraka.andrian@example.com', '0327654321', 'F');

-- 2. Ajouter deux employés (en supposant que les id_candidat générés sont 6 et 7)
INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(6, 'Fenitra', 'Rakoto', 'fenitra.rakoto@entreprise.com', '0321234567', 'M', '2024-01-10'),
(7, 'Sitraka', 'Andrian', 'sitraka.andrian@entreprise.com', '0327654321', 'F', '2024-02-15');

-- 3. Statut employé (en utilisant des postes existants : 1=Dév Backend, 4=Chargé de Recrutement)
INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES
(4, 1, 1, '2024-01-10 09:00:00'),  -- Fenitra Rakoto → Développeur Backend (actif)
(5, 4, 1, '2024-02-15 09:00:00');  -- Sitraka Andrian → Chargé de Recrutement (actif)

-- 4. Comptes utilisateurs (en supposant que les id_employe générés sont 4 et 5)
INSERT INTO user (username, pwd, id_employe) VALUES
('fenitra', 'motdepasse1', 4),
('sitraka', 'motdepasse2', 5);
-- ======================
-- departement
-- ======================
INSERT INTO departement (nom) VALUES
('Informatique'),
('Ressources Humaines');

-- ======================
-- service
-- ======================
INSERT INTO service (nom, id_dept) VALUES
('Développement Web', 1),
('Recrutement', 2);

-- ======================
-- poste
-- ======================
INSERT INTO poste (titre, id_service) VALUES
('Développeur Backend', 1),
('Chargé de Recrutement', 2);

-- ======================
-- candidat
-- ======================
INSERT INTO candidat (nom, prenom, email, telephone, genre) VALUES
('Fenitra', 'Rakoto', 'fenitra.rakoto@example.com', '0321234567', 'M'),
('Sitraka', 'Andrian', 'sitraka.andrian@example.com', '0327654321', 'F');

-- ======================
-- employe
-- ======================
INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(1, 'Fenitra', 'Rakoto', 'fenitra.rakoto@entreprise.com', '0321234567', 'M', '2024-01-10'),
(2, 'Sitraka', 'Andrian', 'sitraka.andrian@entreprise.com', '0327654321', 'F', '2024-02-15');

-- ======================
-- employe_statut
-- ======================
INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES
(1, 1, 1, '2024-01-10 09:00:00'),  -- Fenitra Rakoto → Développeur Backend (actif)
(2, 2, 1, '2024-02-15 09:00:00');  -- Sitraka Andrian → Chargé de Recrutement (actif)

-- ======================
-- role
-- ======================
INSERT INTO role (nom) VALUES
('Administrateur'),
('RH');

-- ======================
-- user
-- ======================
INSERT INTO user (username, pwd, id_employe) VALUES
('fenitra', 'motdepasse1', 1),
('sitraka', 'motdepasse2', 2);

-- ======================
-- poste_role
-- ======================
INSERT INTO poste_role (id_poste, id_role, date_role) VALUES
(1, 1, '2024-01-10'),  -- Développeur Backend → Administrateur
(2, 2, '2024-02-15');  -- Chargé de Recrutement → RH
