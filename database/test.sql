-- Utilisation de la base de données
USE doremitendry;

-- Suppression des données existantes pour repartir à zéro (en respectant l'ordre des clés étrangères)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE notifications;
TRUNCATE TABLE messages;
TRUNCATE TABLE certificats;
TRUNCATE TABLE progression;
TRUNCATE TABLE exercices;
TRUNCATE TABLE lecons;
TRUNCATE TABLE modules;
TRUNCATE TABLE partitions;
TRUNCATE TABLE cours;
TRUNCATE TABLE instruments;
TRUNCATE TABLE utilisateurs;
TRUNCATE TABLE badges;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Insertion des UTILISATEURS
-- Mots de passe hachés avec password_hash('Admin123!', PASSWORD_DEFAULT) :
-- $2y$10$O5p38iH2l49KjKstXFp7CObNnS7M8C.D7.b2G4yRAnz8x/Z0OqUqG
-- Mots de passe hachés avec password_hash('apprenant123', PASSWORD_DEFAULT) :
-- $2y$10$eE5UuA1uS7Z0n1oY1iO2Euz39n4tE/fUvU.g/h72C2Q1g1n8T1F3K
INSERT INTO utilisateurs (id, nom, prenom, email, telephone, photo, mot_de_passe, role, date_inscription) VALUES
(1, 'Principal', 'Admin', 'admin@doremitendry.com', '0340012345', NULL, '$2y$10$O5p38iH2l49KjKstXFp7CObNnS7M8C.D7.b2G4yRAnz8x/Z0OqUqG', 'admin', NOW()),
(2, 'Ranaivo', 'Jean', 'jean.ranaivo@gmail.com', '0321122334', NULL, '$2y$10$eE5UuA1uS7Z0n1oY1iO2Euz39n4tE/fUvU.g/h72C2Q1g1n8T1F3K', 'apprenant', NOW() - INTERVAL 15 DAY),
(3, 'Andria', 'Mialy', 'mialy.andria@gmail.com', '0332233445', NULL, '$2y$10$eE5UuA1uS7Z0n1oY1iO2Euz39n4tE/fUvU.g/h72C2Q1g1n8T1F3K', 'apprenant', NOW() - INTERVAL 10 DAY),
(4, 'Razafy', 'Feno', 'feno.razafy@gmail.com', '0344455667', NULL, '$2y$10$eE5UuA1uS7Z0n1oY1iO2Euz39n4tE/fUvU.g/h72C2Q1g1n8T1F3K', 'apprenant', NOW() - INTERVAL 5 DAY);

-- 2. Insertion des INSTRUMENTS
INSERT INTO instruments (id, nom, image, description) VALUES
(1, 'Piano','Le roi des instruments. Apprenez à coordonner vos deux mains et maîtrisez l''harmonie des accords.'),
(2, 'Guitare', 'Polyvalente et chaleureuse. Des premiers accords ouverts au fingerpicking et solos.'),
(3, 'Flûte', 'La pureté du souffle. Apprenez le contrôle du souffle, le doigté et l''interprétation de magnifiques mélodies.');

-- 3. Insertion des COURS
INSERT INTO cours (id, titre, description, image, instrument_id, niveau) VALUES
(1, 'Accords et rythmes - Niveau 1', 'Apprenez les bases fondamentales du piano : la posture, la lecture de notes simple, et vos premiers accords majeurs et mineurs.', 'piano.jpg', 1, 'Débutant'),
(2, 'Rythmes et accompagnement', 'Maîtrisez les accords de base ouverts à la guitare et apprenez vos premières rythmiques pour accompagner des chansons populaires.', 'guitar.jpg', 2, 'Débutant'),
(3, 'Solfège et lecture de notes', 'Ce cours vous guide pas à pas dans la lecture des clés de sol et de fa, et l''application sur la flûte à bec ou traversière.', 'flute.jpg', 3, 'Débutant');

-- 4. nouvelle table modules
-- Insertion du Module 1
INSERT INTO modules (id, titre, image_hero, ordre) 
VALUES (1, 'HISTORIQUE ET INITIATION AU PIANO', 'piano-4.png', 1);

-- 5. Insertion des LEÇONS
INSERT INTO lecons (id, module_id, titre, video, contenu, duree) VALUES
-- Cours Piano
(1, 1, 'Positionnement des mains et posture', 'piano_posture.mp4', 'Asseyez-vous bien droit, les épaules détendues. Vos mains doivent former une arche naturelle, comme si vous teniez une balle de tennis délicatement.', '10 min'),
(2, 1, 'Découverte des notes blanches', 'piano_notes.mp4', 'Le piano est structuré autour d''une alternance de touches blanches et noires. Repérez le Do (C) juste à gauche des deux touches noires.', '15 min'),
(3, 2, 'L''accord de Do Majeur (C)', 'piano_accord_c.mp4', 'L''accord de Do Majeur est composé des notes Do (C), Mi (E) et Sol (G). Jouez-les simultanément avec les doigts 1, 3 et 5 de la main droite.', '12 min'),
(4, 2, 'L''accord de La Mineur (Am)', 'piano_accord_am.mp4', 'Composé de La (A), Do (C) et Mi (E). Observez la différence de sonorité, plus mélancolique que l''accord de Do Majeur.', '10 min'),
(5, 3, 'Indépendance de la main gauche', 'piano_main_gauche.mp4', 'Entraînez-vous à jouer une basse simple (Do en rondes) avec la main gauche pendant que la main droite joue l''accord plaqué.', '20 min'),

-- Cours Guitare
(6, 4, 'Savoir accorder sa guitare', 'guitar_tuning.mp4', 'L''accordage standard de la guitare est Mi, La, Ré, Sol, Si, Mi (E, A, D, G, B, E) de la corde la plus grave à la plus aiguë. Utilisez un accordeur.', '10 min'),
(7, 4, 'L''accord de Mi Mineur (Em) et La Mineur (Am)', 'guitar_accords_easy.mp4', 'Ces deux accords ne nécessitent que peu de doigts. Pratiquez le passage fluide de l''un à l''autre.', '15 min'),
(8, 5, 'Le rythme de feu de camp (Feu de camp strumming)', 'guitar_strumming.mp4', 'Apprenez le mouvement Bas - Bas - Haut - Haut - Bas - Haut. Gardez votre poignet souple comme si vous secouiez de l''eau.', '18 min'),

-- Cours Flûte
(9, 6, 'Maîtriser son souffle', 'flute_breath.mp4', 'Le son de la flûte dépend de la régularité de l''air. Soufflez doucement comme pour faire vaciller la flamme d''une bougie sans l''éteindre.', '10 min'),
(10, 7, 'Les trois premières notes (Si, La, Sol)', 'flute_notes_basic.mp4', 'Bouchez le trou arrière avec le pouce gauche. Bouchez le 1er trou pour le Si, le 2ème pour le La, et le 3ème pour le Sol.', '15 min');

-- 6. Insertion des EXERCICES
INSERT INTO exercices (id, lecon_id, question, correction) VALUES
(1, 2, 'Quelle touche blanche se situe juste à gauche du groupe de deux touches noires ?', 'Le Do (C).'),
(2, 3, 'Quelles sont les trois notes constituant l''accord de Do Majeur ?', 'Do, Mi, Sol (C, E, G).'),
(3, 7, 'Quels doigts de la main gauche utilise-t-on pour jouer l''accord de Mi mineur (Em) ?', 'Le majeur (2ème doigt) et l''annulaire (3ème doigt) sur la 5ème et 4ème corde.'),
(4, 10, 'Quel trou faut-il boucher à l''arrière de la flûte pour obtenir un son stable sur le Si, La ou Sol ?', 'Le trou du pouce gauche (trou 0) doit être entièrement bouché.');

-- 7. Insertion des PARTITIONS
INSERT INTO partitions (id, titre, fichier, cours_id) VALUES
(1, 'Frère Jacques - Partition Facile (Piano)', 'frere_jacques_piano.pdf', 1),
(2, 'Ode à la Joie - Thème de Beethoven (Piano)', 'ode_a_la_joie_piano.pdf', 1),
(3, 'Jeux Interdits - Thème de base (Guitare)', 'jeux_interdits_guitare.pdf', 2),
(4, 'Au Clair de la Lune - Mélodie simple (Flûte)', 'au_clair_de_la_lune_flute.pdf', 3);
