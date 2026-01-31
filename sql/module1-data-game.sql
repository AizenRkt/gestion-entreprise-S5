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

INSERT INTO candidat (nom, prenom, email, telephone, genre, date_naissance) VALUES
('Ravatomanga', 'Mamy', 'mamyRavato@gmail.com', '123456789', 'M', '2020-01-01'),
('Rajoelina', 'Andry', 'andryRajojo@gmail.com', '987456321', 'M', '2020-01-01'),
('alice', 'dupont', 'aliceDupont@gmail.com', '0348366414', 'F', '2020-01-01'),
('Lalaina', 'Zo', 'zo.lalaina@gmail.com', '0341234567', 'M', '1995-05-15'),
('George', 'Andry', 'andry.george@gmail.com', '0347654321', 'M', '1993-08-20'),
('Razafmanantsoa', 'Hanitra', 'hanitra.razaf@gmail.com', '0341122334', 'F', '1990-04-12'),
('Rabe', 'Tiana', 'tiana.magasin@example.com', '0340000000', 'F', '1995-12-01'),
('Randrianarivelo', 'Miora', 'miora.validator@example.com', '0340000001', 'F', '1994-06-12');

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(1, 'Ravatomanga', 'Mamy', 'mamyRavato@gmail.com', '123456789', 'M', '2020-01-01'),
(2, 'Rajoelina', 'Andry', 'andryRajojo@gmail.com', '987456321', 'M', '2020-01-01'),
(3, 'alice', 'dupont', 'aclieDupont@gmail.com', '0348366414', 'F', '2020-01-01'),
(4, 'Lalaina', 'Zo', 'zo.lalaina@gmail.com', '0341234567', 'M', '2025-11-23'),
(5, 'George', 'Andry', 'andry.george@gmail.com', '0347654321', 'M', '2025-11-23'),
(6, 'Razafmanantsoa', 'Hanitra', 'hanitra.razaf@gmail.com', '0341122334', 'F', '2026-01-28'),
((SELECT id_candidat FROM candidat WHERE email = 'tiana.magasin@example.com' LIMIT 1), 'Rabe', 'Tiana', 'tiana.magasin@example.com', '0340000000', 'F', '2025-12-01'),
((SELECT id_candidat FROM candidat WHERE email = 'miora.validator@example.com' LIMIT 1), 'Randrianarivelo', 'Miora', 'miora.validator@example.com', '0340000001', 'F', '2025-12-02');

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
('tiana', '123', (SELECT id_employe FROM employe WHERE email = 'tiana.magasin@example.com' LIMIT 1)),
('miora', '123', (SELECT id_employe FROM employe WHERE email = 'miora.validator@example.com' LIMIT 1));

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



-- scoring
-- ======================

-- scoring et qcm
INSERT INTO type_scoring (nom) VALUES
('QCM'),
('entretien');

INSERT INTO question (enonce) VALUES
('Quel langage est principalement utilisé pour le développement backend web ?'),
('HTML est utilisé pour quoi ?'),
('Quelle structure permet de stocker des données de façon clé/valeur en PHP ?'),
('Quel est l''outil de versionning le plus utilisé ?'),
('Quelle commande Git permet de récupérer les modifications du dépôt distant ?'),
('Qu''est-ce qu''une API REST ?'),
('Que signifie SQL ?'),
('Quel framework PHP est populaire pour les applications web ?'),
('Quelle méthode HTTP est utilisée pour créer des ressources ?'),
('Que fait la fonction "console.log()" en JavaScript ?');

INSERT INTO question (enonce) VALUES
('Que signifie SLA dans le support technique ?'),
('Quel outil est utilisé pour diagnostiquer un réseau ?'),
('Comment appelle-t-on un document décrivant les procédures de maintenance ?'),
('Quel type de maintenance est préventif ?'),
('Dans une chaîne de production, que signifie QC ?'),
('Quel équipement est utilisé pour mesurer la tension électrique ?'),
('Que fait un technicien de support N1 ?'),
('Quel outil permet de vérifier l''état des disques durs ?'),
('Quel protocole réseau permet de transférer des fichiers ?'),
('Comment appelle-t-on une panne imprévue ?');

