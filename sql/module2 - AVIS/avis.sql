-- client
CREATE TABLE client_type (
    id_client_type INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

CREATE TABLE client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(30),
    email VARCHAR(100),
    adresse TEXT,
    id_type INT,
    FOREIGN KEY (id_type) REFERENCES client_type(id_client_type)
);

-- methodes de valorisation
CREATE TABLE methode_valorisation (
    id_methode_valorisation INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    libelle VARCHAR(50) NOT NULL
);

INSERT INTO methode_valorisation (code, libelle) VALUES
('FIFO', 'First In First Out'),
('LIFO', 'Last In First Out'),
('CUMP', 'Coût Unitaire Moyen Pondéré');

-- article
CREATE TABLE article_famille (
    id_article_famille INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE article_famille_status (
    id_article_famille_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('actif', 'inactif')
);

CREATE TABLE article (
    id_article INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    designation VARCHAR(150) NOT NULL,
    id_famille_article_famille INT,
    id_methode_valorisation INT,
    unite VARCHAR(20) NOT NULL,
    prix_achat NUMERIC(12,2),
    prix_vente NUMERIC(12,2),
    stock_min NUMERIC(12,2) DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE
    ,
    FOREIGN KEY (id_famille_article_famille) REFERENCES article_famille(id_article_famille),
    FOREIGN KEY (id_methode_valorisation) REFERENCES methode_valorisation(id_methode_valorisation)
);

CREATE TABLE article_status (
    id_article_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('disponible', 'rupture de stock', 'en commande')
);

CREATE TABLE article_prix_historique (
    id_article_prix_historique INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT REFERENCES article(id_article),
    prix_vente NUMERIC(12,2),
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- fournisseur et client
CREATE TABLE fournisseur (
    id_fournisseur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(100)
);

CREATE TABLE fournisseur_article (
    id_fournisseur_article INT AUTO_INCREMENT PRIMARY KEY,
    id_fournisseur INT REFERENCES fournisseur(id_fournisseur),
    id_article INT REFERENCES article(id_article),
    prix_achat NUMERIC(12,2),
    delai_livraison INT
);



-- entrepot, site 
CREATE TABLE depot (
    id_depot INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    adresse TEXT
);

CREATE TABLE site (
    id_site INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL,
    nom VARCHAR(100)
);

CREATE TABLE site_depot(
    id_site_depot INT AUTO_INCREMENT PRIMARY KEY,
    id_depot INT REFERENCES depot(id_depot),
    id_site INT REFERENCES site(id_site)
);

-- documents
CREATE TABLE document_type (
    id_document_type INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

CREATE TABLE document (
    id_document INT AUTO_INCREMENT PRIMARY KEY,
    id_document_type INT REFERENCES document_type(id_document_type),
    reference VARCHAR(100) UNIQUE NOT NULL,
    description VARCHAR(255),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    path VARCHAR(255) NOT NULL
);

CREATE TABLE document_status (
    id_document_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('valide', 'annule')
);

-- =========================================================
-- achats / ventes
-- =========================================================

-- =========================
-- BON DE COMMANDE, RECEPTION, FACTURE, PAIEMENT FOURNISSEUR
-- =========================
CREATE TABLE bon_commande_fournisseur (
    id_bon_commande_fournisseur INT AUTO_INCREMENT PRIMARY KEY,
    bc_numero VARCHAR(50) NOT NULL UNIQUE,
    bc_date DATETIME NOT NULL,
    id_fournisseur INT NOT NULL,
    id_depot INT NOT NULL,
    id_demande_achat INT,
    montant_ht DECIMAL(15,2) DEFAULT 0,
    montant_tva DECIMAL(15,2) DEFAULT 0,
    montant_ttc DECIMAL(15,2) DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_demande_achat) REFERENCES demande_achat(id_demande_achat)
);
CREATE TABLE bon_commande_fournisseur_status (
    id_bon_commande_fournisseur_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('validé', 'rejeté'),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reception_fournisseur (
    id_reception_fournisseur INT AUTO_INCREMENT PRIMARY KEY,
    reception_numero VARCHAR(50) NOT NULL UNIQUE,
    reception_date DATETIME NOT NULL,
    id_fournisseur INT NOT NULL,
    id_bon_commande_fournisseur INT NOT NULL,
    id_depot INT NOT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_bon_commande_fournisseur) REFERENCES bon_commande_fournisseur(id_bon_commande_fournisseur)
);
CREATE TABLE reception_fournisseur_status (
    id_reception_fournisseur_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('validé', 'rejeté'),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE facture_fournisseur (
    id_facture_fournisseur INT AUTO_INCREMENT PRIMARY KEY,
    facture_numero VARCHAR(50) NOT NULL UNIQUE,
    facture_date DATETIME NOT NULL,
    id_fournisseur INT NOT NULL,
    id_reception_fournisseur INT NOT NULL,
    montant_ht DECIMAL(15,2) DEFAULT 0,
    montant_tva DECIMAL(15,2) DEFAULT 0,
    montant_ttc DECIMAL(15,2) DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_reception_fournisseur) REFERENCES reception_fournisseur(id_reception_fournisseur)
);
CREATE TABLE facture_fournisseur_status (
    id_facture_fournisseur_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('validé', 'rejeté'),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE mode_paiement (
    id_mode_paiement INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    libelle VARCHAR(50) NOT NULL
);
CREATE TABLE paiement_fournisseur (
    id_paiement_fournisseur INT AUTO_INCREMENT PRIMARY KEY,
    paiement_numero VARCHAR(50) NOT NULL UNIQUE,
    paiement_date DATETIME NOT NULL,
    id_facture_fournisseur INT NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL,
    reference_paiement VARCHAR(100),
    cree_par INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_facture_fournisseur) REFERENCES facture_fournisseur(id_facture_fournisseur)
);
CREATE TABLE paiement_fournisseur_detail (
    id_paiement_fournisseur_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_paiement_fournisseur INT NOT NULL,
    id_mode_paiement INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (id_paiement_fournisseur) REFERENCES paiement_fournisseur(id_paiement_fournisseur)
);
CREATE TABLE paiement_fournisseur_status (
    id_paiement_fournisseur_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('payé'),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- COMMANDE, LIVRAISON, FACTURE, ENCAISSEMENT  CLIENT
-- =========================
CREATE TABLE commande_client (
    id_commande_client INT AUTO_INCREMENT PRIMARY KEY,
    commande_numero VARCHAR(50) NOT NULL UNIQUE,
    commande_date DATETIME NOT NULL,
    id_client INT NOT NULL,
    id_depot INT NOT NULL,
    montant_ht DECIMAL(15,2) DEFAULT 0,
    montant_tva DECIMAL(15,2) DEFAULT 0,
    montant_ttc DECIMAL(15,2) DEFAULT 0,
    created_byt INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE livraison_client (
    id_livraison_client INT AUTO_INCREMENT PRIMARY KEY,
    livraison_numero VARCHAR(50) NOT NULL UNIQUE,
    livraison_date DATETIME NOT NULL,
    id_commande_client INT NOT NULL,
    id_depot INT NOT NULL,
    statut ENUM('BROUILLON','LIVREE') DEFAULT 'BROUILLON',
    cree_par INT NOT NULL,
    valide_par INT,
    date_validation DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_commande_client) REFERENCES commande_client(id_commande_client)
);


CREATE TABLE facture_client (
    id_facture_client INT AUTO_INCREMENT PRIMARY KEY,
    numero_facture VARCHAR(50) NOT NULL UNIQUE,
    date_facture DATETIME NOT NULL,
    client_id INT NOT NULL,
    livraison_client_id INT NOT NULL,
    montant_ht DECIMAL(15,2) DEFAULT 0,
    montant_tva DECIMAL(15,2) DEFAULT 0,
    montant_ttc DECIMAL(15,2) DEFAULT 0,
    statut ENUM('BROUILLON','VALIDE','PAYE') DEFAULT 'BROUILLON',
    cree_par INT NOT NULL,
    valide_par INT,
    date_validation DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (livraison_client_id) REFERENCES livraison_client(id_livraison_client)
);

CREATE TABLE encaissement_client (
    id_encaissement_client INT AUTO_INCREMENT PRIMARY KEY,
    numero_encaissement VARCHAR(50) NOT NULL UNIQUE,
    date_encaissement DATETIME NOT NULL,
    facture_client_id INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    id_mode_paiement INT NOT NULL,
    reference_paiement VARCHAR(100),
    cree_par INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facture_client_id) REFERENCES facture_client(id_facture_client),
    FOREIGN KEY (id_mode_paiement) REFERENCES mode_paiement(id_mode_paiement)
);


-- ==============================
-- LOT (clé FIFO / LIFO / FEFO)
-- ==============================
CREATE TABLE lot (
    id_lot INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT NOT NULL,
    id_depot INT NOT NULL,

    lot_numero VARCHAR(50) NOT NULL,
    date_entree DATETIME NOT NULL,

    quantite_initiale DECIMAL(15,3) NOT NULL,
    cout_unitaire DECIMAL(15,4) NOT NULL,

    date_limite_utilisation_optimale DATE,
    date_limite_consommation DATE,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_article) REFERENCES article(id_article)
);

-- ==============================
-- MOUVEMENT DE STOCK 
-- ==============================
CREATE TABLE mouvement_stock_categorie (
    id_categorie_mouvement_stock INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    libelle VARCHAR(50) NOT NULL
);

INSERT INTO mouvement_stock_categorie (code, libelle) VALUES
('in', 'entree'),
('out', 'sortie');

CREATE TABLE mouvement_stock_type (
    id_type_mouvement_stock INT AUTO_INCREMENT PRIMARY KEY,
    id_categorie_mouvement_stock INT NOT NULL,

    code VARCHAR(30) NOT NULL UNIQUE,
    libelle VARCHAR(100) NOT NULL,

    impact_valorisation BOOLEAN DEFAULT TRUE, -- ex: inventaire = true, réservation = false
    necessite_validation BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (id_categorie_mouvement_stock)
    REFERENCES mouvement_stock_categorie(id_categorie_mouvement_stock)
);

CREATE TABLE mouvement_stock (
    id_mouvement_stock BIGINT AUTO_INCREMENT PRIMARY KEY,

    id_article INT NOT NULL,
    id_depot INT NOT NULL,
    id_lot INT,

    id_type_mouvement_stock INT NOT NULL,

    sens SMALLINT NOT NULL, -- + entrée (1) / - sortie (0)

    quantite DECIMAL(15,3) NOT NULL,
    cout_unitaire DECIMAL(15,4),

    motif VARCHAR(255),

    date_mouvement DATETIME NOT NULL,

    date_validation DATETIME,

    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_article) REFERENCES article(id_article),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot),
    FOREIGN KEY (id_lot) REFERENCES lot(id_lot),
    FOREIGN KEY (id_type_mouvement_stock) REFERENCES mouvement_stock_type(id_type_mouvement_stock)
);

