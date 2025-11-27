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
-- Transport & Distribution
('Chauffeur Poids Lourd', 10),
('Coordinateur Logistique', 10);

INSERT INTO candidat (nom, prenom, email, telephone, genre, date_naissance) VALUES
('Ravatomanga', 'Mamy', 'mamyRavato@gmail.com', '123456789', 'M', '2020-01-01'),
('Rajoelina', 'Andry', 'andryRajojo@gmail.com', '987456321', 'M', '2020-01-01'),
('alice', 'dupont', 'aliceDupont@gmail.com', '0348366414', 'F', '2020-01-01'),
('Lalaina', 'Zo', 'zo.lalaina@gmail.com', '0341234567', 'M', '1995-05-15'),
('George', 'Andry', 'andry.george@gmail.com', '0347654321', 'M', '1993-08-20');

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(1, 'Ravatomanga', 'Mamy', 'mamyRavato@gmail.com', '123456789', 'M', '2020-01-01'),
(2, 'Rajoelina', 'Andry', 'andryRajojo@gmail.com', '987456321', 'M', '2020-01-01'),
(3, 'alice', 'dupont', 'aclieDupont@gmail.com', '0348366414', 'F', '2020-01-01'),
(4, 'Lalaina', 'Zo', 'zo.lalaina@gmail.com', '0341234567', 'M', '2025-11-23'),
(5, 'George', 'Andry', 'andry.george@gmail.com', '0347654321', 'M', '2025-11-23');

INSERT INTO employe_statut (id_employe, id_poste, activite) VALUES
(1, 17, 1),  
(2, 18, 1),
(3, 2, 1),
(4, 2, 1),  -- Zo Lalaina = Développeur Backend
(5, 3, 1);  -- Andry George = Développeur Frontend

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
('andry', '123', 5);

INSERT INTO poste_role (id_poste, id_role, date_role) VALUES
(17, 2, '2020-01-15'),  -- Directeur RH = Manager
(18, 4, '2018-06-10'),  -- Responsable recrutement = RH
(4, 2, '2020-01-01'),  -- Chef de Projet IT = Manager 
(19, 3, '2025-11-23'),  -- Zo Lalaina = Employé
(20, 3, '2025-11-23');  -- Andry George = Employé


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
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/annonceCrea', 'Administrateur', 8);

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
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/employes', 'Manager', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/employes', 'RH', 8);


-- Contrats 
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/contratCrea', 'Administrateur', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/contratCrea', 'RH', 8);
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

INSERT INTO contrat_travail_type (titre, duree_min, duree_max, renouvelable, max_duree_renouvellement, max_nb_renouvellement) VALUES
('CDI', NULL, NULL, 0, NULL, NULL),
('CDD', 1, 24, 1, 18, 2);




-- Fenitra
-- Ajout de plus de candidats pour tester les statistiques
INSERT INTO candidat (nom, prenom, email, telephone, genre, date_naissance) VALUES
('Dupont', 'Marie', 'marie.dupont@example.com', '0123456789', 'F', '1990-05-15'),
('Martin', 'Pierre', 'pierre.martin@example.com', '0987654321', 'M', '1985-03-22'),
('rain', 'Sophie', 'sophie.rain@example.com', '0567891234', 'F', '1992-11-08'),
('Bernard', 'Jean', 'jean.bernard@example.com', '0456123789', 'M', '1988-07-30'),
('Dubois', 'Claire', 'claire.dubois@example.com', '0345678912', 'F', '1995-01-12'),
('Moreau', 'Luc', 'luc.moreau@example.com', '0765432198', 'M', '1980-09-05'),
('Simon', 'Emma', 'emma.simon@example.com', '0654321987', 'F', '1993-04-18'),
('Michel', 'Antoine', 'antoine.michel@example.com', '0876543210', 'M', '1987-12-25'),
('Thomas', 'Julie', 'julie.thomas@example.com', '0234567891', 'F', '1991-06-14'),
('Robert', 'Nicolas', 'nicolas.robert@example.com', '0987123456', 'M', '1984-08-09'),
('Richard', 'Camille', 'camille.richard@example.com', '0345987123', 'F', '1994-02-28'),
('Petit', 'Maxime', 'maxime.petit@example.com', '0765891234', 'M', '1989-10-17'),
('Durand', 'Laura', 'laura.durand@example.com', '0456789123', 'F', '1996-03-07'),
('Roux', 'Alexandre', 'alexandre.roux@example.com', '0876543298', 'M', '1982-11-20'),
('Vincent', 'Chloé', 'chloe.vincent@example.com', '0234987654', 'F', '1997-05-03');