INSERT INTO question (enonce) VALUES
('Quel document comptable résume les revenus et dépenses ?'),
('Qu''est-ce qu''un KPI ?'),
('Que signifie SWOT en stratégie d''entreprise ?'),
('Quel outil est utilisé pour planifier les projets ?'),
('Quel type de budget est lié aux dépenses courantes ?'),
('Quelle compétence est essentielle pour la gestion RH ?'),
('Qu''est-ce qu''un reporting mensuel ?'),
('Quel indicateur mesure la rentabilité d''une entreprise ?'),
('Quelle action correspond à la gestion du changement ?'),
('Quel est l''objectif principal de la gestion de la trésorerie ?');

-- azertyazerty

INSERT INTO reponse (id_question, texte, est_correcte) VALUES
(1, 'PHP', TRUE), (1, 'HTML', FALSE), (1, 'CSS', FALSE), (1, 'Python', FALSE),
(2, 'Structurer le contenu des pages web', TRUE), (2, 'Styliser les pages web', FALSE), (2, 'Programmer la logique backend', FALSE), (2, 'Gérer la base de données', FALSE),
(3, 'Tableau associatif', TRUE), (3, 'Array simple', FALSE), (3, 'Objet JSON', FALSE), (3, 'Fichier TXT', FALSE),
(4, 'Git', TRUE), (4, 'Subversion', FALSE), (4, 'Docker', FALSE), (4, 'Jenkins', FALSE),
(5, 'git pull', TRUE), (5, 'git commit', FALSE), (5, 'git push', FALSE), (5, 'git merge', FALSE),
(6, 'Interface pour communication entre applications', TRUE), (6, 'Base de données', FALSE), (6, 'Serveur web', FALSE), (6, 'Navigateur', FALSE),
(7, 'Structured Query Language', TRUE), (7, 'Simple Query List', FALSE), (7, 'System Quality Level', FALSE), (7, 'Server Quick Link', FALSE),
(8, 'Laravel', TRUE), (8, 'React', FALSE), (8, 'Bootstrap', FALSE), (8, 'Node.js', FALSE),
(9, 'POST', TRUE), (9, 'GET', FALSE), (9, 'DELETE', FALSE), (9, 'PUT', FALSE),
(10, 'Afficher une valeur dans la console', TRUE), (10, 'Envoyer un email', FALSE), (10, 'Créer un fichier', FALSE), (10, 'Modifier le HTML', FALSE);

INSERT INTO reponse (id_question, texte, est_correcte) VALUES
(11, 'Service Level Agreement', TRUE), (11, 'Standard Level Access', FALSE), (11, 'System Log Analysis', FALSE), (11, 'Security Log Alert', FALSE),
(12, 'Wireshark', TRUE), (12, 'Photoshop', FALSE), (12, 'Word', FALSE), (12, 'Excel', FALSE),
(13, 'Manuel de maintenance', TRUE), (13, 'Plan de projet', FALSE), (13, 'Rapport annuel', FALSE), (13, 'Procès-verbal', FALSE),
(14, 'Inspection régulière des machines', TRUE), (14, 'Réparer après panne', FALSE), (14, 'Ignorer les alertes', FALSE), (14, 'Modifier le logiciel', FALSE),
(15, 'Contrôle Qualité', TRUE), (15, 'Quantité Contrôlée', FALSE), (15, 'Qualification Code', FALSE), (15, 'Quick Check', FALSE),
(16, 'Multimètre', TRUE), (16, 'Tournevis', FALSE), (16, 'Clé Allen', FALSE), (16, 'Scie', FALSE),
(17, 'Résoudre les incidents de premier niveau', TRUE), (17, 'Développer une application', FALSE), (17, 'Gérer le budget', FALSE), (17, 'Planifier la production', FALSE),
(18, 'SMART Monitoring', TRUE), (18, 'Photoshop', FALSE), (18, 'Notepad', FALSE), (18, 'Excel', FALSE),
(19, 'FTP', TRUE), (19, 'HTTP', FALSE), (19, 'SMTP', FALSE), (19, 'DNS', FALSE),
(20, 'Panne', TRUE), (20, 'Maintenance', FALSE), (20, 'Optimisation', FALSE), (20, 'Calibration', FALSE);