CREATE TABLE mouvement_stock_status (
    id_mouvement_stock_status INT AUTO_INCREMENT PRIMARY KEY,
    id_mouvement_stock INT NOT NULL,
    libelle ENUM('validé', 'annulé'),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mouvement_stock) REFERENCES mouvement_stock(id_mouvement_stock)
);


CREATE TABLE stock_courant (
    id_article INT NOT NULL,
    id_depot INT NOT NULL,

    quantite DECIMAL(15,3) NOT NULL DEFAULT 0,
    valeur_stock DECIMAL(15,2) NOT NULL DEFAULT 0,
    cout_moyen DECIMAL(15,4),

    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id_article, id_depot),
    FOREIGN KEY (id_article) REFERENCES article(id_article)
);

-- ==============================
-- DEMANDE D'ACHAT
-- ==============================
CREATE TABLE demande_achat (
    id_demande_achat INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(30) NOT NULL UNIQUE,
    date_demande DATE NOT NULL,
    id_fournisseur INT NOT NULL,
    remarque TEXT,
    statut ENUM('CREE','VISEE','REJETEE') DEFAULT 'CREE',
    montant_ht DECIMAL(15,2) DEFAULT 0,
    montant_tva DECIMAL(15,2) DEFAULT 0,
    montant_ttc DECIMAL(15,2) DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_fournisseur) REFERENCES fournisseur(id_fournisseur)
);

CREATE TABLE demande_achat_ligne (
    id_demande_achat_ligne INT AUTO_INCREMENT PRIMARY KEY,
    id_demande_achat INT NOT NULL,
    id_article INT,
    code_article VARCHAR(50),
    designation VARCHAR(200) NOT NULL,
    quantite DECIMAL(15,3) NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    tva DECIMAL(6,3) DEFAULT 0,
    quantite_stock DECIMAL(15,3) DEFAULT 0,
    FOREIGN KEY (id_demande_achat) REFERENCES demande_achat(id_demande_achat) ON DELETE CASCADE,
    FOREIGN KEY (id_article) REFERENCES article(id_article)
);

CREATE TABLE demande_achat_statut (
    id_demande_achat_statut INT AUTO_INCREMENT PRIMARY KEY,
    id_demande_achat INT NOT NULL,
    statut ENUM('CREE','VISEE','REJETEE') NOT NULL,
    commentaire VARCHAR(255),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_demande_achat) REFERENCES demande_achat(id_demande_achat) ON DELETE CASCADE
);
