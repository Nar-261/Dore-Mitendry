USE doremitendry;

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE exercices;
TRUNCATE TABLE lecons;
TRUNCATE TABLE modules;
TRUNCATE TABLE partitions;
TRUNCATE TABLE cours;
TRUNCATE TABLE instruments;
TRUNCATE TABLE utilisateurs;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO utilisateurs (id, nom, prenom, email, telephone, photo, mot_de_passe, role, date_inscription)
VALUES
(1, 'Martin', 'Claire', 'claire.martin@email.com', '0601020304', NULL, 'motdepasse123', 'apprenant', '2025-01-10 09:15:00'),
(2, 'Dubois', 'Lucas', 'lucas.dubois@email.com', '0607080910', NULL, 'guitare2025', 'apprenant', '2025-02-18 14:45:00'),
(3, 'Nguyen', 'Sophie', 'sophie.nguyen@email.com', '0611223344', NULL, 'violon77', 'admin', '2024-12-01 08:00:00'),
(4, 'Moreau', 'Hugo', 'hugo.moreau@email.com', '0677889900', NULL, 'piano321', 'apprenant', '2025-03-07 11:20:00');

INSERT INTO instruments (id, nom, description)
VALUES
(1, 'Piano', 'Instrument classique a cordes frappees, ideal pour la theorie musicale et le rythme.'),
(2, 'Guitare', 'Instrument a cordes pincees tres utilise dans les styles acoustiques et pop.'),
(3, 'Violon', 'Instrument a cordes frottees, apprecie pour son son expressif et sa technique.');

INSERT INTO cours (id, titre, description, instrument_id, niveau)
VALUES
(1, 'Decouverte du piano', 'Apprendre les bases du clavier, la posture et les premieres notes.', 1, 'Debutant'),
(2, 'Guitare acoustique pour debutants', 'Maitriser les accords simples, le rythme et les premieres melodies.', 2, 'Debutant'),
(3, 'Initiation au violon', 'Decouvrir la tenue de l''instrument, les premieres positions et les sons de base.', 3, 'Debutant'),
(4, 'Piano niveau intermediaire', 'Approfondir la lecture de notes et la coordination mains gauche/droite.', 1, 'Intermediaire');

INSERT INTO modules (id, cours_id, titre, description, ordre, image_hero)
VALUES
(1, 1, 'Les bases du clavier', 'Comprendre la disposition des touches et la position des mains.', 1, 'piano_clavier.jpg'),
(2, 1, 'Lecture des notes', 'Lire les notes sur la portee et les associer au clavier.', 2, 'piano_notes.jpg'),
(3, 2, 'Premiers accords', 'Apprendre les accords de base et la mise en forme des doigts.', 1, 'guitare_accords.jpg'),
(4, 2, 'Rythme et strumming', 'Creer un rythme regulier et accompagner une melodie simple.', 2, 'guitare_rythme.jpg'),
(5, 3, 'Tenue de l''instrument', 'Apprendre la bonne posture et le placement du violon.', 1, 'violon_posture.jpg'),
(6, 3, 'Les premieres notes', 'Jouer des notes simples avec precision et controle du son.', 2, 'violon_notes.jpg'),
(7, 4, 'Coordination des mains', 'Travailler la coordination entre main gauche et main droite.', 1, 'piano_mains.jpg');

