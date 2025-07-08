-- Données pour finance_Etablissement_Financier
INSERT INTO finance_Etablissement_Financier (nom_etablissement, fonds) VALUES
('BNI', 50000.00),
('BNI', 250000.00),
('BNI', 75000.00);

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



-- Données pour finance_Type_Transaction
INSERT INTO finance_Type_Transaction (libelle) VALUES
('Ajout');