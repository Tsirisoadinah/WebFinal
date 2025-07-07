
CREATE TABLE finance_Etablissement_Financier (
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   nom_etablissement VARCHAR(50),
   fonds INTEGER
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
   montant_mensuel INTEGER,
   montant_pret INTEGER,
   date_pret DATE,
   date_fin DATE,
   assurance INTEGER,
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
   capital INTEGER,
   interet INTEGER,
   date_paiement DATE,
   FOREIGN KEY(id_pret) REFERENCES finance_Pret(id)
);

CREATE TABLE finance_Transaction (
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   id_ef INTEGER,
   id_client INTEGER,
   id_pret INTEGER,
   date_transaction DATE,
   montant INTEGER,
   FOREIGN KEY(id_ef) REFERENCES finance_Etablissement_Financier(id),
   FOREIGN KEY(id_client) REFERENCES finance_Clients(id),
   FOREIGN KEY(id_pret) REFERENCES finance_Pret(id)
);

CREATE TABLE finance_Client_Pret (
   id INTEGER PRIMARY KEY AUTO_INCREMENT,
   id_pret INTEGER,
   id_client INTEGER,
   FOREIGN KEY(id_client) REFERENCES finance_Clients(id),
   FOREIGN KEY(id_pret) REFERENCES finance_Pret(id)
);