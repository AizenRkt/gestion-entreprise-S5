-- =============================================================================
-- SCRIPT MAÎTRE POUR L'INSERTION DES DONNÉES AVIS
-- Ordre d'exécution des fichiers SQL
-- =============================================================================

-- 1. Création de la base de données et des tables
SOURCE C:/xampp/htdocs/S5/gestion-entreprise-S5/sql/module2 - AVIS/vrai/01_avis.sql;

-- 2. Ajout des colonnes manquantes via procédures
SOURCE C:/xampp/htdocs/S5/gestion-entreprise-S5/sql/module2 - AVIS/vrai/02_A_avis-procedure-corrected.sql;

-- 3. Insertion des données utilisateurs (départements, services, rôles, employés, utilisateurs)
SOURCE C:/xampp/htdocs/S5/gestion-entreprise-S5/sql/module2 - AVIS/vrai/03_avis-data-user.sql;

-- 4. Création des vues KPI
SOURCE C:/xampp/htdocs/S5/gestion-entreprise-S5/sql/module2 - AVIS/vrai/02_avis-vue.sql;

-- 5. Insertion des données d'achat
SOURCE C:/xampp/htdocs/S5/gestion-entreprise-S5/sql/module2 - AVIS/vrai/04_avis-data-achat.sql;

-- 6. Insertion des données de vente
SOURCE C:/xampp/htdocs/S5/gestion-entreprise-S5/sql/module2 - AVIS/vrai/05_avis-data-vente.sql;

-- 7. Insertion des données de stock
SOURCE C:/xampp/htdocs/S5/gestion-entreprise-S5/sql/module2 - AVIS/vrai/06_avis-data-stock.sql;

-- 8. Insertion des données générales restantes
SOURCE C:/xampp/htdocs/S5/gestion-entreprise-S5/sql/module2 - AVIS/vrai/07_avis-data.sql;

-- =============================================================================
-- FIN DU SCRIPT
-- =============================================================================