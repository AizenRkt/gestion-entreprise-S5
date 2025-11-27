DROP DATABASE if exists gestion_entreprise;
CREATE DATABASE if not exists gestion_entreprise;

USE gestion_entreprise;

-- ======================
-- utilisateur, role, métier
-- ======================
CREATE TABLE departement (
    id_dept INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE service (
    id_service INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    id_dept INT,
    FOREIGN KEY (id_dept) REFERENCES departement(id_dept)
);

CREATE TABLE poste (
    id_poste INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    id_service INT,
    FOREIGN KEY (id_service) REFERENCES service(id_service)
);

CREATE TABLE candidat (
    id_candidat INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150)  NOT NULL,
    telephone VARCHAR(20),
    genre VARCHAR(1),
    date_naissance DATE,
    date_candidature DATE DEFAULT CURRENT_DATE
);

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

CREATE TABLE employe_statut (
    id_employe_statut INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    id_poste INT NOT NULL,
    activite INT NOT NULL, -- 0 (pas actif) et 1 (actif)
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe),
    FOREIGN KEY (id_poste) REFERENCES poste(id_poste)
);

CREATE TABLE role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    pwd VARCHAR(255) NOT NULL,
    id_employe INT UNIQUE,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE poste_role (
    id_poste_role INT AUTO_INCREMENT PRIMARY KEY,
    id_poste INT NOT NULL,
    id_role INT NOT NULL,
    date_role DATE NOT NULL,
    FOREIGN KEY (id_role) REFERENCES role(id_role)
);

-- ======================
-- profileur
-- ======================
CREATE TABLE profil (
    id_profil INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE diplome (
    id_diplome INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE competence (
    id_competence INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE ville (
    id_ville INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

-- ======================
-- annonces et critères
-- ======================
CREATE TABLE annonce (
    id_annonce INT AUTO_INCREMENT PRIMARY KEY,
    id_profil INT,
    titre VARCHAR(255) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    age_min INT,
    age_max INT,
    experience INT,
    objectif VARCHAR(255),
    qualite VARCHAR(255),
    FOREIGN KEY (id_profil) REFERENCES profil(id_profil) ON DELETE CASCADE
);

CREATE TABLE statut_annonce (
    id_statut_annonce INT AUTO_INCREMENT PRIMARY KEY,
    id_annonce INT NOT NULL,
    valeur ENUM('retrait','renouvellement') NOT NULL,
    date_fin DATE,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE
);

CREATE TABLE detail_annonce (
    id_detail_annonce INT AUTO_INCREMENT PRIMARY KEY,
    id_annonce INT NOT NULL,
    type ENUM('ville','diplome','competence') NOT NULL,
    id_item INT NOT NULL,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE,
    UNIQUE KEY unique_annonce_item (id_annonce, type, id_item)
);

-- ======================
-- cv
-- ======================
CREATE TABLE cv (
    id_cv INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT NOT NULL,
    id_profil INT NOT NULL,
    date_soumission DATE DEFAULT CURRENT_DATE,
    photo VARCHAR(255),
    FOREIGN KEY (id_profil) REFERENCES profil(id_profil) ON DELETE CASCADE,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat)
);

CREATE TABLE detail_cv (
    id_detail_cv INT AUTO_INCREMENT PRIMARY KEY,
    id_cv INT NOT NULL,
    type ENUM('ville','diplome','competence') NOT NULL,
    id_item INT NOT NULL,
    FOREIGN KEY (id_cv) REFERENCES cv(id_cv) ON DELETE CASCADE,
    UNIQUE KEY unique_cv_item (id_cv, type, id_item)
);

-- ======================
-- postulance
-- ======================
CREATE TABLE postulance (
    id_postulance INT AUTO_INCREMENT PRIMARY KEY,
    id_cv INT NOT NULL,
    id_annonce INT NOT NULL,
    date_postulation DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (id_cv) REFERENCES cv(id_cv) ON DELETE CASCADE,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE
);

-- ======================
-- QCM, question, réponse
-- ======================
CREATE TABLE qcm (
    id_qcm INT AUTO_INCREMENT PRIMARY KEY,
    id_profil INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    note_max DECIMAL(5,2) NOT NULL,  
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_profil) REFERENCES profil(id_profil) ON DELETE CASCADE
);

CREATE TABLE question (
    id_question INT AUTO_INCREMENT PRIMARY KEY,
    enonce VARCHAR(255) NOT NULL
);

CREATE TABLE reponse (
    id_reponse INT AUTO_INCREMENT PRIMARY KEY,
    id_question INT NOT NULL,
    texte TEXT NOT NULL,
    est_correcte BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_question) REFERENCES question(id_question) ON DELETE CASCADE
);

CREATE TABLE detail_qcm (
    id_detail_qcm INT AUTO_INCREMENT PRIMARY KEY,
    id_qcm INT NOT NULL,
    id_question INT NOT NULL,
    bareme_question DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (id_qcm) REFERENCES qcm(id_qcm) ON DELETE CASCADE,
    FOREIGN KEY (id_question) REFERENCES question(id_question) ON DELETE CASCADE
);

-- ======================
-- entretien
-- ======================
CREATE TABLE entretien_candidat (
    id_entretien INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT NOT NULL,
    date DATETIME NOT NULL,
    duree INT,
    id_user INT,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat),
    FOREIGN KEY (id_user) REFERENCES user(id_user)
);

