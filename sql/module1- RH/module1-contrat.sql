CREATE TABLE poste (
    id_poste INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    id_service INT,
    FOREIGN KEY (id_service) REFERENCES service(id_service)
);

CREATE TABLE candidat (
    id_candidat INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150)  NOT NULL,
    telephone VARCHAR(20),
    genre VARCHAR(1),
    date_naissance DATE,
    date_candidature DATE DEFAULT CURRENT_DATE
);

CREATE TABLE employe (
    id_employe INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    genre VARCHAR(1),
    date_embauche DATE,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat)
);

CREATE TABLE employe_statut (
    id_employe_statut INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    id_poste INT NOT NULL,
    activite INT NOT NULL, -- 0 (pas actif) et 1 (actif)
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe),
    FOREIGN KEY (id_poste) REFERENCES poste(id_poste)
);

CREATE TABLE annonce (
    id_annonce INT AUTO_INCREMENT PRIMARY KEY,
    id_profil INT,
    titre VARCHAR(255) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    age_min INT,
    age_max INT,
    experience INT,
    objectif VARCHAR(255),
    qualite VARCHAR(255),
    FOREIGN KEY (id_profil) REFERENCES profil(id_profil) ON DELETE CASCADE
);

CREATE TABLE statut_annonce (
    id_statut_annonce INT AUTO_INCREMENT PRIMARY KEY,
    id_annonce INT NOT NULL,
    valeur ENUM('retrait','renouvellement') NOT NULL,
    date_fin DATE,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE
);

CREATE TABLE detail_annonce (
    id_detail_annonce INT AUTO_INCREMENT PRIMARY KEY,
    id_annonce INT NOT NULL,
    type ENUM('ville','diplome','competence') NOT NULL,
    id_item INT NOT NULL,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE,
    UNIQUE KEY unique_annonce_item (id_annonce, type, id_item)
);

CREATE TABLE cv (
    id_cv INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT NOT NULL,
    id_profil INT NOT NULL,
    date_soumission DATE DEFAULT CURRENT_DATE,
    photo VARCHAR(255),
    FOREIGN KEY (id_profil) REFERENCES profil(id_profil) ON DELETE CASCADE,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat)
);

CREATE TABLE detail_cv (
    id_detail_cv INT AUTO_INCREMENT PRIMARY KEY,
    id_cv INT NOT NULL,
    type ENUM('ville','diplome','competence') NOT NULL,
    id_item INT NOT NULL,
    FOREIGN KEY (id_cv) REFERENCES cv(id_cv) ON DELETE CASCADE,
    UNIQUE KEY unique_cv_item (id_cv, type, id_item)
);

CREATE TABLE postulance (
    id_postulance INT AUTO_INCREMENT PRIMARY KEY,
    id_cv INT NOT NULL,
    id_annonce INT NOT NULL,
    date_postulation DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (id_cv) REFERENCES cv(id_cv) ON DELETE CASCADE,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE
);

SELECT DISTINCT c.id_candidat, c.nom, c.prenom
FROM candidat c
JOIN cv ON cv.id_candidat = c.id_candidat
JOIN postulance p ON p.id_cv = cv.id_cv
-- QCM scoring
JOIN scoring sq ON sq.id_candidat = c.id_candidat AND sq.id_type_scoring = 1
-- Entretien scoring
JOIN scoring se ON se.id_candidat = c.id_candidat AND se.id_type_scoring = 2
-- Entretien détail (pour le "recommande")
JOIN entretien_candidat ec ON ec.id_candidat = c.id_candidat
JOIN detail_entretien de ON de.id_entretien = ec.id_entretien
WHERE p.id_annonce = 2
  AND sq.valeur >= (
          SELECT AVG(sq2.valeur) 
          FROM scoring sq2 
          WHERE sq2.id_type_scoring = 1
      )
  AND se.valeur >= (
          SELECT AVG(se2.valeur) 
          FROM scoring se2 
          WHERE se2.id_type_scoring = 2
      )
  AND de.evaluation = 'recommande';

SELECT DISTINCT 
    c.id_candidat, 
    c.nom, 
    c.prenom, 
    c.email, 
    c.date_candidature,
    sq.valeur AS note_qcm,
    MAX(dq.bareme_question) AS note_max_qcm,
    se.valeur AS note_entretien,
    de.evaluation AS evaluation_entretien
FROM candidat c
JOIN cv ON cv.id_candidat = c.id_candidat
JOIN postulance p ON p.id_cv = cv.id_cv
-- Scores
JOIN scoring sq ON sq.id_candidat = c.id_candidat AND sq.id_type_scoring = 1
JOIN scoring se ON se.id_candidat = c.id_candidat AND se.id_type_scoring = 2
-- QCM détail pour récupérer la note max
JOIN detail_qcm dq ON dq.id_question = sq.id_item
-- Entretien
JOIN entretien_candidat ec ON ec.id_candidat = c.id_candidat
JOIN detail_entretien de ON de.id_entretien = ec.id_entretien
WHERE p.id_annonce = 1
  AND sq.valeur >= dq.bareme_question / 2
  AND de.evaluation = 'recommande'
GROUP BY c.id_candidat, c.nom, c.prenom, c.email, c.date_candidature, sq.valeur, se.valeur, de.evaluation;

CREATE TABLE candidat (
    id_candidat INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150)  NOT NULL,
    telephone VARCHAR(20),
    genre VARCHAR(1),
    date_naissance DATE,
    date_candidature DATE DEFAULT CURRENT_DATE
);

CREATE TABLE employe (
    id_employe INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    genre VARCHAR(1),
    date_embauche DATE,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat)
);