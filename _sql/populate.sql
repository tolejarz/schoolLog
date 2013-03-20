-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 06 Avril 2009 à 22:29
-- Version du serveur: 5.0.77
-- Version de PHP: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


--
-- Base de données: `schoollog`
--

--
-- Contenu de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(1, 'user01', 'user01', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(2, 'user02', 'user02', 'Mme', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(3, 'user03', 'user03', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(4, 'user04', 'user04', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(5, 'user05', 'user05', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(6, 'user06', 'user06', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(7, 'user07', 'user07', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(8, 'user08', 'user08', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(9, 'user09', 'user09', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(10, 'user10', 'user10', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(11, 'user11', 'user11', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(12, 'user12', 'user12', 'Mme', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(13, 'user13', 'user13', 'Mme', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(14, 'user14', 'user14', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(15, 'user15', 'user15', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(16, 'user16', 'user16', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(17, 'testEnseignant', 'enseignant', 'M.', 'fake@dummy.fr', 'enseignant');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `civility`, `email`, `droits`) VALUES(18, 'testSuperviseur', 'superviseur', 'M.', 'fake@dummy.fr', 'superviseur');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `email`, `droits`) VALUES(19, 'adminschoollog', 'Administrateur', 'fake@dummy.fr', 'administrateur');
INSERT INTO `utilisateurs` (`id`, `login`, `nom`, `email`, `droits`) VALUES(20, 'testEleve', 'Eleve', 'fake@dummy.fr', 'eleve');

--
-- Contenu de la table `classes`
--

INSERT INTO `classes` (`id`, `libelle`, `email`) VALUES(1, 'BTS / CPI 1', 'fake@dummy.fr');
INSERT INTO `classes` (`id`, `libelle`, `email`) VALUES(2, 'BTS / CPI 2', 'fake@dummy.fr');
INSERT INTO `classes` (`id`, `libelle`, `email`) VALUES(3, 'CSII1', 'fake@dummy.fr');
INSERT INTO `classes` (`id`, `libelle`, `email`) VALUES(4, 'CSII2', 'fake@dummy.fr');
INSERT INTO `classes` (`id`, `libelle`, `email`) VALUES(5, 'CSII3Rsx', 'fake@dummy.fr');
INSERT INTO `classes` (`id`, `libelle`, `email`) VALUES(6, 'CSII3GL', 'fake@dummy.fr');

--
-- Contenu de la table `eleves_classes`
--

INSERT INTO `eleves_classes` (`id_eleve`, `id_classe`) VALUES(20, 1);

--
-- Contenu de la table `periodes`
--

INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(1, 'cours', '2012-10-06', '2012-12-19', 4);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(2, 'cours', '2013-01-05', '2013-05-29', 4);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(3, 'vacances', '2012-12-20', '2013-01-05', 4);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(4, 'vacances', '2013-04-11', '2013-04-19', 4);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(5, 'cours', '2013-01-05', '2013-05-29', 3);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(6, 'vacances', '2013-04-11', '2013-04-19', 3);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(7, 'cours', '2013-01-05', '2013-05-29', 1);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(8, 'vacances', '2013-04-11', '2013-04-19', 1);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(9, 'cours', '2013-01-05', '2013-05-29', 2);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(10, 'vacances', '2013-04-11', '2013-04-19', 2);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(11, 'cours', '2013-01-05', '2013-05-29', 5);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(12, 'vacances', '2013-04-11', '2013-04-19', 5);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(13, 'cours', '2013-01-05', '2013-05-29', 6);
INSERT INTO `periodes` (`id`, `type`, `date_debut`, `date_fin`, `id_classe`) VALUES(14, 'vacances', '2013-04-11', '2013-04-19', 6);
--
-- Contenu de la table `materiels`
--

INSERT INTO `materiels` (`id`, `type`, `modele`, `etat`) VALUES(1, 'Rétroprojecteur', 'Sony PK5', 'fonctionnel');
INSERT INTO `materiels` (`id`, `type`, `modele`, `etat`) VALUES(2, 'Rétroprojecteur', 'EPSON L-45', 'fonctionnel');
INSERT INTO `materiels` (`id`, `type`, `modele`, `etat`) VALUES(3, 'Télévision', 'Philips RJ45', 'fonctionnel');
INSERT INTO `materiels` (`id`, `type`, `modele`, `etat`) VALUES(4, 'Caméra', 'Hitachi XW23-C4', 'en maintenance');

--
-- Contenu de la table `matieres`
--

INSERT INTO `matieres` (`id`, `nom`) VALUES(1, 'TEC');
INSERT INTO `matieres` (`id`, `nom`) VALUES(2, 'Langage C++');
INSERT INTO `matieres` (`id`, `nom`) VALUES(3, 'Langage C#');
INSERT INTO `matieres` (`id`, `nom`) VALUES(4, 'Langage C');
INSERT INTO `matieres` (`id`, `nom`) VALUES(5, 'Maths');
INSERT INTO `matieres` (`id`, `nom`) VALUES(6, 'Bases de données');
INSERT INTO `matieres` (`id`, `nom`) VALUES(7, 'Prog. système & réseau');
INSERT INTO `matieres` (`id`, `nom`) VALUES(8, 'Intelligence artificielle');
INSERT INTO `matieres` (`id`, `nom`) VALUES(9, 'Génie logiciel');
INSERT INTO `matieres` (`id`, `nom`) VALUES(10, 'Finance');
INSERT INTO `matieres` (`id`, `nom`) VALUES(11, 'Java');
INSERT INTO `matieres` (`id`, `nom`) VALUES(12, 'Réseaux');
INSERT INTO `matieres` (`id`, `nom`) VALUES(13, 'Anglais');
INSERT INTO `matieres` (`id`, `nom`) VALUES(14, 'Système');
INSERT INTO `matieres` (`id`, `nom`) VALUES(15, 'Système Unix & prog. shell');
INSERT INTO `matieres` (`id`, `nom`) VALUES(16, 'Economie');
INSERT INTO `matieres` (`id`, `nom`) VALUES(17, 'Analyse & conception des SI');

--
-- Contenu de la table `eleves_classes`
--

--
-- Contenu de la table `enseignants_matieres_classes`
--

INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(17, 5, 1);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(17, 1, 1);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, 13, 1);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(7, 5, 2);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, 1, 2);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, 13, 2);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(12, 10, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, 13, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(13, 5, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, 1, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(6, 12, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(5, 6, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(1, 2, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(16, 15, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(7, 5, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(16, 11, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(4, 17, 3);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(4, 11, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(5, 6, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(6, 12, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, 13, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, 1, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(1, 3, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(5, 7, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(7, 5, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(9, 9, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(8, 8, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(3, 11, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(10, 9, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(11, 10, 4);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(7, 5, 5);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, 1, 5);
INSERT INTO `enseignants_matieres_classes` (`id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, 13, 5);

--
-- Contenu de la table `modele_planning`
--

INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(1, 'lundi', '13:30:00', '17:30:00', 11, 3, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(2, 'lundi', '17:30:00', '19:30:00', 6, 5, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(3, 'mardi', '13:30:00', '15:30:00', 12, 6, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(4, 'mardi', '15:30:00', '17:00:00', 13, 2, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(5, 'mardi', '17:00:00', '18:30:00', 1, 2, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(6, 'mercredi', '08:30:00', '12:30:00', 3, 1, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(7, 'mercredi', '13:30:00', '17:30:00', 5, 7, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(8, 'mercredi', '17:30:00', '19:30:00', 7, 5, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(9, 'jeudi', '08:30:00', '12:30:00', 9, 9, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(10, 'jeudi', '13:30:00', '17:30:00', 8, 8, 4, 1);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(11, 'lundi', '13:30:00', '15:30:00', 6, 5, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(12, 'lundi', '15:30:00', '17:30:00', 7, 5, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(13, 'mardi', '13:30:00', '15:30:00', 12, 6, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(14, 'mardi', '15:30:00', '17:00:00', 13, 2, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(15, 'mardi', '17:00:00', '18:30:00', 1, 2, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(16, 'mercredi', '10:30:00', '12:30:00', 5, 7, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(17, 'mercredi', '13:30:00', '15:30:00', 5, 7, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(18, 'mercredi', '15:30:00', '17:30:00', 14, NULL, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(19, 'jeudi', '08:30:00', '12:30:00', 9, 10, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(20, 'jeudi', '13:30:00', '17:30:00', 8, 8, 4, 2);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(21, 'lundi', '13:30:00', '15:30:00', 10, 12, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(22, 'lundi', '15:30:00', '17:30:00', 13, 2, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(23, 'lundi', '17:30:00', '19:30:00', 5, 13, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(24, 'mardi', '13:30:00', '15:30:00', 1, 2, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(25, 'mardi', '15:30:00', '17:30:00', 12, 6, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(26, 'mardi', '17:30:00', '19:30:00', 6, 5, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(27, 'mercredi', '13:30:00', '15:30:00', 2, 1, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(28, 'mercredi', '15:30:00', '17:30:00', 15, 16, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(29, 'mercredi', '17:30:00', '19:30:00', 5, 7, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(30, 'jeudi', '08:30:00', '12:30:00', 11, 16, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(31, 'vendredi', '08:30:00', '12:30:00', 17, 4, 3, 5);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(32, 'lundi', '10:30:00', '12:30:00', 1, 17, 1, 7);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(33, 'mardi', '08:30:00', '10:30:00', 13, 2, 1, 7);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(34, 'jeudi', '13:30:00', '15:30:00', 5, 17, 1, 7);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(35, 'jeudi', '13:30:00', '15:30:00', 1, 2, 2, 9);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(36, 'vendredi', '15:30:00', '17:30:00', 13, 2, 2, 9);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(37, 'vendredi', '08:30:00', '10:30:00', 5, 7, 2, 9);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(38, 'lundi', '10:30:00', '12:30:00', 5, 7, 5, 11);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(39, 'lundi', '08:30:00', '10:30:00', 1, 2, 5, 11);
INSERT INTO `modele_planning` (`id`, `jour`, `heure_debut`, `heure_fin`, `id_matiere`, `id_enseignant`, `id_classe`, `id_periode`) VALUES(40, 'lundi', '13:30:00', '15:30:00', 13, 2, 5, 11);

--
-- Contenu de la table `operations`
--

INSERT INTO `operations` (`id`, `date_creation`, `date_origine`, `date_report`, `etat`, `id_enseignant`, `id_modele_planning`) VALUES(1, '2009-04-05 19:14:03', '2009-04-20', '2009-04-22 18:30:00', 'validée', 5, 11);
INSERT INTO `operations` (`id`, `date_creation`, `date_origine`, `date_report`, `etat`, `id_enseignant`, `id_modele_planning`) VALUES(2, '2009-04-24 22:03:57', '2009-04-21', '2009-04-21 18:30:00', 'refusée', 2, 14);
INSERT INTO `operations` (`id`, `date_creation`, `date_origine`, `date_report`, `etat`, `id_enseignant`, `id_modele_planning`) VALUES(3, '2009-05-02 08:35:58', '2009-05-06', '2009-05-07 18:30:00', 'validée', 7, 17);
INSERT INTO `operations` (`id`, `date_creation`, `date_origine`, `date_report`, `etat`, `id_enseignant`, `id_modele_planning`) VALUES(4, '2009-05-02 09:17:51', '2009-05-07', '2009-05-08 16:30:00', 'en attente', 17, 34);
INSERT INTO `operations` (`id`, `date_creation`, `date_origine`, `date_report`, `etat`, `id_enseignant`, `id_modele_planning`) VALUES(5, '2009-05-03 19:39:57', '2009-05-05', '2009-05-08 18:00:00', 'en attente', 2, 14);
INSERT INTO `operations` (`id`, `date_creation`, `date_origine`, `date_report`, `etat`, `id_enseignant`, `id_modele_planning`) VALUES(6, '2009-05-05 02:12:13', '2009-05-08', NULL, 'en attente', 17, 31);

--
-- Contenu de la table `reservations`
--

INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(1, '2009-04-25 10:09:31', '2009-04-27 11:30:00', '2009-04-27 12:30:00', 9, 4, 'validée');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(2, '2009-04-25 11:39:20', '2009-04-29 13:30:00', '2009-04-29 14:30:00', 2, 4, 'validée');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(3, '2009-04-25 14:51:25', '2009-04-28 08:30:00', '2009-04-28 10:30:00', 5, 4, 'validée');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(4, '2009-04-25 14:52:55', '2009-05-01 08:30:00', '2009-05-01 10:30:00', 9, 4, 'validée');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(5, '2009-04-25 14:58:26', '2009-04-28 13:30:00', '2009-04-28 17:30:00', 9, 4, 'validée');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(6, '2009-04-25 14:58:52', '2009-04-30 08:30:00', '2009-04-30 12:30:00', 9, 4, 'validée');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(7, '2009-05-01 16:26:21', '2009-05-01 17:30:00', '2009-05-01 18:30:00', 5, 4, 'validée');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(8, '2009-05-03 19:42:45', '2009-05-03 08:30:00', '2009-05-03 14:30:00', 2, 2, 'en attente');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(9, '2009-05-03 19:59:46', '2009-05-04 08:30:00', '2009-05-04 11:30:00', 2, 1, 'en attente');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(10, '2009-05-03 20:01:37', '2009-05-04 08:30:00', '2009-05-04 10:30:00', 2, 2, 'en attente');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(11, '2009-05-03 20:05:00', '2009-05-07 08:30:00', '2009-05-07 11:30:00', 6, 3, 'validée');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(12, '2009-05-03 20:10:53', '2009-05-06 08:30:00', '2009-05-06 10:30:00', 5, 3, 'validée');
INSERT INTO `reservations` (`id`, `date_creation`, `date_heure_debut`, `date_heure_fin`, `id_enseignant`, `id_materiel`, `etat`) VALUES(13, '2009-05-05 00:50:52', '2009-05-07 08:30:00', '2009-05-07 13:30:00', 5, 1, 'en attente');

--
-- Contenu de la table `supports`
--

INSERT INTO `supports` (`id`, `date_creation`, `titre`, `nom_fichier`, `tags`, `id_enseignant`, `id_matiere`, `id_classe`) VALUES(1, '2009-04-05 12:51:16', 'Les bases', '20081403_les_bases_de_cplusplus.doc', 'bases;c++;cplusplus', 1, 2, 3);
INSERT INTO `supports` (`id`, `date_creation`, `titre`, `nom_fichier`, `tags`, `id_enseignant`, `id_matiere`, `id_classe`) VALUES(2, '2009-04-05 12:51:16', 'Les templates', '20081503_les_templates_cplusplus.doc', 'templates;c++;cplusplus', 1, 2, 3);
INSERT INTO `supports` (`id`, `date_creation`, `titre`, `nom_fichier`, `tags`, `id_enseignant`, `id_matiere`, `id_classe`) VALUES(3, '2009-04-05 12:51:16', 'Les collections', '20081603_les_collections_csharp.pdf', 'collections;c#', 1, 3, 4);
INSERT INTO `supports` (`id`, `date_creation`, `titre`, `nom_fichier`, `tags`, `id_enseignant`, `id_matiere`, `id_classe`) VALUES(4, '2009-04-05 23:36:59', 'sdf', '1238967419sdf.pdf', 'sdf', 1, 3, 4);
INSERT INTO `supports` (`id`, `date_creation`, `titre`, `nom_fichier`, `tags`, `id_enseignant`, `id_matiere`, `id_classe`) VALUES(5, '2009-04-25 15:19:05', 'Cours', '20090425_151932_cours.doc', '', 1, 4, 3);
INSERT INTO `supports` (`id`, `date_creation`, `titre`, `nom_fichier`, `tags`, `id_enseignant`, `id_matiere`, `id_classe`) VALUES(6, '2009-04-25 15:19:50', 'Cours', '20090425_152017_cours.doc', '', 1, 2, 3);
INSERT INTO `supports` (`id`, `date_creation`, `titre`, `nom_fichier`, `tags`, `id_enseignant`, `id_matiere`, `id_classe`) VALUES(7, '2009-04-25 15:20:41', 'Cours', '20090425_152108_cours.doc', '', 9, 9, 4);
