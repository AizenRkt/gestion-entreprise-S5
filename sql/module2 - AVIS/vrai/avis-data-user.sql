-- ============================================
-- DONNÉES UTILISATEURS MODULE AVIS
-- Achat, Vente, Stock
-- ============================================

USE gestion_entreprise_test;

-- ============================================
-- 1. DÉPARTEMENTS
-- ============================================
INSERT INTO departement (id_dept, nom) VALUES
(1, 'Supply Chain'),
(2, 'Commercial'),
(3, 'Finance');

-- ============================================
-- 2. SERVICES
-- ============================================
INSERT INTO service (id_service, nom, id_dept) VALUES
-- Supply Chain
(1, 'Stock', 1),
(2, 'Achats', 1),
-- Commercial
(3, 'Ventes', 2),
-- Finance
(4, 'Comptabilité Fournisseur', 3),
(5, 'Comptabilité Client', 3);

-- ============================================
-- 3. POSTES
-- ============================================
INSERT INTO poste (id_poste, titre, id_service) VALUES
-- Stock (service 1)
(1, 'Agent Inventaire', 1),
(2, 'Gestionnaire Stock', 1),
(3, 'Responsable Stock', 1),
-- Achats (service 2)
(4, 'Demandeur', 2),
(5, 'Acheteur', 2),
(6, 'Responsable Achats', 2),
-- Ventes (service 3)
(7, 'Commercial', 3),
(8, 'Agent Livraison', 3),
(9, 'Responsable Ventes', 3),
-- Comptabilité Fournisseur (service 4)
(10, 'Comptable Fournisseur', 4),
-- Comptabilité Client (service 5)
(11, 'Comptable Client', 5);

-- ============================================
-- 4. RÔLES
-- ============================================
INSERT INTO role (id_role, nom) VALUES
-- Stock
(1, 'ROLE_AGENT_INVENTAIRE'),
(2, 'ROLE_GESTIONNAIRE_STOCK'),
(3, 'ROLE_RESPONSABLE_STOCK'),
-- Achats
(4, 'ROLE_DEMANDEUR'),
(5, 'ROLE_ACHETEUR'),
(6, 'ROLE_RESPONSABLE_ACHATS'),
-- Ventes
(7, 'ROLE_COMMERCIAL'),
(8, 'ROLE_AGENT_LIVRAISON'),
(9, 'ROLE_RESPONSABLE_VENTES'),
-- Comptabilité
(10, 'ROLE_COMPTABLE_FOURNISSEUR'),
(11, 'ROLE_COMPTABLE_CLIENT');

-- ============================================
-- 5. POSTE_ROLE (liaison poste -> rôle)
-- ============================================
INSERT INTO poste_role (id_poste, id_role, date_role) VALUES
-- Stock
(1, 1, CURDATE()),  -- Agent Inventaire -> ROLE_AGENT_INVENTAIRE
(2, 2, CURDATE()),  -- Gestionnaire Stock -> ROLE_GESTIONNAIRE_STOCK
(3, 3, CURDATE()),  -- Responsable Stock -> ROLE_RESPONSABLE_STOCK
-- Achats
(4, 4, CURDATE()),  -- Demandeur -> ROLE_DEMANDEUR
(5, 5, CURDATE()),  -- Acheteur -> ROLE_ACHETEUR
(6, 6, CURDATE()),  -- Responsable Achats -> ROLE_RESPONSABLE_ACHATS
-- Ventes
(7, 7, CURDATE()),   -- Commercial -> ROLE_COMMERCIAL
(8, 8, CURDATE()),   -- Agent Livraison -> ROLE_AGENT_LIVRAISON
(9, 9, CURDATE()),   -- Responsable Ventes -> ROLE_RESPONSABLE_VENTES
-- Comptabilité
(10, 10, CURDATE()), -- Comptable Fournisseur -> ROLE_COMPTABLE_FOURNISSEUR
(11, 11, CURDATE()); -- Comptable Client -> ROLE_COMPTABLE_CLIENT

