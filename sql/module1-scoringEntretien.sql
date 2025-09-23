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

CREATE TABLE entretien_candidat (
    id_entretien INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT NOT NULL,
    date DATETIME NOT NULL,
    duree INT,
    id_user INT,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat),
    FOREIGN KEY (id_user) REFERENCES user(id_user)
);

CREATE TABLE detail_entretien (
    id_detail_entretien INT AUTO_INCREMENT PRIMARY KEY,
    id_entretien INT,
    evaluation ENUM('recommande', 'reserve', 'refuse') NULL, 
    commentaire VARCHAR(255) NULL,
    FOREIGN KEY (id_entretien) REFERENCES entretien_candidat(id_entretien)
);

CREATE TABLE type_scoring (
    id_type_scoring INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE scoring (
    id_scoring INT AUTO_INCREMENT PRIMARY KEY,
    id_candidat INT NOT NULL,
    id_type_scoring INT NOT NULL,
    id_item INT NOT NULL,
    valeur DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (id_candidat) REFERENCES candidat(id_candidat),
    FOREIGN KEY (id_type_scoring) REFERENCES type_scoring(id_type_scoring)
);

SELECT 
    s.id_candidat,
    c.nom,
    c.prenom,
    s.valeur,
    q.note_max,
    (q.note_max / 2) AS moyenne
FROM scoring s
JOIN candidat c ON s.id_candidat = c.id_candidat
JOIN qcm q ON s.id_item = q.id_qcm
LEFT JOIN employe e ON c.id_candidat = e.id_candidat
LEFT JOIN entretien_candidat ent ON c.id_candidat = ent.id_candidat
WHERE s.id_item = 1
AND s.id_type_scoring = 1
AND s.valeur >= (q.note_max / 2)
AND e.id_candidat IS NULL
AND ent.id_candidat IS NULL