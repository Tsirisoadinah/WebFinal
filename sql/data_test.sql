INSERT INTO finance_Type_Transaction (id, libelle) VALUES
(1, 'Ajout de fonds');

INSERT INTO finance_Etablissement_Financier (id, nom_etablissement, fonds) VALUES
(1, 'Banque A', 1000000);

SELECT * FROM `finance_Transaction`

INSERT INTO finance_Type_Pret (libelle, taux, duree_max) VALUES
('Prêt personnel', 5.50, 60),
('Prêt immobilier', 3.80, 240),
('Prêt auto', 4.20, 84);

INSERT INTO finance_Clients (nom, email, revenu_mensuel, score_credit) VALUES
('Dupont Jean', 'jean.dupont@email.com', 3500, 720),
('Martin Sophie', 'sophie.martin@email.com', 4200, 680),
('Lefèvre Paul', 'paul.lefevre@email.com', 2800, 650);

INSERT INTO finance_Pret (id_type_pret, montant_mensuel, montant_pret, date_pret, date_fin, assurance, delai) VALUES
(1, 500, 25000, '2024-01-01', '2028-12-31', 20, 60),
(2, 1200, 200000, '2023-06-01', '2033-05-31', 50, 120),
(3, 350, 15000, '2025-01-01', '2029-12-31', 15, 60);

INSERT INTO finance_Historique_Pret (id_pret, capital, interet, date_paiement) VALUES
(1, 480, 68.75, '2024-02-01'), -- Prêt 1, intérêts basés sur taux 5.5%
(1, 480, 68.50, '2024-03-01'),
(1, 480, 68.25, '2024-04-01'),
(2, 1000, 633.33, '2023-07-01'), -- Prêt 2, intérêts basés sur taux 3.8%
(2, 1000, 632.50, '2023-08-01'),
(3, 335, 52.50, '2025-02-01'), -- Prêt 3, intérêts basés sur taux 4.2%
(3, 335, 52.25, '2025-03-01');

INSERT INTO finance_Client_Pret (id_pret, id_client) VALUES
(1, 1), -- Jean Dupont avec Prêt personnel
(2, 2), -- Sophie Martin avec Prêt immobilier
(3, 3); -- Paul Lefèvre avec Prêt auto


SELECT 
                    YEAR(date_paiement) AS annee,
                    MONTH(date_paiement) AS mois,
                    SUM(interet) AS total_interets,
                    SUM(capital) AS total_capital
                FROM finance_Historique_Pret
                WHERE date_paiement BETWEEN '01-02-2024' AND '01-04-2024'
                GROUP BY YEAR(date_paiement), MONTH(date_paiement)
                ORDER BY annee DESC, mois DESC

UPDATE `finance_Historique_Pret` SET interet = 40 WHERE id = 1;