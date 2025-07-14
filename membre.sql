DROP DATABASE IF EXISTS membres;
CREATE DATABASE membres;
USE membres;

CREATE TABLE membre (
    id_membre int PRIMARY KEY auto_increment,
    nom VARCHAR (100),
    date_naissance DATE,
    genre ENUM('H', 'F'),
    email VARCHAR (100),
    ville VARCHAR (100),
    mdp VARCHAR (100),
    image_profil VARCHAR (100)

);

CREATE TABLE categorie_objet(
    id_categorie int PRIMARY KEY auto_increment,
    nom_categorie VARCHAR (100)
);

CREATE TABLE objet (
    id_objet int PRIMARY KEY auto_increment,
    nom_objet VARCHAR (100),
    id_categorie int ,
    id_membre int

);


CREATE TABLE images_objet (
    id_image int PRIMARY KEY auto_increment,
    id_objet int,
    nom_image VARCHAR(100),
    is_principale BOOLEAN DEFAULT 0,
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet) ON DELETE CASCADE


    
);

CREATE TABLE emprunt (
    id_emprunt int PRIMARY KEY auto_increment,
    id_objet int,
    id_membre int,
    date_emprunt date ,
    date_retour date

);


INSERT INTO categorie_objet (nom_categorie) VALUES
('Esthetique'), ('Bricolage'), ('Mecanique'), ('Cuisine');


INSERT INTO membre (nom, date_naissance, genre, email, ville, mdp, image_profil) VALUES
('Alice', '2000-05-12', 'F', 'alice@example.com', 'Paris', 'mdp1', 'alice.jpg'),
('Bob', '1998-11-30', 'H', 'bob@example.com', 'Lyon', 'mdp2', 'bob.jpg'),
('Charlie', '2002-01-21', 'H', 'charlie@example.com', 'Toulouse', 'mdp3', 'charlie.jpg'),
('Diana', '1999-08-09', 'F', 'diana@example.com', 'Nice', 'mdp4', 'diana.jpg');


INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
('Seche-cheveux', 1, 1), ('Fer a lisser', 1, 1), ('Perceuse', 2, 1), ('Tournevis', 2, 1), ('Pompe a velo', 3, 1), ('Cle a molette', 3, 1), ('Mixeur', 4, 1), ('Balance', 4, 1), ('Peigne', 1, 1), ('Creme visage', 1, 1),

('Marteau', 2, 2), ('Scie', 2, 2), ('Compresseur', 3, 2), ('Tournevis etoile', 2, 2), ('Spatule', 4, 2), ('Casserole', 4, 2), ('Brosse cheveux', 1, 2), ('Tondeuse', 1, 2), ('Extracteur jus', 4, 2), ('Mixer bol', 4, 2),

('Cle plate', 3, 3), ('Chalumeau', 3, 3), ('Pince multiprise', 2, 3), ('Cuillere bois', 4, 3), ('Planche a decouper', 4, 3), ('Lime', 2, 3), ('Gel coiffant', 1, 3), ('Shampoing', 1, 3), ('Ciseaux', 1, 3), ('Marmite', 4, 3),

('Four', 4, 4), ('Grille-pain', 4, 4), ('Tournevis plat', 2, 4), ('Fusible', 3, 4), ('Pompe manuelle', 3, 4), ('Scie sauteuse', 2, 4), ('Peigne fin', 1, 4), ('Rasoir', 1, 4), ('Creme main', 1, 4), ('Batteur', 4, 4);


INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) VALUES
(1, 2, '2025-07-01', '2025-07-07'),
(2, 3, '2025-07-03', NULL),
(5, 4, '2025-07-05', '2025-07-10'),
(10, 1, '2025-07-02', NULL),
(13, 1, '2025-07-01', '2025-07-08'),
(15, 3, '2025-07-04', NULL),
(22, 2, '2025-07-06', NULL),
(27, 4, '2025-07-07', '2025-07-14'),
(31, 3, '2025-07-08', NULL),
(36, 1, '2025-07-09', NULL);
