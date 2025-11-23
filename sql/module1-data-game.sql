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
('Rajoelina', 'Andry', 'andryRajojo@gmail.com', '987456321', 'M', '2020-01-01');

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(1, 'Ravatomanga', 'Mamy', 'mamyRavato@gmail.com', '123456789', 'M', '2020-01-01'),
(2, 'Rajoelina', 'Andry', 'andryRajojo@gmail.com', '987456321', 'M', '2020-01-01');

INSERT INTO employe_statut (id_employe, id_poste, activite) VALUES
(1, 17, 1),  
(2, 18, 1);  

INSERT INTO role (nom) VALUES
('Administrateur'),
('Manager'),
('Employé'),
('RH'),
('Responsable Sécurité');

INSERT INTO user (username, pwd, id_employe) VALUES
('mamy.ravato', '123', 1),
('dj.rajojo', '123', 2);

INSERT INTO poste_role (id_poste, id_role, date_role) VALUES
(17, 2, '2020-01-15'),  -- Directeur RH = Manager
(18, 4, '2018-06-10');  -- Responsable recrutement = RH

INSERT INTO candidat (nom, prenom, email, telephone, genre, date_naissance) VALUES
('alice', 'dupont', 'aliceDupont@gmail.com', '0348366414', 'F', '2020-01-01');

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(3, 'alice', 'dupont', 'aclieDupont@gmail.com', '0348366414', 'F', '2020-01-01');

INSERT INTO employe_statut (id_employe, id_poste, activite) VALUES
(3, 2, 1);  

INSERT INTO user (username, pwd, id_employe) VALUES
('alice.dupont', '123', 3);

INSERT INTO poste_role (id_poste, id_role, date_role) VALUES
(4, 2, '2020-01-01');  -- Chef de Projet IT = Manager 


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
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/seeAllQcm', 'RH');

-- Employés (Gestion)
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/employes', 'Manager', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/employes', 'RH');


-- Contrats 
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/contratCrea', 'Administrateur', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/contratCrea', 'RH', 8);
-- scoring
-- ======================

INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/annonceListe', 'Administrateur', 8);
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES ('/annonceListe', 'RH' , 8);

-- ======================
-- scoring et qcm
INSERT INTO type_scoring (nom) VALUES
('QCM'),
('entretien')

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