CREATE TABLE detail_entretien (
    id_detail_entretien INT AUTO_INCREMENT PRIMARY KEY,
    id_entretien INT NOT NULL,
    evaluation ENUM('recommande','refuse') NOT NULL, 
    commentaire VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_entretien) REFERENCES entretien_candidat(id_entretien)
);

-- ======================
-- scoring
-- ======================
CREATE TABLE type_scoring (
    id_type_scoring INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE scoring (
    id_scoring INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT NOT NULL,
    id_type_scoring INT NOT NULL,
    id_item INT NOT NULL,
    valeur DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat),
    FOREIGN KEY (id_type_scoring) REFERENCES type_scoring(id_type_scoring)
);

-- ======================
-- contrat d’essai et renouvellement
-- ======================
CREATE TABLE contrat_essai (
    id_contrat_essai INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT NOT NULL,
    debut DATE NOT NULL,
    fin DATE NOT NULL,
    pathPdf VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat)
);

-- ======================
-- permissions
-- ======================
CREATE TABLE route_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_pattern VARCHAR(191) NOT NULL,
    role_name VARCHAR(50) NOT NULL,
    id_service INT NOT NULL,
    UNIQUE KEY unique_route_role (route_pattern, role_name),
    FOREIGN KEY (id_service) REFERENCES service(id_service)
);

CREATE TABLE menu_ui (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    id_service INT NOT NULL,
    role VARCHAR(255),
    FOREIGN KEY (id_service) REFERENCES service(id_service)
);

-- ======= Partie 2 =========
CREATE TABLE contrat_essai_statut (
    id_statut_contrat_essai INT AUTO_INCREMENT PRIMARY KEY,
    id_contrat_essai INT NOT NULL,
    statut ENUM('valider', 'annuler'),
    date_statut DATE,
    commentaire TEXT,
    FOREIGN KEY (id_contrat_essai) REFERENCES contrat_essai(id_contrat_essai)
);

CREATE TABLE contrat_essai_renouvellement (
    id_renouvellement_essai INT AUTO_INCREMENT PRIMARY KEY,
    id_contrat_essai INT NOT NULL NOT NULL,
    nouvelle_date_fin DATE,
    date_renouvellement DATE NOT NULL,
    date_fin DATE NOT NULL,
    commentaire TEXT,
    FOREIGN KEY (id_contrat_essai) REFERENCES contrat_essai(id_contrat_essai)
);

-- partie contrat de travail CDI et CDD
CREATE TABLE contrat_travail_type (
    id_type_contrat INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(25),
    duree_min INT,
    duree_max INT,
    renouvelable TINYINT,
    max_duree_renouvellement INT,
    max_nb_renouvellement INT
);

CREATE TABLE contrat_travail (
    id_contrat_travail INT AUTO_INCREMENT PRIMARY KEY,
    id_type_contrat INT NOT NULL,
    id_employe INT NOT NULL,
    debut DATE NOT NULL,
    fin DATE NULL,
    salaire_base DECIMAL(10,2),
    date_signature DATE NULL,
    date_creation DATE NOT NULL DEFAULT CURRENT_DATE,
    id_poste INT NULL,
    pathPdf VARCHAR(255),

    FOREIGN KEY (id_type_contrat) REFERENCES contrat_travail_type(id_type_contrat),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe),
    FOREIGN KEY (id_poste) REFERENCES poste(id_poste)
);

