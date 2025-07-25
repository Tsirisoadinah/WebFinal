
CREATE TABLE finance_Etablissement_Financier (
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   nom_etablissement VARCHAR(50),
   fonds DECIMAL(10,2)
);

CREATE TABLE finance_Type_Pret (
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   libelle VARCHAR(50),
   taux DECIMAL(10,2),
   duree_max INTEGER
);

CREATE TABLE finance_Clients (
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   nom VARCHAR(50),
   email VARCHAR(50),
   revenu_mensuel INTEGER,
   score_credit INTEGER
);

CREATE TABLE finance_Pret (
   id_type_pret INTEGER,
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   montant_mensuel DECIMAL(10,2),
   montant_pret DECIMAL(10,2),
   date_pret DATE,
   date_fin DATE,
   assurance DECIMAL(10,2),
   delai INTEGER,
   FOREIGN KEY(id_type_pret) REFERENCES finance_Type_Pret(id)
);

CREATE TABLE finance_Type_Transaction (
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   libelle VARCHAR(50)
);

CREATE TABLE finance_Historique_Pret (
   id_pret INTEGER,
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   capital DECIMAL(10,2),
   interet DECIMAL(10,2),
   date_paiement DATE,
   FOREIGN KEY(id_pret) REFERENCES finance_Pret(id)
);

CREATE TABLE finance_Transaction (
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   id_ef INTEGER,
   id_client INTEGER,
   id_type_transaction INTEGER,
   date_transaction DATE,
   montant DECIMAL(10,2),
   FOREIGN KEY(id_ef) REFERENCES finance_Etablissement_Financier(id),
   FOREIGN KEY(id_client) REFERENCES finance_Clients(id),
   FOREIGN KEY(id_type_transaction) REFERENCES finance_Type_Transaction(id)
);

CREATE TABLE finance_Client_Pret (
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   id_pret INTEGER,
   id_client INTEGER,
   FOREIGN KEY(id_client) REFERENCES finance_Clients(id),
   FOREIGN KEY(id_pret) REFERENCES finance_Pret(id)
);

CREATE TABLE finance_Simulation(
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   nom VARCHAR(50),
   id_client INTEGER,
   mensualite DECIMAL(10,2),
   montant_pret DECIMAL(10,2),
   taux DECIMAL(10,2),
   duree INTEGER,
   date_debut DATE,
   date_fin DATE,
   assurance DECIMAL(10,2),
   delai INTEGER,
   FOREIGN KEY(id_client) REFERENCES finance_Clients(id)
);