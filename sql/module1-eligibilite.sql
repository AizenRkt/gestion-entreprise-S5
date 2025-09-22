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

CREATE TABLE postulance (
    id_postulance INT AUTO_INCREMENT PRIMARY KEY,
    id_cv INT NOT NULL,
    id_annonce INT NOT NULL,
    date_postulation DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (id_cv) REFERENCES cv(id_cv) ON DELETE CASCADE,
    FOREIGN KEY (id_annonce) REFERENCES annonce(id_annonce) ON DELETE CASCADE
);

-- selection 
SELECT c.id_candidat, c.nom, c.prenom, c.email
FROM postulance p
JOIN cv cv ON p.id_cv = cv.id_cv
JOIN candidat c ON cv.id_candidat = c.id_candidat
JOIN annonce a ON p.id_annonce = a.id_annonce
WHERE p.id_annonce = 1
    -- Vérifier l’âge
    AND (a.age_min IS NULL OR TIMESTAMPDIFF(YEAR, c.date_naissance, CURDATE()) >= a.age_min)
    AND (a.age_max IS NULL OR TIMESTAMPDIFF(YEAR, c.date_naissance, CURDATE()) <= a.age_max)
    -- Vérifier compétences obligatoires
    AND NOT EXISTS (
        SELECT 1
        FROM detail_annonce da
        WHERE da.id_annonce = a.id_annonce
            AND da.type = 'competence'
            AND da.id_item NOT IN (
                SELECT dc.id_item
                FROM detail_cv dc
                WHERE dc.id_cv = cv.id_cv
                AND dc.type = 'competence'
            )
    )
    -- Vérifier diplômes obligatoires
    AND NOT EXISTS (
        SELECT 1
        FROM detail_annonce da
        WHERE da.id_annonce = a.id_annonce
        AND da.type = 'diplome'
        AND NOT EXISTS (
            SELECT 1
            FROM detail_cv dc
            WHERE dc.id_cv = cv.id_cv
                AND dc.type = 'diplome'
                AND dc.id_item = da.id_item
        )
    )

    -- Vérifier ville si exigée
    AND NOT EXISTS (
        SELECT 1
        FROM detail_annonce da
        WHERE da.id_annonce = a.id_annonce
            AND da.type = 'ville'
            AND da.id_item NOT IN (
                SELECT dc.id_item
                FROM detail_cv dc
                WHERE dc.id_cv = cv.id_cv
                AND dc.type = 'ville'
            )
    );


SELECT 1
FROM postulance p
JOIN cv cv ON p.id_cv = cv.id_cv
JOIN candidat c ON cv.id_candidat = c.id_candidat
JOIN annonce a ON p.id_annonce = a.id_annonce
WHERE p.id_annonce = 1
AND c.id_candidat = 10
AND (a.age_min IS NULL OR TIMESTAMPDIFF(YEAR, c.date_naissance, CURDATE()) >= a.age_min)
AND (a.age_max IS NULL OR TIMESTAMPDIFF(YEAR, c.date_naissance, CURDATE()) <= a.age_max)
AND NOT EXISTS (
    SELECT 1
    FROM detail_annonce da
    WHERE da.id_annonce = a.id_annonce
        AND da.type = 'competence'
        AND NOT EXISTS (
            SELECT 1
            FROM detail_cv dc
            WHERE dc.id_cv = cv.id_cv
            AND dc.type = 'competence'
            AND dc.id_item = da.id_item
        )
)
AND NOT EXISTS (
    SELECT 1
    FROM detail_annonce da
    WHERE da.id_annonce = a.id_annonce
        AND da.type = 'diplome'
        AND NOT EXISTS (
            SELECT 1
            FROM detail_cv dc
            WHERE dc.id_cv = cv.id_cv
            AND dc.type = 'diplome'
            AND dc.id_item = da.id_item
        )
)
AND NOT EXISTS (
    SELECT 1
    FROM detail_annonce da
    WHERE da.id_annonce = a.id_annonce
        AND da.type = 'ville'
        AND NOT EXISTS (
            SELECT 1
            FROM detail_cv dc
            WHERE dc.id_cv = cv.id_cv
            AND dc.type = 'ville'
            AND dc.id_item = da.id_item
        )
)
LIMIT 1