-- ============================================
-- 6. EMPLOYÉS
-- ============================================
INSERT INTO employe (id_employe, nom, prenom, email, telephone, genre, date_embauche) VALUES
-- Stock
(1, 'RAKOTO', 'Jean', 'jean.rakoto@entreprise.mg', '034 00 001 01', 'M', '2024-01-15'),
(2, 'RABE', 'Marie', 'marie.rabe@entreprise.mg', '034 00 001 02', 'F', '2023-06-01'),
(3, 'RANDRIA', 'Paul', 'paul.randria@entreprise.mg', '034 00 001 03', 'M', '2022-03-10'),
-- Achats
(4, 'RASOA', 'Noro', 'noro.rasoa@entreprise.mg', '034 00 002 01', 'F', '2024-02-20'),
(5, 'RAHARISON', 'Fidy', 'fidy.raharison@entreprise.mg', '034 00 002 02', 'M', '2023-08-15'),
(6, 'RAZAFY', 'Hery', 'hery.razafy@entreprise.mg', '034 00 002 03', 'M', '2021-11-01'),
-- Ventes
(7, 'ANDRIA', 'Tiana', 'tiana.andria@entreprise.mg', '034 00 003 01', 'F', '2024-03-05'),
(8, 'RAVELOSON', 'Mamy', 'mamy.raveloson@entreprise.mg', '034 00 003 02', 'M', '2023-09-20'),
(9, 'RABEARISON', 'Lanto', 'lanto.rabearison@entreprise.mg', '034 00 003 03', 'M', '2022-01-15'),
-- Comptabilité
(10, 'RASOANAIVO', 'Voahangy', 'voahangy.rasoanaivo@entreprise.mg', '034 00 004 01', 'F', '2023-04-01'),
(11, 'RAMANANA', 'Solo', 'solo.ramanana@entreprise.mg', '034 00 004 02', 'M', '2022-07-10');

-- ============================================
-- 7. EMPLOYE_STATUT (liaison employé -> poste actif)
-- ============================================
INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES
-- Stock
(1, 1, 1, NOW()),  -- Jean RAKOTO -> Agent Inventaire (actif)
(2, 2, 1, NOW()),  -- Marie RABE -> Gestionnaire Stock (actif)
(3, 3, 1, NOW()),  -- Paul RANDRIA -> Responsable Stock (actif)
-- Achats
(4, 4, 1, NOW()),  -- Noro RASOA -> Demandeur (actif)
(5, 5, 1, NOW()),  -- Fidy RAHARISON -> Acheteur (actif)
(6, 6, 1, NOW()),  -- Hery RAZAFY -> Responsable Achats (actif)
-- Ventes
(7, 7, 1, NOW()),  -- Tiana ANDRIA -> Commercial (actif)
(8, 8, 1, NOW()),  -- Mamy RAVELOSON -> Agent Livraison (actif)
(9, 9, 1, NOW()),  -- Lanto RABEARISON -> Responsable Ventes (actif)
-- Comptabilité
(10, 10, 1, NOW()), -- Voahangy RASOANAIVO -> Comptable Fournisseur (actif)
(11, 11, 1, NOW()); -- Solo RAMANANA -> Comptable Client (actif)

-- ============================================
-- 8. USERS (comptes utilisateurs)
-- Mot de passe par défaut: password123 (à hasher en production)
-- ============================================
INSERT INTO user (id_user, username, pwd, id_employe) VALUES
-- Stock
(1, 'agent.inventaire', '123', 1),
(2, 'gestionnaire.stock', '123', 2),
(3, 'responsable.stock', '123', 3),
-- Achats
(4, 'demandeur', '123', 4),
(5, 'acheteur', '123', 5),
(6, 'responsable.achats', '123', 6),
-- Ventes
(7, 'commercial', '123', 7),
(8, 'agent.livraison', '123', 8),
(9, 'responsable.ventes', '123', 9),
-- Comptabilité
(10, 'comptable.fournisseur', '123', 10),
(11, 'comptable.client', '123', 11);

-- ============================================
-- 9. MENU_UI (menus par service et rôle)
-- Chemin relatif vers les fichiers de menu PHP
-- ============================================
INSERT INTO menu_ui (id_menu, nom, id_service, role) VALUES
-- Stock (service 1)
(1, 'avis/stock/menuAgentInventaire', 1, 'ROLE_AGENT_INVENTAIRE'),
(2, 'avis/stock/menuGestionStock', 1, 'ROLE_GESTIONNAIRE_STOCK'),
(3, 'avis/stock/menuResponsableStock', 1, 'ROLE_RESPONSABLE_STOCK'),
-- Achats (service 2)
(4, 'avis/achat/menuDemandeur', 2, 'ROLE_DEMANDEUR'),
(5, 'avis/achat/menuAcheteur', 2, 'ROLE_ACHETEUR'),
(6, 'avis/achat/menuResponsableAchats', 2, 'ROLE_RESPONSABLE_ACHATS'),
-- Ventes (service 3)
(7, 'avis/vente/menuCommercial', 3, 'ROLE_COMMERCIAL'),
(8, 'avis/vente/menuAgentLivraison', 3, 'ROLE_AGENT_LIVRAISON'),
(9, 'avis/vente/menuResponsableVentes', 3, 'ROLE_RESPONSABLE_VENTES'),
-- Comptabilité Fournisseur (service 4)
(10, 'avis/achat/menuComptableFournisseur', 4, 'ROLE_COMPTABLE_FOURNISSEUR'),
-- Comptabilité Client (service 5)
(11, 'avis/vente/menuComptableClient', 5, 'ROLE_COMPTABLE_CLIENT');

