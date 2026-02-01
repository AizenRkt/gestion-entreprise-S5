DROP DATABASE gestion_entreprise_test;
CREATE DATABASE gestion_entreprise_test;
USE gestion_entreprise_test;

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
    id_client_type INT NOT NULL,
    FOREIGN KEY (id_client_type) REFERENCES client_type(id_client_type)
);

-- methodes de valorisation
CREATE TABLE methode_valorisation (
    id_methode_valorisation INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10),
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
    description TEXT,
    necessite_lot BOOLEAN DEFAULT FALSE
);

CREATE TABLE article_famille_status (
    id_article_famille_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('actif', 'inactif'),
    date_status DATETIME NOT NULL,
    id_article_famille INT NOT NULL,
    FOREIGN KEY (id_article_famille) REFERENCES article_famille(id_article_famille)
);

CREATE TABLE article (
    id_article INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    designation VARCHAR(150) NOT NULL,
    id_famille_article_famille INT REFERENCES article_famille(id_article_famille),
    id_methode_valorisation INT REFERENCES methode_valorisation(id_methode_valorisation),
    allocation_defaut ENUM('fifo','lifo','fefo'),
    unite VARCHAR(20) NOT NULL,
    prix_achat NUMERIC(12,2),
    prix_vente NUMERIC(12,2),
    stock_min NUMERIC(12,2) DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_famille_article_famille) REFERENCES article_famille(id_article_famille),
    FOREIGN KEY (id_methode_valorisation) REFERENCES methode_valorisation(id_methode_valorisation)
);

CREATE TABLE article_status (
    id_article_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('disponible', 'rupture de stock', 'en commande'),
    date_status DATETIME NOT NULL,
    id_article INT NOT NULL,
    FOREIGN KEY (id_article) REFERENCES article(id_article)
);

CREATE TABLE article_prix_historique (
    id_article_prix_historique INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT REFERENCES article(id_article),
    prix_vente NUMERIC(12,2),
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_article) REFERENCES article(id_article)
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
    delai_livraison INT,
    FOREIGN KEY (id_fournisseur) REFERENCES fournisseur(id_fournisseur),
    FOREIGN KEY (id_article) REFERENCES article(id_article)
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
    id_site INT REFERENCES site(id_site),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot),
    FOREIGN KEY (id_site) REFERENCES site(id_site)
);

-- documents
CREATE TABLE document_type_avis (
    id_document_type INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

CREATE TABLE document_avis (
    id_document INT AUTO_INCREMENT PRIMARY KEY,
    id_document_type INT NOT NULL,
    reference VARCHAR(100) UNIQUE NOT NULL,
    description VARCHAR(255),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    path VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_document_type) REFERENCES document_type_avis(id_document_type)
);

CREATE TABLE document_status_avis (
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
    montant_ht DECIMAL(15,2) DEFAULT 0,
    montant_tva DECIMAL(15,2) DEFAULT 0,
    montant_ttc DECIMAL(15,2) DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_fournisseur) REFERENCES fournisseur(id_fournisseur),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot)
);
CREATE TABLE bon_commande_fournisseur_status (
    id_bon_commande_fournisseur_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('validé', 'rejeté'),
    id_bon_commande_fournisseur INT NOT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_bon_commande_fournisseur) REFERENCES bon_commande_fournisseur(id_bon_commande_fournisseur)
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
    FOREIGN KEY (id_bon_commande_fournisseur) REFERENCES bon_commande_fournisseur(id_bon_commande_fournisseur),
    FOREIGN KEY (id_fournisseur) REFERENCES fournisseur(id_fournisseur),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot)
);
CREATE TABLE reception_fournisseur_status (
    id_reception_fournisseur_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('validé', 'rejeté'),
    id_reception_fournisseur INT NOT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_reception_fournisseur) REFERENCES reception_fournisseur(id_reception_fournisseur)
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
    FOREIGN KEY (id_reception_fournisseur) REFERENCES reception_fournisseur(id_reception_fournisseur),
    FOREIGN KEY (id_fournisseur) REFERENCES fournisseur(id_fournisseur)
);
CREATE TABLE facture_fournisseur_status (
    id_facture_fournisseur_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('validé', 'rejeté'),
    id_facture_fournisseur INT NOT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_facture_fournisseur) REFERENCES facture_fournisseur(id_facture_fournisseur)
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
    FOREIGN KEY (id_paiement_fournisseur) REFERENCES paiement_fournisseur(id_paiement_fournisseur),
    FOREIGN KEY (id_mode_paiement) REFERENCES mode_paiement(id_mode_paiement)
);
CREATE TABLE paiement_fournisseur_status (
    id_paiement_fournisseur_status INT AUTO_INCREMENT PRIMARY KEY,
    libelle ENUM('payé'),
    id_paiement_fournisseur INT NOT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_paiement_fournisseur) REFERENCES paiement_fournisseur(id_paiement_fournisseur)
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
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_client) REFERENCES client(id_client),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot)
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
    FOREIGN KEY (id_commande_client) REFERENCES commande_client(id_commande_client),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot)
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
    FOREIGN KEY (livraison_client_id) REFERENCES livraison_client(id_livraison_client),
    FOREIGN KEY (client_id) REFERENCES client(id_client)
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
    FOREIGN KEY (id_article) REFERENCES article(id_article),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot),
    INDEX idx_lot_date_entree (date_entree)
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
    necessite_validation BOOLEAN DEFAULT TRUE,

    FOREIGN KEY (id_categorie_mouvement_stock) REFERENCES mouvement_stock_categorie(id_categorie_mouvement_stock)
);
INSERT INTO mouvement_stock_type (id_categorie_mouvement_stock, code, libelle, impact_valorisation, necessite_validation) VALUES
(1, 'ACHAT_RECEPTION', 'Réception achat fournisseur', TRUE, TRUE),
(1, 'RETOUR_CLIENT', 'Retour client (réintégration stock)', TRUE, TRUE),
(1, 'TRANSFERT_ENTREE', 'Entrée stock par transfert inter-dépôt', TRUE, TRUE),
(1, 'INVENTAIRE_PLUS', 'Ajustement inventaire (écart positif)', TRUE, TRUE),
(1, 'PRODUCTION_ENTREE', 'Entrée production / fabrication', TRUE, TRUE),
(1, 'ANNULATION_SORTIE', 'Annulation sortie stock', TRUE, TRUE);
INSERT INTO mouvement_stock_type (id_categorie_mouvement_stock, code, libelle, impact_valorisation, necessite_validation) VALUES
(2, 'VENTE_LIVRAISON', 'Sortie stock - livraison client', TRUE, TRUE),
(2, 'TRANSFERT_SORTIE', 'Sortie stock vers autre dépôt', TRUE, TRUE),
(2, 'INVENTAIRE_MOINS', 'Ajustement inventaire (écart négatif)', TRUE, TRUE),
(2, 'PERTE_CASSE', 'Perte / casse / vol', TRUE, TRUE),
(2, 'PEREMPTION', 'Destruction produit périmé', TRUE, TRUE),
(2, 'CONSOMMATION_INTERNE', 'Consommation interne', TRUE, TRUE);
INSERT INTO mouvement_stock_type(id_categorie_mouvement_stock, code, libelle, impact_valorisation, necessite_validation)VALUES
(2, 'RESERVATION', 'Réservation stock (non valorisée)', FALSE, FALSE),
(1, 'ANNULATION_RESERVATION', 'Annulation réservation stock', FALSE, FALSE);