INSERT INTO reponse (id_question, texte, est_correcte) VALUES
(21, 'Compte de résultat', TRUE), (21, 'Balance des paiements', FALSE), (21, 'Plan de trésorerie', FALSE), (21, 'Journal de bord', FALSE),
(22, 'Indicateur clé de performance', TRUE), (22, 'Plan d''action', FALSE), (22, 'Rapport annuel', FALSE), (22, 'Budget prévisionnel', FALSE),
(23, 'Strengths, Weaknesses, Opportunities, Threats', TRUE), (23, 'Sales, Work, Operations, Targets', FALSE), (23, 'System, Workflow, Organization, Timing', FALSE), (23, 'Strategy, Workload, Output, Trends', FALSE),
(24, 'Microsoft Project', TRUE), (24, 'Photoshop', FALSE), (24, 'Excel', FALSE), (24, 'Visual Studio', FALSE),
(25, 'Budget de fonctionnement', TRUE), (25, 'Budget d''investissement', FALSE), (25, 'Budget prévisionnel', FALSE), (25, 'Budget global', FALSE),
(26, 'Gestion des compétences et relations employé', TRUE), (26, 'Programmation', FALSE), (26, 'Maintenance des machines', FALSE), (26, 'Transport logistique', FALSE),
(27, 'Document détaillant les activités et résultats', TRUE), (27, 'Projet technique', FALSE), (27, 'Analyse de marché', FALSE), (27, 'Plan de production', FALSE),
(28, 'Rentabilité', TRUE), (28, 'Productivité', FALSE), (28, 'Qualité', FALSE), (28, 'Maintenance', FALSE),
(29, 'Implémenter des changements organisationnels', TRUE), (29, 'Réparer les machines', FALSE), (29, 'Programmer un site web', FALSE), (29, 'Analyser les données', FALSE),
(30, 'Assurer que l''entreprise dispose de liquidités suffisantes', TRUE), (30, 'Planifier les vacances', FALSE), (30, 'Réparer les ordinateurs', FALSE), (30, 'Gérer les serveurs', FALSE);

INSERT INTO menu_ui (nom, id_service, role) VALUES('menuDirecteurRH', 8, 'Manager');
INSERT INTO menu_ui (nom, id_service, role) VALUES('menuRH', 8, 'RH');
INSERT INTO menu_ui (nom, id_service, role) VALUES('menuKPISTOCK', (SELECT id_service FROM service WHERE nom = 'Kpi et Stock' LIMIT 1), 'Manager');

-- Menu pour Magasinier (Gestion des Stocks)
INSERT INTO menu_ui (nom, id_service, role) VALUES('menuMAGASIN', (SELECT id_service FROM service WHERE nom = 'Gestion des Stocks' LIMIT 1), 'Employé');


-- Menu pour Validateur (Transport & Distribution)
INSERT INTO menu_ui (nom, id_service, role) VALUES('menuLOG', (SELECT id_service FROM service WHERE nom = 'Transport & Distribution' LIMIT 1), 'Manager');

INSERT INTO contrat_travail_type (titre, duree_min, duree_max, renouvelable, max_duree_renouvellement, max_nb_renouvellement) VALUES
('CDI', NULL, NULL, 0, NULL, NULL),
('CDD', 1, 24, 1, 18, 2);

-- donne presence statut heure d'arrivée
INSERT INTO statut_pointage (heure, remarque, tolerance, jour) VALUES
('08:00:00', 'Heure normale', 10, 1),  -- Lundi
('08:00:00', 'Heure normale', 10, 2),  -- Mardi
('08:00:00', 'Heure normale', 10, 3),  -- Mercredi
('08:00:00', 'Heure normale', 10, 4),  -- Jeudi
('07:30:00', 'Heure normale', 10, 5);  -- Vendredi