CREATE TABLE contrat_travail_renouvellement (
    id_renouvellement INT AUTO_INCREMENT PRIMARY KEY,
    id_contrat_travail INT NOT NULL,
    nouvelle_date_fin DATE,
    commentaire TEXT,
    date_renouvellement DATE NOT NULL,
    date_creation DATE NOT NULL,
    pathPdf VARCHAR(255),
    FOREIGN KEY (id_contrat_travail) REFERENCES contrat_travail(id_contrat_travail)
);

-- partie document
CREATE TABLE document_type (
    id_type_document INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE document (
    id_document INT AUTO_INCREMENT PRIMARY KEY,
    id_type_document INT NOT NULL,
    id_employe INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    pathScan VARCHAR(255),
    dateUpload DATE NOT NULL,
    date_expiration DATE,
    FOREIGN KEY (id_type_document) REFERENCES document_type(id_type_document),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE document_statut (
    id_statut_document INT AUTO_INCREMENT PRIMARY KEY,
    id_document INT NOT NULL,
    statut ENUM('valide', 'expire', 'annule') NOT NULL,
    date_statut DATE NOT NULL,
    commentaire TEXT,
    FOREIGN KEY (id_document) REFERENCES document(id_document)
);

-- partie congé
CREATE TABLE type_conge (
    id_type_conge INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    remuneree TINYINT(1) DEFAULT 0,
    nb_jours_max INT DEFAULT NULL
);

CREATE TABLE demande_conge (
    id_demande_conge INT AUTO_INCREMENT PRIMARY KEY,
    id_type_conge INT NOT NULL,
    id_employe INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    nb_jours INT NOT NULL,
    FOREIGN KEY (id_type_conge) REFERENCES type_conge(id_type_conge),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE validation_conge (
    id_validation_conge INT AUTO_INCREMENT PRIMARY KEY,
    id_demande_conge INT NOT NULL,
    statut ENUM('valide', 'refuse') NOT NULL,
    date_validation DATE,
    FOREIGN KEY (id_demande_conge) REFERENCES demande_conge(id_demande_conge)
);

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

-- partie heure sup
CREATE TABLE max_heure_sup (
    id_max_heure_sup INT AUTO_INCREMENT PRIMARY KEY,
    nb_heures_max_par_semaine INT NOT NULL,
    date_application DATE NOT NULL
);

CREATE TABLE demande_heure_sup (
    id_demande_heure_sup INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    date_demande DATETIME NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE detail_heure_sup (
    id_detail_heure_sup INT AUTO_INCREMENT PRIMARY KEY,
    id_demande_heure_sup INT NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    FOREIGN KEY (id_demande_heure_sup) REFERENCES demande_heure_sup(id_demande_heure_sup)
);

CREATE TABLE validation_heure_sup (
    id_validation_heure_sup INT AUTO_INCREMENT PRIMARY KEY,
    id_demande_heure_sup INT NOT NULL,
    commentaire TEXT,
    statut ENUM('valide', 'refuse') NOT NULL,
    date_validation DATE NOT NULL,
    FOREIGN KEY (id_demande_heure_sup) REFERENCES demande_heure_sup(id_demande_heure_sup)
);

--pointage
CREATE TABLE statut_pointage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    heure TIME,
    remarque VARCHAR(255),
    tolerance INT,
    jour INT CHECK (jour BETWEEN 1 AND 7)  -- 1 = lundi, 7 = dimanche
);

CREATE TABLE checkin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    datetime_checkin DATETIME NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE checkout (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    datetime_checkout DATETIME NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);


CREATE TABLE pointage (
    id_pointage INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    id_checkin INT,
    id_checkout INT,
    retard_min INT,
    duree_work TIME,
    date_pointage DATE NOT NULL,
    statut VARCHAR(50),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe),
    FOREIGN KEY (id_checkin) REFERENCES checkin(id),
    FOREIGN KEY (id_checkout) REFERENCES checkout(id),
    UNIQUE KEY unique_pointage_jour (id_employe, date_pointage)
);

--view

CREATE OR REPLACE VIEW view_absence_details AS
SELECT 
    a.id_absence,
    e.id_employe,
    e.nom AS employe_nom,
    e.prenom AS employe_prenom,
    ta.nom AS type_absence,
    a.date_debut AS absence_date_debut,
    a.date_fin AS absence_date_fin,
    da.type_documentation,
    da.motif,
    da.date_documentation,
    CASE 
        WHEN v.id_documentation_absence IS NOT NULL THEN 'Validé'
        WHEN v.id_documentation_absence IS NULL AND da.id_documentation_absence IS NOT NULL THEN 'En attente'
        ELSE 'Archivé'
    END AS validation_status
FROM 
    employe e
JOIN 
    absence a ON e.id_employe = a.id_employe
JOIN 
    type_absence ta ON a.id_type_absence = ta.id_type_absence
JOIN 
    documentation_absence da ON e.id_employe = da.id_employe
LEFT JOIN 
    validation_documentation_absence v ON da.id_documentation_absence = v.id_documentation_absence AND a.id_absence = v.id_absence;

CREATE OR REPLACE VIEW view_heure_sup_details AS
SELECT 
    d.id_demande_heure_sup,
    e.id_employe,
    e.nom AS employe_nom,
    e.prenom AS employe_prenom,
    d.date_demande,
    dh.heure_debut,
    dh.heure_fin,
    dh.date_debut AS date_heure_debut,
    dh.date_fin AS date_heure_fin,
    CASE 
        WHEN v.statut = 'valide' THEN 'Validé'
        WHEN v.statut = 'refuse' THEN 'Refusé'
        ELSE 'En attente'
    END AS validation_statut
FROM 
    demande_heure_sup d
JOIN 
    employe e ON d.id_employe = e.id_employe
JOIN 
    detail_heure_sup dh ON d.id_demande_heure_sup = dh.id_demande_heure_sup
LEFT JOIN 
    validation_heure_sup v ON d.id_demande_heure_sup = v.id_demande_heure_sup;


CREATE OR REPLACE VIEW view_conge_details AS
SELECT 
    d.id_demande_conge,
    e.id_employe,
    e.nom AS employe_nom,
    e.prenom AS employe_prenom,
    d.date_debut,
    d.date_fin,
    d.nb_jours,
    t.nom AS type_conge_nom,
    CASE 
        WHEN v.statut = 'valide' THEN 'Validé'
        WHEN v.statut = 'refuse' THEN 'Refusé'
        ELSE 'En attente'
    END AS validation_statut
FROM 
    demande_conge d
JOIN 
    employe e ON d.id_employe = e.id_employe
JOIN 
    type_conge t ON d.id_type_conge = t.id_type_conge
LEFT JOIN 
    validation_conge v ON d.id_demande_conge = v.id_demande_conge;


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

INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES
(1, 17, 1, NOW()),  -- Date de modification actuelle
(2, 18, 1, '2025-11-23 00:00:00'),  -- Date de modification spécifique
(3, 2, 1, '2025-11-25 00:00:00'),   -- Date de modification spécifique
(4, 2, 1, '2025-11-26 00:00:00'),   -- Zo Lalaina = Développeur Backend
(5, 3, 1, '2025-11-26 00:00:00');   -- Date de modification actuelle

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

-- donne presence statut heure d'arrivée
INSERT INTO statut_pointage (heure, remarque, tolerance, jour) VALUES
('08:00:00', 'Heure normale', 10, 1),  -- Lundi
('08:00:00', 'Heure normale', 10, 2),  -- Mardi
('08:00:00', 'Heure normale', 10, 3),  -- Mercredi
('08:00:00', 'Heure normale', 10, 4),  -- Jeudi
('07:30:00', 'Heure normale', 10, 5),  -- Vendredi
('09:00:00', 'Heure normale', 10, 6);  -- Samedi

--donne absenece

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





--donne conge
-- Inserting types of leave
INSERT INTO type_conge (nom, description, remuneree, nb_jours_max) VALUES
('Congé payé', 'Congé avec salaire', 1, 30),  -- Paid leave
('Congé sans solde', 'Congé sans rémunération', 0, NULL),  -- Unpaid leave
('Congé maladie', 'Congé pour raisons médicales', 1, 15);  -- Sick leave

-- Inserting leave requests
INSERT INTO demande_conge (id_type_conge, id_employe, date_debut, date_fin, nb_jours) VALUES
(1, 1, '2025-01-01', '2025-01-10', 10),  -- Paid leave request
(2, 2, '2025-01-15', '2025-01-20', 5),  -- Unpaid leave request
(3, 3, '2025-01-23', '2025-01-29', 5);  -- Sick leave request

-- Inserting validation of leave requests
/*
INSERT INTO validation_conge (id_demande_conge, statut, date_validation) VALUES
(1, 'valide', '2023-10-28'),  -- Approved leave
(2, 'refuse', '2023-10-28');  -- Approved leave

*/