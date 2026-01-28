CREATE OR REPLACE VIEW view_absence_details AS
SELECT 
    a.id_absence,
    e.id_employe,
    e.nom AS employe_nom,
    e.prenom AS employe_prenom,
    ta.nom AS type_absence,
    a.date_debut AS absence_date_debut,
    a.date_fin AS absence_date_fin,
    COALESCE(da.type_documentation, 'demande') AS type_documentation,
    COALESCE(da.motif, 'Non spécifié') AS motif,
    COALESCE(da.date_documentation, a.date_debut) AS date_documentation,
    CASE 
        WHEN vda.id_validation_documentation_absence IS NOT NULL THEN 'Validé'
        WHEN da.id_documentation_absence IS NOT NULL THEN 'En attente'
        ELSE 'En attente' -- Par défaut, une absence sans documentation est en attente
    END AS validation_status
FROM 
    absence a
JOIN 
    employe e ON e.id_employe = a.id_employe
JOIN 
    type_absence ta ON a.id_type_absence = ta.id_type_absence
LEFT JOIN 
    documentation_absence da ON (
        da.id_employe = a.id_employe 
        AND da.date_debut = a.date_debut 
        AND da.date_fin = a.date_fin
    )
LEFT JOIN 
    validation_documentation_absence vda ON a.id_absence = vda.id_absence;