-- donne absence

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

    
--donne heureSupp

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
(1, 'Demande acceptée pour le 5 octobre.', 'valide', '2023-10-02'),
(2, 'Demande refusée pour le 5 octobre.', 'refuse', '2023-10-02');

-- donne conge
-- Inserting types of leave
INSERT INTO type_conge (nom, description, remuneree, nb_jours_max) VALUES
('Congé payé', 'Congé avec salaire', 1, 30),  -- Paid leave
('Congé sans solde', 'Congé sans rémunération', 0, NULL),  -- Unpaid leave
('Congé maladie', 'Congé pour raisons médicales', 1, 15);  -- Sick leave

-- Inserting leave requests
INSERT INTO demande_conge (id_type_conge, id_employe, date_debut, date_fin, nb_jours) VALUES
(1, 1, '2025-01-01', '2025-01-10', 10),  -- Paid leave request for 2025
(2, 2, '2025-02-15', '2025-02-20', 5),  -- Unpaid leave request for 2025
(3, 3, '2025-03-23', '2025-03-29', 5),  -- Sick leave request for 2025
(1, 4, '2027-03-01', '2027-03-05', 5),  -- Paid leave for Zo Lalaina
(1, 5, '2025-05-10', '2025-05-15', 6),  -- Paid leave for Andry George
(1, 4, '2026-12-01', '2027-03-05', 5),  -- Paid leave for Zo Lalaina
(1, 4, '2027-11-01', '2027-11-05', 5),  -- Paid leave for Zo Lalaina
(1, 4, '2028-04-01', '2028-04-05', 5),  -- Paid leave for Zo Lalaina
(1, 4, '2032-01-01', '2032-01-05', 5);  -- Paid leave for Zo Lalaina


-- Inserting validation of leave requests
INSERT INTO validation_conge (id_demande_conge, statut, date_validation) VALUES
(1, 'valide', '2024-12-15'),  -- Approved leave for 2025
(2, 'refuse', '2024-12-16'),  -- Refused leave
(3, 'valide', '2024-12-17'),  -- Approved leave
(4, 'valide', '2024-12-18'),  -- Approved leave
(5, 'valide', '2024-12-19');  -- Approved leave

-- Modifications pour tester le filtre de date sur les statistiques
-- Mise à jour des dates de modification pour diversifier les périodes
UPDATE employe_statut SET date_modification = '2023-01-15' WHERE id_employe = 1;
UPDATE employe_statut SET date_modification = '2024-06-20' WHERE id_employe = 2;
UPDATE employe_statut SET date_modification = '2023-03-10' WHERE id_employe = 3;
UPDATE employe_statut SET date_modification = '2025-11-01' WHERE id_employe = 4;
UPDATE employe_statut SET date_modification = '2024-09-05' WHERE id_employe = 5;
UPDATE employe_statut SET date_modification = '2023-02-28' WHERE id_employe = 6;
UPDATE employe_statut SET date_modification = '2024-07-12' WHERE id_employe = 7;
UPDATE employe_statut SET date_modification = '2023-04-18' WHERE id_employe = 8;
UPDATE employe_statut SET date_modification = '2025-10-22' WHERE id_employe = 9;
UPDATE employe_statut SET date_modification = '2024-08-30' WHERE id_employe = 10;
UPDATE employe_statut SET date_modification = '2023-05-14' WHERE id_employe = 11;
UPDATE employe_statut SET date_modification = '2024-12-03' WHERE id_employe = 12;
UPDATE employe_statut SET date_modification = '2023-06-25' WHERE id_employe = 13;
UPDATE employe_statut SET date_modification = '2025-09-17' WHERE id_employe = 14;
UPDATE employe_statut SET date_modification = '2024-11-08' WHERE id_employe = 15;
UPDATE employe_statut SET date_modification = '2023-07-09' WHERE id_employe = 16;
UPDATE employe_statut SET date_modification = '2024-10-19' WHERE id_employe = 17;
UPDATE employe_statut SET date_modification = '2023-08-21' WHERE id_employe = 18;
UPDATE employe_statut SET date_modification = '2025-08-13' WHERE id_employe = 19;
UPDATE employe_statut SET date_modification = '2024-05-27' WHERE id_employe = 20;

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
(5, 17), -- Support Technique

