-- =========================
-- Tables
-- =========================

CREATE TABLE poste_competence (
    id_poste INT NOT NULL,
    id_competence INT NOT NULL,
    PRIMARY KEY (id_poste, id_competence)
);

CREATE TABLE employe_competence (
    id_employe INT NOT NULL,
    id_competence INT NOT NULL,
    PRIMARY KEY (id_employe, id_competence)
);

CREATE TABLE employe_formation (
    id_employe_formation SERIAL PRIMARY KEY,
    id_employe INT NOT NULL,
    id_formation INT NOT NULL,
    date_assigned DATE NOT NULL DEFAULT CURRENT_DATE,
    date_completed DATE,
    status VARCHAR(20) DEFAULT 'ASSIGNED'
);

CREATE TABLE formation (
    id_formation SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE formation_competence (
    id_formation INT NOT NULL,
    id_competence INT NOT NULL,
    PRIMARY KEY (id_formation, id_competence)
);

-- =========================
-- Views
-- =========================

CREATE VIEW vw_poste_match AS
SELECT
    pc.id_poste,
    e.id_employe,
    COUNT(pc.id_competence) AS total_required_skills,
    COUNT(ec.id_competence) AS skills_matched,
    (COUNT(ec.id_competence) = COUNT(pc.id_competence)) AS is_full_match
FROM poste_competence pc
CROSS JOIN employe e
LEFT JOIN employe_competence ec
    ON ec.id_employe = e.id_employe
    AND ec.id_competence = pc.id_competence
GROUP BY pc.id_poste, e.id_employe;

CREATE VIEW vw_missing_skills AS
SELECT
    pc.id_poste,
    e.id_employe,
    c.id_competence,
    c.nom AS missing_competence
FROM poste_competence pc
JOIN competence c ON c.id_competence = pc.id_competence
CROSS JOIN employe e
LEFT JOIN employe_competence ec
    ON ec.id_employe = e.id_employe
    AND ec.id_competence = pc.id_competence
WHERE ec.id_competence IS NULL;

CREATE VIEW vw_formation_suggestion AS
SELECT
    ms.id_poste,
    ms.id_employe,
    ms.id_competence,
    f.id_formation,
    f.nom AS formation_name
FROM vw_missing_skills ms
JOIN formation_competence fc 
    ON fc.id_competence = ms.id_competence
JOIN formation f 
    ON f.id_formation = fc.id_formation;

CREATE VIEW vw_employe_formation_history AS
SELECT
    ef.id_employe,
    e.nom AS employe_name,
    ef.id_formation,
    f.nom AS formation_name,
    ef.date_assigned,
    ef.date_completed,
    ef.status
FROM employe_formation ef
JOIN employe e ON e.id_employe = ef.id_employe
JOIN formation f ON f.id_formation = ef.id_formation;

CREATE VIEW vw_untrained_missing_skills AS
SELECT *
FROM vw_missing_skills ms
WHERE NOT EXISTS (
    SELECT 1
    FROM employe_formation ef
    JOIN formation_competence fc ON fc.id_formation = ef.id_formation
    WHERE ef.id_employe = ms.id_employe
      AND fc.id_competence = ms.id_competence
      AND ef.status IN ('ASSIGNED', 'IN_PROGRESS', 'COMPLETED')
);
