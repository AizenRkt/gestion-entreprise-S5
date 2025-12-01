CREATE TABLE assurance (
    id_assurance INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    minpay INT ,
    maxpay INT,
    taux FLOAT NOT NULL
);

INSERT INTO assurance (id_assurance, nom, minpay, maxpay, taux) VALUES
(1, 'Retenue CNaPS', NULL, NULL, 1),
(2, 'Retenue sanitaire', NULL, NULL, 5),
(3, 'Tranche IRSA 1', 0, 350000, 0),
(4, 'Tranche IRSA 2', 350001, 400000, 5),
(5, 'Tranche IRSA 3', 400001, 500000, 10),
(6, 'Tranche IRSA 4', 500001, 600000, 15),
(7, 'Tranche IRSA 5', 600001, 4000000, 20),
(8, 'Tranche IRSA 6', 4000001, NULL, 25);