-- Hanitra Razafmanantsoa (id_employe = 6)
(6, 1),  -- Programmation
(6, 3),  -- Gestion de Projet
(6, 10); -- Leadership


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

-- Contrats de travail
INSERT INTO contrat_travail (id_employe, id_type_contrat, debut, fin, salaire_base, date_signature, id_poste) VALUES
(1, 1, '2020-01-01', NULL, 1500000, '2020-01-01', 17),  -- CDI, pas de fin
(2, 1, '2018-06-10', NULL, 1200000, '2018-06-10', 18),  -- CDI
(3, 1, '2020-01-01', NULL, 2000000, '2020-01-01', 2),  -- CDI
(4, 2, '2025-11-23', '2025-12-23', 800000, '2025-11-23', 2),  -- CDD fin décembre 2025
(5, 2, '2025-11-23', '2026-01-23', 900000, '2025-11-23', 3),  -- CDD fin janvier 2026
(6, 2, '2025-11-28', '2025-12-28', 700000, '2025-11-28', 2); -- CDD pour Rakoto Jean

INSERT INTO document_type (nom) VALUES 
('contrat d''essai'),
('contrat de travail'),
('CIN'),
('certificat de résidence');

-- rohy
INSERT INTO jour_ferie (date, description, recurrence) VALUES
('2025-01-01', 'Nouvel An', 'annuel'),
('2025-01-01', '10eme anniversaire de lentreprise', 'fixe'),
('2025-02-08', 'Jour de la République', 'annuel'),
('2025-04-07', 'Fête de la Liberté', 'annuel'),
('2025-05-01', 'Fête du Travail', 'annuel'),
('2025-06-26', 'Fête de lIndépendance', 'annuel'),
('2025-08-15', 'Assomption', 'annuel'),
('2025-11-01', 'Toussaint', 'annuel'),
('2025-12-25', 'Noël', 'annuel');

-- Ajouts manquants pour poste_role
INSERT INTO poste_role (id_poste, id_role, date_role) VALUES
(2, 3, '2020-01-01'),  -- Développeur Backend = Employé
(3, 3, '2020-01-01'),  -- Développeur Frontend = Employé
(5, 3, '2020-01-01'),  -- Technicien Support N1 = Employé
(6, 2, '2020-01-01'),  -- Administrateur Systèmes = Manager
(7, 3, '2020-01-01'),  -- Opérateur Machine = Employé
(8, 2, '2020-01-01'),  -- Chef d'Équipe Production = Manager
(9, 3, '2020-01-01'),  -- Inspecteur Qualité = Employé
(10, 2, '2020-01-01'), -- Responsable Qualité = Manager
(11, 3, '2020-01-01'), -- Ingénieur R&D = Employé
(12, 2, '2020-01-01'), -- Chef de Projet Innovation = Manager
(13, 3, '2020-01-01'), -- Dessinateur Industriel = Employé
(14, 2, '2020-01-01'), -- Ingénieur Conception = Manager
(15, 3, '2020-01-01'), -- Comptable = Employé
(16, 2, '2020-01-01'), -- Contrôleur de Gestion = Manager
(21, 3, '2020-01-01'), -- Magasinier = Employé
(22, 2, '2020-01-01'), -- Responsable Entrepôt = Manager
(23, 3, '2020-01-01'), -- Chauffeur Poids Lourd = Employé
(24, 2, '2020-01-01'); -- Coordinateur Logistique = Manager

INSERT INTO assurance (id_assurance, nom, minpay, maxpay, taux) VALUES
(1, 'Retenue CNaPS', NULL, NULL, 1),
(2, 'Retenue sanitaire', NULL, NULL, 5),
(3, 'Tranche IRSA 1', 0, 350000, 0),
(4, 'Tranche IRSA 2', 350001, 400000, 5),
(5, 'Tranche IRSA 3', 400001, 500000, 10),
(6, 'Tranche IRSA 4', 500001, 600000, 15),
(7, 'Tranche IRSA 5', 600001, 4000000, 20),
(8, 'Tranche IRSA 6', 4000001, NULL, 25);

