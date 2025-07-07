-- Données de test pour finance_Clients
INSERT INTO finance_Clients (nom, email, revenu_mensuel, score_credit) VALUES
('Jean Dupont', 'jean.dupont@example.com', 3000, 750),
('Marie Martin', 'marie.martin@example.com', 2500, 680),
('Pierre Durand', 'pierre.durand@example.com', 3500, 800),
('Sophie Lefevre', 'sophie.lefevre@example.com', 2200, 720);

-- Données de test pour finance_Type_Pret
INSERT INTO finance_Type_Pret (libelle, taux, duree_max) VALUES
('Prêt Immobilier', 2.5, 300),
('Prêt Personnel', 5.0, 60),
('Prêt Auto', 3.75, 84),
('Prêt Étudiant', 1.5, 120);

-- Données pour finance_Etablissement_Financier
INSERT INTO finance_Etablissement_Financier (nom_etablissement, fonds) VALUES
('Banque Principale', 10000000);

-- Données pour finance_Type_Transaction
INSERT INTO finance_Type_Transaction (libelle) VALUES
('Ajout de fonds'),
('Prêt accordé'),
('Remboursement');