-- Ajout d'employés correspondants
INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(6, 'Dupont', 'Marie', 'marie.dupont@example.com', '0123456789', 'F', '2020-02-01'),
(7, 'Martin', 'Pierre', 'pierre.martin@example.com', '0987654321', 'M', '2019-08-15'),
(8, 'Leroy', 'Sophie', 'sophie.leroy@example.com', '0567891234', 'F', '2021-01-10'),
(9, 'Bernard', 'Jean', 'jean.bernard@example.com', '0456123789', 'M', '2018-05-20'),
(10, 'Dubois', 'Claire', 'claire.dubois@example.com', '0345678912', 'F', '2022-03-05'),
(11, 'Moreau', 'Luc', 'luc.moreau@example.com', '0765432198', 'M', '2017-11-12'),
(12, 'Simon', 'Emma', 'emma.simon@example.com', '0654321987', 'F', '2020-07-22'),
(13, 'Michel', 'Antoine', 'antoine.michel@example.com', '0876543210', 'M', '2019-09-30'),
(14, 'Thomas', 'Julie', 'julie.thomas@example.com', '0234567891', 'F', '2021-04-18'),
(15, 'Robert', 'Nicolas', 'nicolas.robert@example.com', '0987123456', 'M', '2018-12-08'),
(16, 'Richard', 'Camille', 'camille.richard@example.com', '0345987123', 'F', '2022-06-14'),
(17, 'Petit', 'Maxime', 'maxime.petit@example.com', '0765891234', 'M', '2017-03-25'),
(18, 'Durand', 'Laura', 'laura.durand@example.com', '0456789123', 'F', '2020-10-05'),
(19, 'Roux', 'Alexandre', 'alexandre.roux@example.com', '0876543298', 'M', '2019-01-15'),
(20, 'Vincent', 'Chloé', 'chloe.vincent@example.com', '0234987654', 'F', '2021-08-30');

-- Ajout de statuts pour ces employés, en les répartissant dans différents services
INSERT INTO employe_statut (id_employe, id_poste, activite) VALUES
(6, 2, 1),   -- Marie Dupont: Développeur Backend (Développement Logiciel)
(7, 3, 1),   -- Pierre Martin: Développeur Frontend (Développement Logiciel)
(8, 5, 1),   -- Sophie Leroy: Technicien Support N1 (Support Technique)
(9, 6, 1),   -- Jean Bernard: Administrateur Systèmes (Support Technique)
(10, 7, 1),  -- Claire Dubois: Opérateur Machine (Chaîne de Montage)
(11, 8, 1),  -- Luc Moreau: Chef d'Équipe Production (Chaîne de Montage)
(12, 9, 1),  -- Emma Simon: Inspecteur Qualité (Contrôle Qualité)
(13, 10, 1), -- Antoine Michel: Responsable Qualité (Contrôle Qualité)
(14, 11, 1), -- Julie Thomas: Ingénieur R&D (Recherche & Développement)
(15, 12, 1), -- Nicolas Robert: Chef de Projet Innovation (Recherche & Développement)
(16, 13, 1), -- Camille Richard: Dessinateur Industriel (Bureau d'Études)
(17, 14, 1), -- Maxime Petit: Ingénieur Conception (Bureau d'Études)
(18, 15, 1), -- Laura Durand: Comptable (Comptabilité & Finance)
(19, 16, 1), -- Alexandre Roux: Contrôleur de Gestion (Comptabilité & Finance)
(20, 19, 1); -- Chloé Vincent: Responsable Formation (Ressources Humaines)

-- Ajout de quelques employés inactifs pour diversifier
UPDATE employe_statut SET activite = 0 WHERE id_employe IN (5, 10, 15);

-- Ajout d'utilisateurs pour certains employés
INSERT INTO user (username, pwd, id_employe) VALUES
('marie.dupont', '123', 4),
('pierre.martin', '123', 5),
('sophie.leroy', '123', 6),
('jean.bernard', '123', 7),
('claire.dubois', '123', 8);
-- donne presence statut heure d'arrivée
INSERT INTO statut_pointage (heure, remarque, tolerance, jour) VALUES
('08:00:00', 'Heure normale', 10, 1),  -- Lundi
('08:00:00', 'Heure normale', 10, 2),  -- Mardi
('08:00:00', 'Heure normale', 10, 3),  -- Mercredi
('08:00:00', 'Heure normale', 10, 4),  -- Jeudi
('07:30:00', 'Heure normale', 10, 5),  -- Vendredi
('09:00:00', 'Heure normale', 10, 6);  -- Samedi

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
('08:00:00', 'Heure normale', 10, 6);  -- Samedi


INSERT INTO statut_pointage (heure, remarque, tolerance, jour) VALUES
('14:30:00', 'Heure normale', 10, 7); 

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

    
-- donne heureSupp

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
(1, 1, '2023-11-01', '2023-11-10', 10),  -- Paid leave request
(2, 2, '2023-11-15', '2023-11-20', 5),  -- Unpaid leave request
(3, 3, '2023-12-01', '2023-12-05', 5);  -- Sick leave request

-- Inserting validation of leave requests
INSERT INTO validation_conge (id_demande_conge, statut, date_validation) VALUES
(1, 'valide', '2023-10-28'),  -- Approved leave
(2, 'refuse', '2023-10-28');  -- Approved leave