-- ============================================
-- 10. ROUTE_PERMISSIONS (permissions par route)
-- ============================================
INSERT INTO route_permissions (route_pattern, role_name, id_service) VALUES
-- =====================
-- STOCK ROUTES
-- =====================
-- Agent Inventaire
('/stock/inventaire/comptage', 'ROLE_AGENT_INVENTAIRE', 1),
('/stock/inventaire/fiche', 'ROLE_AGENT_INVENTAIRE', 1),
('/referentiel/article/list', 'ROLE_AGENT_INVENTAIRE', 1),

-- Gestionnaire Stock
('/kpi/stock', 'ROLE_GESTIONNAIRE_STOCK', 1),
('/stock/mouvement/saisie', 'ROLE_GESTIONNAIRE_STOCK', 1),
('/stock/mouvement/list', 'ROLE_GESTIONNAIRE_STOCK', 1),
('/stock/reservations', 'ROLE_GESTIONNAIRE_STOCK', 1),
('/stock/inventaire/comptage', 'ROLE_GESTIONNAIRE_STOCK', 1),
('/stock/inventaire/fiche', 'ROLE_GESTIONNAIRE_STOCK', 1),
('/referentiel/article-famille/list', 'ROLE_GESTIONNAIRE_STOCK', 1),
('/referentiel/article/list', 'ROLE_GESTIONNAIRE_STOCK', 1),
('/referentiel/fournisseur/list', 'ROLE_GESTIONNAIRE_STOCK', 1),

-- Responsable Stock (accès complet)
('/kpi/direction', 'ROLE_RESPONSABLE_STOCK', 1),
('/kpi/stock', 'ROLE_RESPONSABLE_STOCK', 1),
('/stock/mouvement/*', 'ROLE_RESPONSABLE_STOCK', 1),
('/stock/reservations', 'ROLE_RESPONSABLE_STOCK', 1),
('/stock/inventaire/*', 'ROLE_RESPONSABLE_STOCK', 1),
('/stock/admin/*', 'ROLE_RESPONSABLE_STOCK', 1),
('/referentiel/article-famille/*', 'ROLE_RESPONSABLE_STOCK', 1),
('/referentiel/article/*', 'ROLE_RESPONSABLE_STOCK', 1),
('/referentiel/fournisseur/*', 'ROLE_RESPONSABLE_STOCK', 1),

-- =====================
-- ACHAT ROUTES
-- =====================
-- Demandeur
('/avis/achat/saisie', 'ROLE_DEMANDEUR', 2),
('/avis/achat/demandes', 'ROLE_DEMANDEUR', 2),

-- Acheteur
('/avis/achat/demandes', 'ROLE_ACHETEUR', 2),
('/avis/achat/saisie', 'ROLE_ACHETEUR', 2),
('/avis/achat/bc', 'ROLE_ACHETEUR', 2),
('/avis/achat/bc/*', 'ROLE_ACHETEUR', 2),
('/avis/achat/receptions', 'ROLE_ACHETEUR', 2),
('/avis/achat/receptions/*', 'ROLE_ACHETEUR', 2),

-- Responsable Achats (accès complet)
('/kpi/direction', 'ROLE_RESPONSABLE_ACHATS', 2),
('/kpi/achats', 'ROLE_RESPONSABLE_ACHATS', 2),
('/avis/achat/*', 'ROLE_RESPONSABLE_ACHATS', 2),

-- Comptable Fournisseur
('/avis/achat/receptions', 'ROLE_COMPTABLE_FOURNISSEUR', 4),
('/avis/achat/factures', 'ROLE_COMPTABLE_FOURNISSEUR', 4),
('/avis/achat/factures/*', 'ROLE_COMPTABLE_FOURNISSEUR', 4),
('/avis/achat/paiements', 'ROLE_COMPTABLE_FOURNISSEUR', 4),
('/avis/achat/paiements/*', 'ROLE_COMPTABLE_FOURNISSEUR', 4),

-- =====================
-- VENTE ROUTES
-- =====================
-- Commercial
('/ventes/clients', 'ROLE_COMMERCIAL', 3),
('/ventes/commandes', 'ROLE_COMMERCIAL', 3),

-- Agent Livraison
('/ventes/commandes', 'ROLE_AGENT_LIVRAISON', 3),
('/ventes/livraisons', 'ROLE_AGENT_LIVRAISON', 3),

-- Responsable Ventes (accès complet)
('/kpi/direction', 'ROLE_RESPONSABLE_VENTES', 3),
('/kpi/ventes', 'ROLE_RESPONSABLE_VENTES', 3),
('/ventes/*', 'ROLE_RESPONSABLE_VENTES', 3),

-- Comptable Client
('/ventes/livraisons', 'ROLE_COMPTABLE_CLIENT', 5),
('/ventes/factures', 'ROLE_COMPTABLE_CLIENT', 5),
('/ventes/encaissements', 'ROLE_COMPTABLE_CLIENT', 5);
