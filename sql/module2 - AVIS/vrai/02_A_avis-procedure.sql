DELIMITER //

CREATE PROCEDURE add_columns_commande_client()
BEGIN
    -- Ajouter colonne statut si elle n'existe pas
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'commande_client' 
        AND COLUMN_NAME = 'statut'
    ) THEN
        ALTER TABLE commande_client ADD COLUMN statut ENUM('BROUILLON','VALIDE','CLOTURE') DEFAULT 'BROUILLON';
    END IF;

    -- Ajouter colonne valide_par si elle n'existe pas
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'commande_client' 
        AND COLUMN_NAME = 'valide_par'
    ) THEN
        ALTER TABLE commande_client ADD COLUMN valide_par INT NULL;
    END IF;

    -- Ajouter colonne date_validation si elle n'existe pas
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'commande_client' 
        AND COLUMN_NAME = 'date_validation'
    ) THEN
        ALTER TABLE commande_client ADD COLUMN date_validation DATETIME NULL;
    END IF;
END//

DELIMITER ;
CALL add_columns_commande_client();