CREATE TABLE contrat_essai (
    id_contrat_essai INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT NOT NULL,
    debut DATE NOT NULL,
    fin DATE NOT NULL,
    pathPdf VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat)
);


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

CREATE TABLE contrat_migration_cdd_cdi(
    id_migration INT AUTO_INCREMENT PRIMARY KEY,
    id_cdd INT NOT NULL,
    id_cdi INT NOT NULL,
    date_migration DATETIME,
    FOREIGN KEY (id_cdd) REFERENCES contrat_travail(id_contrat_travail),
    FOREIGN KEY (id_cdi) REFERENCES contrat_travail(id_contrat_travail)
);

CREATE TABLE contrat_employe_statut(
    id_contrat_employe_statut INT AUTO_INCREMENT PRIMARY KEY,
    id_contrat_travail INT NOT NULL,
    id_employe_statut INT NOT NULL,
    date_ajout DATE NOT NULL,
    FOREIGN KEY (id_employe_statut) REFERENCES employe_statut(id_employe_statut),
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
    statut ENUM('valider', 'refuser'),
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
