
-- ======================

-- ======================
-- ======================
-- Candidats, CV et detail_cv (ensemble à partir de l'id 1)
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
('Raharinirina', 'Eric', 'eric.rahar@example.com', '0349876543', 'M', '1985-05-10'),
('Rabe', 'Tiana', 'tiana.rabe@example.com', '0321111111', 'M', '1991-02-10'),
('Rasoanaivo', 'Hery', 'hery.raso@example.com', '0322222222', 'M', '1989-05-20'),
('Andrianarisoa', 'Fanja', 'fanja.andria@example.com', '0343333333', 'F', '1993-07-15'),
('Rakotomalala', 'Zo', 'zo.rakoto@example.com', '0344444444', 'M', '1990-09-25'),
('Randrianarivelo', 'Mamy', 'mamy.randria@example.com', '0335555555', 'M', '1987-12-01'),
('Ratsimbazafy', 'Niry', 'niry.ratsimba@example.com', '0336666666', 'F', '1994-03-18'),
('Raharison', 'Lova', 'lova.rahar@example.com', '0347777777', 'F', '1992-06-22'),
('Ravelo', 'Tovo', 'tovo.ravelo@example.com', '0348888888', 'M', '1988-11-30'),
('Rakotoarisoa', 'Feno', 'feno.rakoto@example.com', '0329999999', 'M', '1995-04-05'),
('Ramanandraibe', 'Sitraka', 'sitraka.raman@example.com', '0331010101', 'M', '1991-08-12'),
('Rajaonarivelo', 'Hanitra', 'hanitra.rajao@example.com', '0342020202', 'F', '1993-10-17'),
('Ratsimandresy', 'Tsiory', 'tsiory.ratsim@example.com', '0323030303', 'M', '1986-01-23'),
('Raharimalala', 'Malala', 'malala.rahari@example.com', '0334040404', 'F', '1994-05-29'),
('Randriamampionona', 'Fetra', 'fetra.randri@example.com', '0345050505', 'M', '1992-09-09'),
('Ravelomanana', 'Nomena', 'nomena.ravelo@example.com', '0326060606', 'F', '1990-12-31');
-- CV (id_candidat de 1 à 20, id_profil de 1 à 10, photo fictive)
INSERT INTO cv (id_candidat, id_profil, photo) VALUES
(1, 1, 'photo1.jpg'),
(2, 2, 'photo2.jpg'),
(3, 3, 'photo3.jpg'),
(4, 4, 'photo4.jpg'),
(5, 5, 'photo5.jpg'),
(6, 6, 'photo6.jpg'),
(7, 7, 'photo7.jpg'),
(8, 8, 'photo8.jpg'),
(9, 9, 'photo9.jpg'),
(10, 10, 'photo10.jpg'),
(11, 1, 'photo11.jpg'),
(12, 2, 'photo12.jpg'),
(13, 3, 'photo13.jpg'),
(14, 4, 'photo14.jpg'),
(15, 5, 'photo15.jpg'),
(16, 6, 'photo16.jpg'),
(17, 7, 'photo17.jpg'),
(18, 8, 'photo18.jpg'),
(19, 9, 'photo19.jpg'),
(20, 10, 'photo20.jpg');
-- detail_cv (ville, diplome, competence pour chaque CV)
INSERT INTO detail_cv (id_cv, type, id_item) VALUES
(1, 'ville', 1), (1, 'diplome', 1), (1, 'diplome', 2), (1, 'competence', 1), (1, 'competence', 2),
(2, 'ville', 2), (2, 'diplome', 2), (2, 'diplome', 3), (2, 'competence', 2), (2, 'competence', 3),
(3, 'ville', 3), (3, 'diplome', 3), (3, 'diplome', 4), (3, 'competence', 3), (3, 'competence', 4),
(4, 'ville', 4), (4, 'diplome', 4), (4, 'diplome', 1), (4, 'competence', 4), (4, 'competence', 5),
(5, 'ville', 5), (5, 'diplome', 1), (5, 'diplome', 2), (5, 'competence', 5), (5, 'competence', 6),
(6, 'ville', 6), (6, 'diplome', 2), (6, 'diplome', 3), (6, 'competence', 6), (6, 'competence', 7),
(7, 'ville', 1), (7, 'diplome', 3), (7, 'diplome', 4), (7, 'competence', 7), (7, 'competence', 8),
(8, 'ville', 2), (8, 'diplome', 4), (8, 'diplome', 1), (8, 'competence', 8), (8, 'competence', 9),
(9, 'ville', 3), (9, 'diplome', 1), (9, 'diplome', 2), (9, 'competence', 9), (9, 'competence', 10),
(10, 'ville', 4), (10, 'diplome', 2), (10, 'diplome', 3), (10, 'competence', 10), (10, 'competence', 11),
(11, 'ville', 5), (11, 'diplome', 3), (11, 'diplome', 4), (11, 'competence', 11), (11, 'competence', 12),
(12, 'ville', 6), (12, 'diplome', 4), (12, 'diplome', 1), (12, 'competence', 12), (12, 'competence', 13),
(13, 'ville', 1), (13, 'diplome', 1), (13, 'diplome', 2), (13, 'competence', 13), (13, 'competence', 14),
(14, 'ville', 2), (14, 'diplome', 2), (14, 'diplome', 3), (14, 'competence', 14), (14, 'competence', 15),
(15, 'ville', 3), (15, 'diplome', 3), (15, 'diplome', 4), (15, 'competence', 15), (15, 'competence', 1),
(16, 'ville', 4), (16, 'diplome', 4), (16, 'diplome', 1), (16, 'competence', 1), (16, 'competence', 2),
(17, 'ville', 5), (17, 'diplome', 1), (17, 'diplome', 2), (17, 'competence', 2), (17, 'competence', 3),
(18, 'ville', 6), (18, 'diplome', 2), (18, 'diplome', 3), (18, 'competence', 3), (18, 'competence', 4),
(19, 'ville', 1), (19, 'diplome', 3), (19, 'diplome', 4), (19, 'competence', 4), (19, 'competence', 5),
(20, 'ville', 2), (20, 'diplome', 4), (20, 'diplome', 1), (20, 'competence', 5), (20, 'competence', 6),
(1, 'competence', 3), (1, 'competence', 4), (2, 'competence', 4), (2, 'competence', 5), (3, 'competence', 5), (3, 'competence', 6),
(4, 'competence', 6), (4, 'competence', 7), (5, 'competence', 7), (5, 'competence', 8), (6, 'competence', 8), (6, 'competence', 9),
(7, 'competence', 9), (7, 'competence', 10), (8, 'competence', 10), (8, 'competence', 11), (9, 'competence', 11), (9, 'competence', 12),
(10, 'competence', 12), (10, 'competence', 13), (11, 'competence', 13), (11, 'competence', 14), (12, 'competence', 14), (12, 'competence', 15),
(13, 'competence', 15), (13, 'competence', 1), (14, 'competence', 1), (14, 'competence', 2), (15, 'competence', 2), (15, 'competence', 3),
(16, 'competence', 3), (16, 'competence', 4), (17, 'competence', 4), (17, 'competence', 5), (18, 'competence', 5), (18, 'competence', 6),
(19, 'competence', 6), (19, 'competence', 7), (20, 'competence', 7), (20, 'competence', 8);

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


-- ======================
-- type_resultat_candidat
-- ======================
INSERT INTO type_resultat_candidat (valeur) VALUES
('refus'),
('attente');

INSERT INTO resultat_candidat (id_candidat, id_type_resultat_candidat, date) VALUES
(1, 1, '2025-09-20'),
(2, 2, '2025-09-20'),
(3, 1, '2025-09-20'),
(4, 2, '2025-09-20'),
(5, 1, '2025-09-20'),
(6, 2, '2025-09-20'),
(7, 1, '2025-09-20'),
(8, 2, '2025-09-20'),
(9, 1, '2025-09-20'),
(10, 2, '2025-09-20');
-- ======================
-- contrat_essai pour 5 candidats (parmi ceux de resultat_candidat)
-- ======================
INSERT INTO contrat_essai (id_candidat, debut, fin) VALUES
(2, '2025-09-21', '2025-12-21'),
(4, '2025-09-22', '2025-12-22'),
(6, '2025-09-23', '2025-12-23'),
(8, '2025-09-24', '2025-12-24'),
(10, '2025-09-25', '2025-12-25');