INSERT INTO lecons (id, module_id, titre, contenu)
VALUES
(1, 1, 'Position des mains', 'Placez vos mains sur le clavier dans une posture naturelle et detendue.'),
(2, 1, 'Decouverte des octaves', 'Identifiez les groupes de touches et apprenez a reperer les octaves.'),
(3, 2, 'Lecture de la portee', 'Comprenez la relation entre les notes ecrites et les notes jouees.'),
(4, 2, 'Exercices de memorisation', 'Travaillez la reconnaissance rapide des notes sur les touches du piano.'),
(5, 3, 'Accord C majeur', 'Apprenez a placer les doigts et a faire sonner l''accord correctement.'),
(6, 3, 'Accord G majeur', 'Travaillez la transition entre les accords et la stabilite du son.'),
(7, 4, 'Les rythmes de base', 'Exercez des battements simples pour garder un tempo regulier.'),
(8, 4, 'Strumming simple', 'Enchainez des mouvements bases sur des patterns de base.'),
(9, 5, 'Position du violon', 'Apprenez a tenir l''instrument sans tension excessive.'),
(10, 5, 'Placement du archet', 'Decouvrez la bonne prise et le mouvement du archet.'),
(11, 6, 'Les notes La et Mi', 'Jouez les premieres notes avec precision et controle du souffle.'),
(12, 6, 'Phonetique du son', 'Ameliorez la qualite du son a partir de la justesse du doigté.'),
(13, 7, 'Main gauche', 'Travaillez la stabilite et la precision de la main gauche.'),
(14, 7, 'Main droite', 'Entrainez le mouvement et le toucher de la main droite.');

INSERT INTO exercices (id, lecon_id, question, correction)
VALUES
(1, 1, 'Ou devez-vous placer vos mains au debut du cours ?', 'Les mains doivent etre relachees, au-dessus des touches, avec les doigts legerement courbes.'),
(2, 1, 'Quel est l''objectif principal de la posture ?', 'Eviter la tension et permettre un mouvement fluide des doigts.'),
(3, 2, 'Combien de touches comprend une octave ?', 'Une octave correspond a 8 notes, de Do a Do.'),
(4, 2, 'Comment reperer les octaves ?', 'En observant les groupes de 2 puis 3 touches noires.'),
(5, 3, 'Quelle est la premiere chose a verifier sur la portee ?', 'La cle utilisee et la position des notes sur les lignes et interlignes.'),
(6, 3, 'Que devez-vous faire en jouant une note ?', 'La jouer avec precision et en gardant une posture stable.'),
(7, 5, 'Quel doigt doit etre place sur la corde la plus grave pour l''accord C ?', 'Le doigt 1 sur la 1re corde, le 2 sur la 2e, le 3 sur la 3e, selon la position choisie.'),
(8, 5, 'Quel est le but du bon placement des doigts ?', 'Produire un son clair et eviter les fausses notes.'),
(9, 7, 'Quel rythme est conseille au debut ?', 'Un rythme simple a 4 temps, regulier et stable.'),
(10, 7, 'Comment garder le tempo ?', 'En comptant clairement 1-2-3-4 a voix haute ou mentalement.'),
(11, 9, 'Pourquoi la posture est-elle importante ?', 'Elle evite la fatigue et ameliore la qualite du son.'),
(12, 9, 'Quelle partie du corps doit rester detendue ?', 'Le cou, les epaules et les bras.'),
(13, 13, 'Que devez-vous travailler en priorite ?', 'La precision des doigts et le controle des notes.'),
(14, 13, 'Comment ameliorer la main gauche ?', 'En faisant des exercices lents et reguliers.'),
(15, 14, 'Que cherchez-vous avec la main droite ?', 'Le mouvement fluide et l''egalite du son.'),
(16, 14, 'Quel est l''objectif du travail au clavier ?', 'Maintenir une sonorite homogene.');

INSERT INTO partitions (id, titre, fichier, cours_id)
VALUES
(1, 'Le Cours des premieres notes', 'partition_piano_01.pdf', 1),
(2, 'Melodie douce au piano', 'partition_piano_02.pdf', 1),
(3, 'Accords faciles', 'partition_guitare_01.pdf', 2),
(4, 'Rythme de base', 'partition_guitare_02.pdf', 2),
(5, 'Premier exercice de violon', 'partition_violon_01.pdf', 3),
(6, 'Archet et sonorite', 'partition_violon_02.pdf', 3);