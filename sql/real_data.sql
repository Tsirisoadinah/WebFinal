-- Données pour finance_Etablissement_Financier
INSERT INTO finance_Etablissement_Financier (nom_etablissement, fondsg) VALUES
('BNI', 50000000000.00),
('BNI', 25000000000.00),
('BNI', 75000000000.00);

-- Données pour finance_Type_Pret
INSERT INTO finance_Type_Pret (libelle, taux, duree_max) VALUES
('Prêt Personnel', 18.50, 60),
('Prêt Immobilier', 12.75, 240),
('Prêt Auto', 15.25, 84),
('Prêt Entreprise', 14.00, 120),
('Prêt Étudiant', 8.50, 96);

-- Données pour finance_Clients
INSERT INTO finance_Clients (nom, email, revenu_mensuel, score_credit) VALUES
('Rakoto Jean', 'rakoto.jean@email.mg', 2500000, 750),
('Rabe Marie', 'rabe.marie@email.mg', 3200000, 680),
('Randria Paul', 'randria.paul@email.mg', 4500000, 820),
('Razafy Sophie', 'razafy.sophie@email.mg', 1800000, 650),
('Rasolofo Michel', 'rasolofo.michel@email.mg', 5600000, 780),
('Andrianary Hery', 'andrianary.hery@email.mg', 2100000, 720),
('Raharisoa Nivo', 'raharisoa.nivo@email.mg', 3800000, 695),
('Ratsimandresy Fidy', 'ratsimandresy.fidy@email.mg', 2800000, 760),
('Razafindrakoto Lala', 'razafindrakoto.lala@email.mg', 4200000, 710),
('Randriamampionona Soa', 'randriamampionona.soa@email.mg', 3600000, 800);

-- Données pour finance_Pret
INSERT INTO finance_Pret (id_type_pret, montant_mensuel, montant_pret, date_pret, date_fin, assurance, delai) VALUES
(1, 450000.00, 15000000.00, '2023-01-15', '2026-01-15', 150000.00, 36),
(2, 850000.00, 120000000.00, '2022-06-20', '2037-06-20', 600000.00, 180),
(3, 320000.00, 18000000.00, '2023-03-10', '2028-03-10', 180000.00, 60),
(1, 280000.00, 8000000.00, '2023-07-05', '2026-01-05', 80000.00, 30),
(4, 750000.00, 45000000.00, '2022-11-12', '2030-11-12', 450000.00, 96),
(2, 1200000.00, 180000000.00, '2023-02-28', '2038-02-28', 900000.00, 180),
(3, 380000.00, 22000000.00, '2023-05-18', '2028-11-18', 220000.00, 66),
(1, 350000.00, 12000000.00, '2023-08-22', '2026-08-22', 120000.00, 36),
(5, 180000.00, 6000000.00, '2023-04-01', '2027-04-01', 60000.00, 48),
(4, 680000.00, 35000000.00, '2023-06-15', '2030-06-15', 350000.00, 84);

-- Données pour finance_Type_Transaction
INSERT INTO finance_Type_Transaction (libelle) VALUES
('Ajout');