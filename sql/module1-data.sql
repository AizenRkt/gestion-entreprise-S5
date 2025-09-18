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
-- candidat
-- ======================
INSERT INTO candidat (nom, prenom, email, telephone, genre) VALUES
('Rakoto', 'Jean', 'jean.rakoto@example.com', '0321112233', 'M'),
('Randria', 'Marie', 'marie.randria@example.com', '0324445566', 'F'),
('Ando', 'Paul', 'paul.ando@example.com', '0337778899', 'M'),
('Rasoanaivo', 'Lalao', 'lalao.raso@example.com', '0341234567', 'F'),
('Raharinirina', 'Eric', 'eric.rahar@example.com', '0349876543', 'M');

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
('BEPC'),
('BACC'),
('CAP'),
('BACC+1'),
('BACC+2'),
('BACC+3'),
('BACC+4'),
('BACC+5'),
('BACC+6'),
('Doctorat');
;

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
