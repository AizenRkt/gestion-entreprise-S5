-- ======================
-- utilisateur, role, métier
-- ======================
INSERT INTO departement (nom) VALUES
('Informatique'),
('Production'),
('Ingénierie'),
('Administration'),
('Logistique');

INSERT INTO service (nom, id_dept) VALUES
-- Informatique
('Développement Logiciel', 1),
('Support Technique', 1),
-- Production
('Chaîne de Montage', 2),
('Contrôle Qualité', 2),
-- Ingénierie
('Recherche & Développement', 3),
('Bureau d''Études', 3),
-- Administration
('Comptabilité & Finance', 4),
('Ressources Humaines', 4),
-- Logistique
('Gestion des Stocks', 5),
('Transport & Distribution', 5);

INSERT INTO poste (titre, id_service) VALUES
-- Développement Logiciel
('Développeur Backend', 1),
('Développeur Frontend', 1),
('Chef de Projet IT', 1),
-- Support Technique
('Technicien Support N1', 2),
('Administrateur Systèmes', 2),
-- Chaîne de Montage
('Opérateur Machine', 3),
('Chef d''Équipe Production', 3),
-- Contrôle Qualité
('Inspecteur Qualité', 4),
('Responsable Qualité', 4),
-- Recherche & Développement
('Ingénieur R&D', 5),
('Chef de Projet Innovation', 5),
-- Bureau d''Études
('Dessinateur Industriel', 6),
('Ingénieur Conception', 6),
-- Comptabilité & Finance
('Comptable', 7),
('Contrôleur de Gestion', 7),
-- Ressources Humaines
('Directeur RH', 8),
('Chargé de Recrutement', 8),
('Responsable Formation', 8),
-- Gestion des Stocks
('Magasinier', 9),
('Responsable Entrepôt', 9),
-- Transport & Distribution
('Chauffeur Poids Lourd', 10),
('Coordinateur Logistique', 10);

INSERT INTO candidat (nom, prenom, email, telephone, genre, date_naissance) VALUES
('Dupont', 'Alice', 'alice.dupont@example.com', '0321234567', 'F', '1990-05-12'),
('Rakoto', 'Jean', 'jean.rakoto@example.com', '0347654321', 'M', '1985-11-23'),
('Martin', 'Sophie', 'sophie.martin@example.com', '0339876543', 'F', '1992-08-04'),
('Randria', 'Paul', 'paul.randria@example.com', '0324567890', 'M', '1980-02-15'),
('Rabe', 'Clara', 'clara.rabe@example.com', '0341122334', 'F', '1995-07-30');

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(1, 'Dupont', 'Alice', 'alice.dupont@example.com', '0321234567', 'F', '2020-01-15'),
(2, 'Rakoto', 'Jean', 'jean.rakoto@example.com', '0347654321', 'M', '2018-06-10'),
(3, 'Martin', 'Sophie', 'sophie.martin@example.com', '0339876543', 'F', '2021-09-01'),
(4, 'Randria', 'Paul', 'paul.randria@example.com', '0324567890', 'M', '2015-03-20'),
(5, 'Rabe', 'Clara', 'clara.rabe@example.com', '0341122334', 'F', '2022-11-05');

INSERT INTO employe_statut (id_employe, id_poste, activite) VALUES
(1, 1, 1),  -- Alice = Développeur Backend
(2, 5, 1),  -- Jean = Administrateur Systèmes
(3, 9, 1),  -- Sophie = Responsable Qualité
(4, 13, 1), -- Paul = Ingénieur Conception
(5, 19, 1); -- Clara = Responsable Entrepôt

INSERT INTO role (nom) VALUES
('Administrateur'),
('Manager'),
('Employé'),
('RH'),
('Responsable Sécurité');

INSERT INTO user (username, pwd, id_employe) VALUES
('alice.dupont', 'password123', 1),
('jean.rakoto', 'admin2024', 2),
('sophie.martin', 'sophiepass', 3),
('paul.randria', 'paulpass', 4),
('clara.rabe', 'clara2024', 5);

INSERT INTO poste_role (id_poste, id_role, date_role) VALUES
(1, 3, '2020-01-15'),  -- Développeur Backend = Employé
(5, 1, '2018-06-10'),  -- Administrateur Systèmes = Administrateur
(9, 2, '2021-09-01'),  -- Responsable Qualité = Manager
(13, 2, '2015-03-20'), -- Ingénieur Conception = Manager
(19, 4, '2022-11-05'); -- Responsable Entrepôt = RH

--OKOKOKOKOKOK

-- ======================
-- profileur
-- ======================
INSERT INTO profil (nom) VALUES
('Informaticien'),
('Technicien'),
('Ingénieur'),
('Cadre Administratif'),
('Opérateur de Production');

INSERT INTO diplome (nom) VALUES
('BEPC'),
('BACC'),
('LICENCE'),
('MASTER'),
('DOCTORAT');

INSERT INTO competence (nom) VALUES
('Programmation'),
('Administration Systèmes'),
('Gestion de Projet'),
('Analyse de Données'),
('Cybersécurité'),
('Maintenance Industrielle'),
('Conception Mécanique'),
('Contrôle Qualité'),
('Communication'),
('Leadership'),
('Négociation'),
('Comptabilité'),
('Analyse Financière'),
('Rédaction Technique'),
('Planification Logistique'),
('Gestion des Stocks'),
('Support Technique'),
('Service Client'),
('Innovation & Créativité'),
('Travail en Équipe');
INSERT INTO competence (nom) VALUES
('Marketing Digital'),
('Gestion des Risques'),
('Planification Stratégique'),
('Ressources Humaines'),
('Formation & Coaching'),
('Gestion du Changement'),
('Analyse Statistique'),
('Design Graphique'),
('Programmation Web'),
('Programmation Mobile'),
('Gestion de la Supply Chain'),
('Électrotechnique'),
('Maintenance Informatique'),
('Test & Validation Logiciel'),
('Soutien Technique Client'),
('Gestion de Budget'),
('Procédures Sécuritaires'),
('Logistique Internationale'),
('Optimisation des Processus'),
('Veille Technologique');

INSERT INTO ville (nom) VALUES
('Antananarivo'),
('Paris'),
('Londres'),
('New York'),
('Tokyo');