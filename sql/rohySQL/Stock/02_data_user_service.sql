INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES
(6, 'Razafimanantsoa', 'Hanitra', 'hanitraRazafimanantsoa@gmail.com', '123456789', 'M', '2020-02-12');

INSERT INTO user (username, pwd, id_employe) VALUES
('hanitra', '123', 6);

INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES
(6, 3, 1, '2025-11-26 00:00:00');   

--id 11
INSERT INTO service (nom, id_dept) VALUES
('Kpi et Stock', 5);

INSERT INTO poste (titre, id_service) VALUES
-- id 24 Kpi et stcok
('Responsable Stock', 11);

INSERT INTO poste_role (id_poste, id_role, date_role) VALUES
(24, 2, '2024-10-20');  --Hanitra = Manager