CREATE TABLE mouvement_stock (
    id_mouvement_stock INT AUTO_INCREMENT PRIMARY KEY,

    id_article INT NOT NULL,
    id_depot INT NOT NULL,
    id_lot INT,

    id_type_mouvement_stock INT NOT NULL,
    id_reference INT NOT NULL,
    table_reference VARCHAR(100),

    sens SMALLINT NOT NULL, -- + entrée (1) / - sortie (0)

    quantite DECIMAL(15,3) NOT NULL,
    cout_unitaire DECIMAL(15,4),
    ecart_valorisation DECIMAL(15,2) DEFAULT 0,

    mouvement_numero VARCHAR(50) UNIQUE,

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

-- Détails de consommation par lot pour les sorties
CREATE TABLE mouvement_stock_lot_detail (
    id_mouvement_stock_lot_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_mouvement_stock INT NOT NULL,
    id_lot INT NOT NULL,
    quantite DECIMAL(15,3) NOT NULL,
    cout_unitaire DECIMAL(15,4) NOT NULL,
    valeur DECIMAL(15,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mouvement_stock) REFERENCES mouvement_stock(id_mouvement_stock),
    FOREIGN KEY (id_lot) REFERENCES lot(id_lot),
    INDEX idx_msl_detail_mouv (id_mouvement_stock),
    INDEX idx_msl_detail_lot (id_lot)
);

CREATE TABLE stock_courant (
    id_stock_courant INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT NOT NULL,
    id_depot INT NOT NULL,

    quantite DECIMAL(15,3) NOT NULL DEFAULT 0,
    valeur_stock DECIMAL(15,2) NOT NULL DEFAULT 0,
    cout_moyen DECIMAL(15,4),

    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (id_article) REFERENCES article(id_article),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot)
);

CREATE TABLE stock_reservation (
    id_stock_reservation INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT NOT NULL,
    id_depot INT NOT NULL,
    quantite DECIMAL(15,3) NOT NULL,
    reference VARCHAR(100),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_article) REFERENCES article(id_article),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot)
);

-- ==============================
-- CLOTURE MENSUELLE DU STOCK / GEL DU CUMP
-- ==============================
CREATE TABLE stock_cloture_periode (
    id_stock_cloture_periode INT AUTO_INCREMENT PRIMARY KEY,
    annee INT NOT NULL,
    mois INT NOT NULL,
    statut ENUM('OUVERT','CLOTURE') DEFAULT 'OUVERT',
    date_cloture DATETIME,
    UNIQUE KEY uniq_stock_cloture_periode (annee, mois)
);

CREATE TABLE stock_cloture_detail (
    id_stock_cloture_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_stock_cloture_periode INT NOT NULL,
    id_article INT NOT NULL,
    id_depot INT NOT NULL,
    qty_ouverture DECIMAL(15,3),
    valeur_ouverture DECIMAL(15,2),
    cump_ouverture DECIMAL(15,4),
    qty_cloture DECIMAL(15,3),
    valeur_cloture DECIMAL(15,2),
    cump_cloture DECIMAL(15,4),
    FOREIGN KEY (id_stock_cloture_periode) REFERENCES stock_cloture_periode(id_stock_cloture_periode),
    FOREIGN KEY (id_article) REFERENCES article(id_article),
    FOREIGN KEY (id_depot) REFERENCES depot(id_depot),
    INDEX idx_stock_cloture_detail_periode (id_stock_cloture_periode),
    INDEX idx_stock_cloture_detail_art_dep (id_article, id_depot)
);