INSERT INTO taux_heures_sup (type_heuresup, heure_debut, heure_fin, taux) VALUES
('Heures sup 25%', 1, 2, 25),
('Heures sup 50%', 3, 10, 50),
('Heures sup 100%', 11, 9999, 100);

INSERT INTO prime (nom, description, montant, type_prime) VALUES
('Prime de rendement', 'Prime accordée pour performance exceptionnelle', 50000, 'mensuelle'),
('Prime d\'anciennete', 'Prime en fonction des annees d\'anciennete', 20000, 'annuelle'),
('Prime de Noel', 'Prime exceptionnelle de fin d\'annee', 100000, 'ponctuelle'),
('Prime de presence', 'Prime pour presence parfaite sur le mois', 15000, 'mensuelle'),
('Prime de projet', 'Prime pour reussite d\'un projet specifique', 30000, 'ponctuelle');


INSERT INTO pourcentage_avance (pourcentage, date)
VALUES
(30, '2025-12-07'),
(50, '2025-12-07');


-- Insert 5 employees (one INSERT per row so LAST_INSERT_ID() returns the correct id)
INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(NULL, 'Diallo', 'Mamadou', 'mamadou.diallo@example.com', '221770000001', 'M', '2025-01-01');
SET @e1 = LAST_INSERT_ID();

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(NULL, 'Nguyen', 'Linh', 'linh.nguyen@example.com', '221770000002', 'F', '2025-02-01');
SET @e2 = LAST_INSERT_ID();

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(NULL, 'Smith', 'Alice', 'alice.smith@example.com', '221770000003', 'F', '2025-03-01');
SET @e3 = LAST_INSERT_ID();

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(NULL, 'Kouame', 'Eric', 'eric.kouame@example.com', '221770000004', 'M', '2025-04-01');
SET @e4 = LAST_INSERT_ID();

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(NULL, 'Garcia', 'María', 'maria.garcia@example.com', '221770000005', 'F', '2025-05-01');
SET @e5 = LAST_INSERT_ID();

-- Ensure contract types exist (creates CDD and CDI if not present)
INSERT INTO contrat_travail_type (titre, duree_min, duree_max, renouvelable, max_duree_renouvellement, max_nb_renouvellement)
VALUES
('CDD', 1, 12, 1, 6, 2),
('CDI', 12, NULL, 0, NULL, NULL);

-- Use the previously captured employee ids (@e1..@e5) to create contracts for 2025-2026
INSERT INTO contrat_travail (id_type_contrat, id_employe, debut, fin, salaire_base, date_signature, date_creation, id_poste, pathPdf) VALUES
(1, @e1, '2025-01-01', '2026-01-01', 30000.00, '2025-01-01', CURRENT_DATE, NULL, NULL),
(1, @e2, '2025-02-01', '2026-02-01', 32000.00, '2025-02-01', CURRENT_DATE, NULL, NULL),
(2, @e3, '2025-03-01', '2026-03-01', 35000.00, '2025-03-01', CURRENT_DATE, NULL, NULL),
(2, @e4, '2025-04-01', '2026-04-01', 34000.00, '2025-04-01', CURRENT_DATE, NULL, NULL),
(1, @e5, '2025-05-01', '2026-05-01', 31000.00, '2025-05-01', CURRENT_DATE, NULL, NULL);

-- Link contract status entries (example)
INSERT INTO contrat_employe_statut (id_contrat_travail, id_employe_statut, date_ajout) VALUES
(LAST_INSERT_ID() - 4, 1, CURRENT_DATE),
(LAST_INSERT_ID() - 3, 1, CURRENT_DATE),
(LAST_INSERT_ID() - 2, 1, CURRENT_DATE),
(LAST_INSERT_ID() - 1, 1, CURRENT_DATE),
(LAST_INSERT_ID(), 1, CURRENT_DATE);

