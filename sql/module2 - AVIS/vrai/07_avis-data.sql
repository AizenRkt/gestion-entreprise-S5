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
('Gestion des Stocks', 5),
('Transport & Distribution', 5),
-- KPI et Stock
('Kpi et Stock', 1);

INSERT INTO poste (titre, id_service) VALUES
-- en contrat d'essai
('essaie', 8),
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
('Chauffeur Poids Lourd', 10),
('Coordinateur Logistique', 10),
-- KPI et Stock
('Responsable KPI Stock', (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1));

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(NULL, 'Ravatomanga', 'Mamy', 'mamyRavato@gmail.com', '123456789', 'M', '2020-01-01'),
(NULL, 'Rajoelina', 'Andry', 'andryRajojo@gmail.com', '987456321', 'M', '2020-01-01'),
(NULL, 'alice', 'dupont', 'aclieDupont@gmail.com', '0348366414', 'F', '2020-01-01'),
(NULL, 'Lalaina', 'Zo', 'zo.lalaina@gmail.com', '0341234567', 'M', '2025-11-23'),
(NULL, 'George', 'Andry', 'andry.george@gmail.com', '0347654321', 'M', '2025-11-23'),
(NULL, 'Razafmanantsoa', 'Hanitra', 'hanitra.razaf@gmail.com', '0341122334', 'F', '2026-01-28'),
(NULL, 'Rabe', 'Tiana', 'tiana.magasin@example.com', '0340000000', 'F', '2025-12-01'),
(NULL, 'Randrianarivelo', 'Miora', 'miora.validator@example.com', '0340000001', 'F', '2025-12-02');

INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES
(1, 17, 1, NOW()),  -- Date de modification actuelle
(2, 18, 1, '2025-11-23 00:00:00'),  -- Date de modification spécifique
(3, 2, 1, '2025-11-25 00:00:00'),   -- Date de modification spécifique
(4, 2, 1, '2025-11-26 00:00:00'),   -- Zo Lalaina = Développeur Backend
(5, 3, 1, '2025-11-26 00:00:00'),   -- Date de modification actuelle
(6, (SELECT id_poste FROM poste WHERE titre = 'Responsable KPI Stock' AND id_service = (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1)), 1, '2026-01-28 00:00:00'), -- Hanitra Razafmanantsoa
((SELECT id_employe FROM employe WHERE email = 'tiana.magasin@example.com' LIMIT 1), (SELECT id_poste FROM poste WHERE titre = 'Magasinier' AND id_service = (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1) LIMIT 1), 1, NOW()),
((SELECT id_employe FROM employe WHERE email = 'miora.validator@example.com' LIMIT 1), (SELECT id_poste FROM poste WHERE titre = 'Coordinateur Logistique' AND id_service = (SELECT id_service FROM service WHERE nom = 'Transport & Distribution' LIMIT 1) LIMIT 1), 1, NOW());

INSERT INTO role (nom) VALUES
('Administrateur'),
('Manager'),
('Employé'),
('RH'),
('Responsable Sécurité');

INSERT INTO user (username, pwd, id_employe) VALUES
('mamy.ravato', '123', 1),
('dj.rajojo', '123', 2),
('alice.dupont', '123', 3),
('zo', '123', 4),
('andry', '123', 5),
('hanitra', '123', 6),
('tiana', '123', 7),
('miora', '123', 8);

INSERT INTO poste_role (id_poste, id_role, date_role) VALUES
(17, 2, '2020-01-15'),  -- Directeur RH = Manager
(18, 4, '2018-06-10'),  -- Responsable recrutement = RH
(4, 2, '2020-01-01'),  -- Chef de Projet IT = Manager 
(19, 3, '2025-11-23'),  -- Zo Lalaina = Employé
(20, 3, '2025-11-23'),  -- Andry George = Employé
((SELECT id_poste FROM poste WHERE titre = 'Responsable KPI Stock' AND id_service = (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1)), (SELECT id_role FROM role WHERE nom = 'Manager' LIMIT 1), '2026-01-28'), -- Hanitra Razafmanantsoa Manager
((SELECT id_poste FROM poste WHERE titre = 'Magasinier' AND id_service = (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1)), (SELECT id_role FROM role WHERE nom = 'Employé' LIMIT 1), NOW()), -- Magasinier = Employé
((SELECT id_poste FROM poste WHERE titre = 'Coordinateur Logistique' AND id_service = (SELECT id_service FROM service WHERE nom = 'Transport & Distribution' LIMIT 1)), (SELECT id_role FROM role WHERE nom = 'Manager' LIMIT 1), NOW()); -- Coordinateur Logistique = Manager

-- ======================
-- Permissions des Routes (RBAC)
-- ======================

-- Annonces (Gestion)
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/kpi/achats', 'Manager', (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/kpi/stock', 'Manager', (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/kpi/vente', 'Manager', (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1));
-- Stock (KPI et Stock)
-- Magasin operations (entry/exit)
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/stock/entree', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/stock/sortie', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));

-- Annonces (Consultation)
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/annonceListe', 'Administrateur', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/annonceListe', 'RH', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/annonceListe', 'Employé', 8);


-- CV question (Gestion)
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/createQuestion', 'Administrateur', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/createQuestion', 'RH', 8);

-- QCM créer de 0 (Gestion & Consultation)
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/createQcm', 'Administrateur', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/createQcm', 'RH', 8);

-- QCM existant (Gestion & Consultation)
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/seeAllQcm', 'Administrateur', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/seeAllQcm', 'RH', 8);

-- Employés (Gestion)
-- AVIS (Achats/Ventes) pour Magasinier (Employé, Gestion des Stocks)
-- Ventes
-- Ventes dashboard réservé au Manager (Kpi et Stock)
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/ventes', 'Manager', (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/ventes/clients', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/ventes/commandes', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/ventes/livraisons', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/ventes/factures', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/ventes/encaissements', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));

-- Achats
-- Achats dashboard réservé au Manager (Kpi et Stock)
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/dashboard', 'Manager', (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/saisie', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/demandes', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/bc', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/bc/nouveau', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/receptions', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/receptions/nouveau', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/factures', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/factures/nouveau', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/paiements', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/avis/achat/paiements/nouveau', 'Employé', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1));

-- menu 
INSERT INTO menu_ui (nom, id_service, role) VALUES('menuDirecteurRH', 8, 'Manager');
INSERT INTO menu_ui (nom, id_service, role) VALUES('menuRH', 8, 'RH');
INSERT INTO menu_ui (nom, id_service, role) VALUES('menuKPISTOCK', (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1), 'Manager');

-- Menu pour Magasinier (Gestion des Stocks)
INSERT INTO menu_ui (nom, id_service, role) VALUES('menuMAGASIN', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1), 'Employé');

-- Menu pour Validateur (Transport & Distribution)
INSERT INTO menu_ui (nom, id_service, role) VALUES('menuLOG', (SELECT id_service FROM service WHERE nom = 'Transport & Distribution' LIMIT 1), 'Manager');
