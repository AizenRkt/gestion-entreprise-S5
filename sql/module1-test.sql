-- table
CREATE TABLE profil (
    id_profil INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE qcm (
    id_qcm INT AUTO_INCREMENT PRIMARY KEY,
    id_annonce INT NOT NULL,
    id_profil INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    note_max DECIMAL(5,2) NOT NULL,  
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE,
    FOREIGN KEY (id_profil) REFERENCES profil(id_profil) ON DELETE CASCADE
);

CREATE TABLE question (
    id_question INT AUTO_INCREMENT PRIMARY KEY,
    enonce VARCHAR(255) NOT NULL
);

CREATE TABLE reponse (
    id_reponse INT AUTO_INCREMENT PRIMARY KEY,
    id_question INT NOT NULL,
    texte TEXT NOT NULL,
    est_correcte BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_question) REFERENCES question(id_question) ON DELETE CASCADE
);

CREATE TABLE detail_qcm (
    id_detail_qcm INT AUTO_INCREMENT PRIMARY KEY,
    id_qcm INT NOT NULL,
    id_question INT NOT NULL,
    bareme_question DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (id_qcm) REFERENCES qcm(id_qcm) ON DELETE CASCADE,
    FOREIGN KEY (id_question) REFERENCES question(id_question) ON DELETE CASCADE,
    UNIQUE KEY unique_qcm_question (id_qcm, id_question)
);

-- data
-- ====== INSERTS POUR profil ======
INSERT INTO profil (nom) VALUES
('Développeur'),
('Designer'),
('Chef de projet');

-- ====== INSERTS POUR qcm ======
INSERT INTO annonce (id_profil, titre, date_debut, date_fin, age_min, age_max, experience, objectif, qualite)
VALUES
(1, 'Annonce Front-End', '2025-09-01', '2025-10-01', 22, 35, 2, 'Créer une application web', 'Créatif, rigoureux'),
(2, 'Annonce Design', '2025-09-05', '2025-10-05', 25, 40, 3, 'Améliorer UX', 'Imaginatif, précis'),
(3, 'Annonce Chef de projet', '2025-09-10', '2025-11-10', 28, 45, 5, 'Gérer une équipe', 'Leadership, organisation');


-- On suppose que les annonces existent déjà avec id_annonce 1,2,3
INSERT INTO qcm (id_annonce, id_profil, titre, note_max) VALUES
(1, 1, 'QCM Front-End', 20),
(2, 2, 'QCM Design', 15),
(3, 3, 'QCM Gestion de projet', 25);

-- ====== INSERTS POUR question ======
INSERT INTO question (enonce) VALUES
('Quelle est la balise HTML pour un paragraphe ?'),
('Quelle propriété CSS change la couleur du texte ?'),
('Quelle méthode JavaScript est utilisée pour ajouter un élément au DOM ?'),
('Qu''est-ce que le wireframe en design ?'),
('Quel outil est le plus utilisé pour la gestion de projet Agile ?');

-- ====== INSERTS POUR reponse ======
-- Question 1
INSERT INTO reponse (id_question, texte, est_correcte) VALUES
(1, 'p', TRUE),
(1, 'div', FALSE),
(1, 'span', FALSE),
(1, 'section', FALSE);

-- Question 2
INSERT INTO reponse (id_question, texte, est_correcte) VALUES
(2, 'color', TRUE),
(2, 'background-color', FALSE),
(2, 'font-size', FALSE),
(2, 'text-decoration', FALSE);

-- Question 3
INSERT INTO reponse (id_question, texte, est_correcte) VALUES
(3, 'appendChild()', TRUE),
(3, 'getElementById()', FALSE),
(3, 'querySelector()', FALSE),
(3, 'removeChild()', FALSE);

-- Question 4
INSERT INTO reponse (id_question, texte, est_correcte) VALUES
(4, 'Un prototype de l''interface', TRUE),
(4, 'Un style CSS', FALSE),
(4, 'Une base de données', FALSE),
(4, 'Une feuille de route', FALSE);

-- Question 5
INSERT INTO reponse (id_question, texte, est_correcte) VALUES
(5, 'Jira', TRUE),
(5, 'Photoshop', FALSE),
(5, 'Figma', FALSE),
(5, 'Slack', FALSE);

-- ====== INSERTS POUR detail_qcm ======
INSERT INTO detail_qcm (id_qcm, id_question, bareme_question) VALUES
(1, 1, 5),  -- QCM Front-End, Question 1
(1, 2, 5),  -- QCM Front-End, Question 2
(1, 3, 10), -- QCM Front-End, Question 3
(2, 4, 15), -- QCM Design, Question 4
(3, 5, 25); -- QCM Gestion de projet, Question 5


-- insert
INSERT INTO qcm (id_annonce, titre, note_max)
VALUES (:id_annonce, :titre, :note_max);

INSERT INTO question (enonce) VALUES (:enonce);

INSERT INTO reponse (id_question, texte, est_correcte) VALUES (:id_question, :texte, :est_correcte);

INSERT INTO detail_qcm (id_qcm, id_question, bareme_question)
VALUES (:id_qcm, :id_question, :bareme_question);


-- request
SELECT q.id_qcm, q.titre, q.note_max, q.date_creation -- getById (annonce)
FROM qcm q
JOIN annonce a ON q.id_annonce = a.id_annonce
WHERE a.id_annonce = :id_annonce;

SELECT q.id_qcm, q.titre, q.note_max, q.date_creation
FROM qcm q
JOIN annonce a ON q.id_annonce = a.id_annonce
WHERE a.id_annonce = 1;


SELECT q.id_question, q.enonce, r.id_reponse, r.texte -- QCM (questions + réponses)
FROM detail_qcm dq
JOIN question q ON dq.id_question = q.id_question
JOIN reponse r ON r.id_question = q.id_question
WHERE dq.id_qcm = :id_qcm
ORDER BY q.id_question;

SELECT q.id_question, q.enonce, r.id_reponse, r.texte
FROM detail_qcm dq
JOIN question q ON dq.id_question = q.id_question
JOIN reponse r ON r.id_question = q.id_question
WHERE dq.id_qcm = 3
ORDER BY q.id_question;


SELECT 
    q.id_question,
    q.enonce AS question,
    r.id_reponse,
    r.texte AS reponse,
    r.est_correcte
FROM question q
JOIN reponse r ON q.id_question = r.id_question
WHERE q.id_question = :id_question;

SELECT 
    q.id_question,
    q.enonce AS question,
    r.id_reponse,
    r.texte AS reponse,
    r.est_correcte
FROM question q
JOIN reponse r ON q.id_question = r.id_question
WHERE q.id_question = 2;

{
    id_question : 
    enonce :
    reponse : {
        id_reponse :
        reponse :
        est_correcte
    }
    {
        id_reponse :
        reponse :
        est_correcte
    }
    {
        id_reponse :
        reponse :
        est_correcte
    }
}

SELECT u.* FROM user u
            JOIN employe_statut es ON u.id_employe = es.id_employe
            WHERE u.username = 'jrakoto' AND es.activite = 1
            ORDER BY es.date_modification DESC
            LIMIT 1