-- 1) Insert pourcentage_avance rows and capture ids
INSERT INTO pourcentage_avance (pourcentage, date) VALUES (30, '2025-11-20');
SET @p1 = LAST_INSERT_ID();
INSERT INTO pourcentage_avance (pourcentage, date) VALUES (50, '2025-12-07');
SET @p2 = LAST_INSERT_ID();

-- 2) Assign some primes to employees (uses existing prime ids from your seeds: 1..5)
INSERT INTO employe_prime (id_employe, id_prime, mois, annee) VALUES
(@e1, 1, 11, 2025),
(@e1, 4, 11, 2025),
(@e2, 1, 12, 2025),
(@e3, 2, 3, 2025),
(@e4, 5, 6, 2025),
(@e5, 1, 5, 2025);

-- 3) Advance salary requests for employees (use pourcentage ids @p1/@p2)
INSERT INTO avance_salaire (id_employe, id_pourcentage, montant, date_avance, statut) VALUES
(@e1, @p1, 150000.00, '2025-11-20', 'accordee'),
(@e2, @p2, 250000.00, '2025-12-07', 'demandee'),
(@e3, @p1, 100000.00, '2025-11-15', 'accordee');

-- 4) Overtime requests (demande_heure_sup) and capture ids
INSERT INTO demande_heure_sup (id_employe, date_demande) VALUES
(@e1, '2025-11-05 09:00:00');
SET @d1 = LAST_INSERT_ID();

INSERT INTO demande_heure_sup (id_employe, date_demande) VALUES
(@e1, '2025-12-03 14:30:00');
SET @d2 = LAST_INSERT_ID();

INSERT INTO demande_heure_sup (id_employe, date_demande) VALUES
(@e2, '2025-11-10 10:00:00');
SET @d3 = LAST_INSERT_ID();

INSERT INTO demande_heure_sup (id_employe, date_demande) VALUES
(@e3, '2025-12-01 08:30:00');
SET @d4 = LAST_INSERT_ID();

-- 5) Details for overtime requests
INSERT INTO detail_heure_sup (id_demande_heure_sup, heure_debut, heure_fin, date_debut, date_fin) VALUES
(@d1, '17:00:00', '20:00:00', '2025-11-10', '2025-11-10'),  -- 3h for e1 (Nov)
(@d2, '18:00:00', '20:00:00', '2025-12-08', '2025-12-08'),  -- 2h for e1 (Dec)
(@d3, '18:30:00', '21:00:00', '2025-11-12', '2025-11-12'),  -- 2.5h for e2
(@d4, '19:00:00', '22:00:00', '2025-12-05', '2025-12-05');  -- 3h for e3

-- 6) Validations for overtime (approval)
INSERT INTO validation_heure_sup (id_demande_heure_sup, commentaire, statut, date_validation) VALUES
(@d1, 'Heures sup validées pour novembre.', 'valide', '2025-11-06'),
(@d2, 'Heures sup validées pour décembre.', 'valide', '2025-12-04'),
(@d3, 'Heures sup validées.', 'valide', '2025-11-13'),
(@d4, 'Heures sup validées.', 'valide', '2025-12-06');

-- 7) Example entries for employe_prime additional months (optional)
INSERT INTO employe_prime (id_employe, id_prime, mois, annee) VALUES
(@e4, 1, 11, 2025),
(@e5, 4, 12, 2025);

INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES
(@e1, 2, 1, NOW()),  -- Diallo Mamadou -> Développeur Backend (service 1, dept 1)
(@e2, 3, 1, NOW()),  -- Nguyen Linh -> Développeur Frontend (service 1, dept 1)
(@e3, 5, 1, NOW()),  -- Smith Alice -> Technicien Support N1 (service 2, dept 1)
(@e4, 6, 1, NOW()),  -- Kouame Eric -> Administrateur Systèmes (service 2, dept 1)
(@e5, 15, 1, NOW()); -- Garcia María -> Comptable (service 7, dept 4)


INSERT INTO employe_prime (id_employe, id_prime, mois, annee) VALUES
(8, 1, 10, 2025);

INSERT INTO avance_salaire (id_employe, id_pourcentage, montant, date_avance, statut) VALUES
(8, 2, 150000.00, '2025-10-15', 'accordee');