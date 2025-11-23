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
