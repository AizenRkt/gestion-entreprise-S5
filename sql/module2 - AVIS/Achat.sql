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
