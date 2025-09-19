
-- ======================
-- ville
-- ======================
INSERT INTO ville (nom) VALUES
('Antananarivo'),
('Toamasina'),
('Fianarantsoa'),
('Mahajanga'),
('Toliara'),
('Antsiranana');
-- ======================
-- profil
-- ======================
INSERT INTO profil (nom) VALUES
('Développeur'),
('Designer'),
('Chef de projet'),
('Marketing'),
('Comptable'),
('Administrateur'),
('RH'),
('Technicien'),
('Manager'),
('Autre');
-- ======================
-- departement
-- ======================
INSERT INTO departement (nom) VALUES
('Informatique'),
('Ressources Humaines'),
('Finance'),
('Marketing'),
('Logistique');

-- ======================
-- service
-- ======================
INSERT INTO service (nom, id_dept) VALUES
('Développement Web', 1),
('Support IT', 1),
('Recrutement', 2),
('Comptabilité', 3),
('Communication Digitale', 4);

-- ======================
-- poste
-- ======================
INSERT INTO poste (titre, id_service) VALUES
('Développeur Backend', 1),
('Développeur Frontend', 1),
('Technicien Support', 2),
('Chargé de Recrutement', 3),
('Comptable', 4),
('Community Manager', 5);

-- ======================
-- annonce
-- ======================
INSERT INTO annonce (id_profil, titre, date_debut, date_fin, age_min, age_max, experience, objectif, qualite) VALUES
	(1, 'Développeur Web Junior', '2025-09-01', '2025-09-30', 20, 30, 1, 'Développer des sites web', 'Rigueur, autonomie'),
	(2, 'Designer Graphique', '2025-09-05', '2025-09-25', 22, 35, 2, 'Créer des visuels', 'Créativité, sens artistique'),
	(3, 'Chef de projet IT', '2025-09-10', '2025-09-30', 28, 40, 5, 'Gérer des projets', 'Leadership, organisation');

-- ======================
-- statut_annonce
-- ======================
INSERT INTO statut_annonce (id_annonce, valeur, date_fin) VALUES
	(1, 'renouvellement', '2025-09-30'),
	(2, 'retrait', '2025-09-25'),
	(3, 'renouvellement', '2025-09-30');

-- ======================
-- detail_annonce
-- ======================
-- Pour l'annonce 1 (Développeur Web Junior), on ajoute des critères ville, diplome, competence
INSERT INTO detail_annonce (id_annonce, type, id_item) VALUES
	(1, 'ville', 1), -- Antananarivo
	(1, 'ville', 2), -- Toamasina
	(1, 'diplome', 2), -- Bacc
	(1, 'diplome', 3), -- Licence
	(1, 'competence', 2), -- Programmation Java
	(1, 'competence', 4), -- Développement Web
	(2, 'ville', 3), -- Fianarantsoa
	(2, 'diplome', 2), -- Bacc
	(2, 'competence', 7), -- Communication
	(3, 'ville', 1), -- Antananarivo
	(3, 'diplome', 4), -- Doctorat
	(3, 'competence', 6); -- Gestion de projet
    
-- ======================
-- candidat
-- ======================
INSERT INTO candidat (nom, prenom, email, telephone, genre, date_naissance) VALUES
('Rakoto', 'Jean', 'jean.rakoto@example.com', '0321112233', 'M', '1990-01-01'),
('Randria', 'Marie', 'marie.randria@example.com', '0324445566', 'F', '1992-03-15'),
('Ando', 'Paul', 'paul.ando@example.com', '0337778899', 'M', '1988-07-22'),
('Rasoanaivo', 'Lalao', 'lalao.raso@example.com', '0341234567', 'F', '1995-11-30'),
('Raharinirina', 'Eric', 'eric.rahar@example.com', '0349876543', 'M', '1985-05-10');

INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(1, 'Rakoto', 'Jean', 'jean.rakoto@entreprise.com', '0321112233', 'M', '2023-01-10'),
(2, 'Randria', 'Marie', 'marie.randria@entreprise.com', '0324445566', 'F', '2023-03-15'),
(3, 'Ando', 'Paul', 'paul.ando@entreprise.com', '0337778899', 'M', '2023-05-20');

-- ======================
-- employe_statut
-- ======================
INSERT INTO employe_statut (id_employe, id_poste) VALUES
(1, 1),  -- Jean Rakoto → Développeur Backend
(2, 4),  -- Marie Randria → Chargée de Recrutement
(3, 3);  -- Paul Ando → Technicien Support

-- ======================
-- role
-- ======================
INSERT INTO role (nom) VALUES
('Administrateur'),
('Manager'),
('Employé'),
('RH'),
('Comptable');

-- ======================
-- user
-- ======================
INSERT INTO user (username, pwd, id_employe) VALUES
('jrakoto', 'password123', 1),
('mrandria', 'password123', 2),
('pando', 'password123', 3);

-- ======================
-- user_role
-- ======================
INSERT INTO user_role (id_user, id_role, date_role) VALUES
(1, 1, '2023-01-10'),  -- jrakoto → Admin
(2, 4, '2023-03-15'),  -- mrandria → RH
(3, 3, '2023-05-20');  -- pando → Employé

-- ======================
-- diplome
-- ======================
INSERT INTO diplome (nom) VALUES
('Bepc'),
('Bacc'),
('Licence'),
('Doctorat');

-- ======================
-- competence
-- ======================
INSERT INTO competence (nom) VALUES
('Informatique de base'),
('Programmation Java'),
('Programmation Python'),
('Développement Web'),
('Administration Systèmes'),
('Gestion de projet'),
('Communication'),
('Travail en équipe'),
('Anglais'),
('Français'),
('Comptabilité'),
('Marketing'),
('Analyse de données'),
('Conduite'),
('Réseaux informatiques');
