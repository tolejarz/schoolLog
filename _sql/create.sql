-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 06 Avril 2009 à 22:28
-- Version du serveur: 5.0.77
-- Version de PHP: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


--
-- Base de données: `schoollog`
--
DROP DATABASE IF EXISTS `schoollog`;
CREATE DATABASE `schoollog` DEFAULT CHARACTER SET utf8;
USE `schoollog`;
-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `eleves_classes`
--

DROP TABLE IF EXISTS `eleves_classes`;
CREATE TABLE IF NOT EXISTS `eleves_classes` (
  `id_eleve` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  PRIMARY KEY  (`id_eleve`,`id_classe`),
  KEY `eleves_matieres_ibfk_classe` (`id_classe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants_matieres_classes`
--

CREATE TABLE IF NOT EXISTS `enseignants_matieres_classes` (
  `id_enseignant` int(11) default NULL,
  `id_matiere` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  KEY `enseignants_matieres_classes_ibfk_classe` (`id_classe`),
  KEY `enseignants_matieres_classes_ibfk_matiere` (`id_matiere`),
  KEY `enseignants_matieres_classes_ibfk_enseignant` (`id_enseignant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `materiels`
--

DROP TABLE IF EXISTS `materiels`;
CREATE TABLE IF NOT EXISTS `materiels` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(64) NOT NULL,
  `modele` varchar(128) NOT NULL,
  `etat` enum('fonctionnel','en maintenance') NOT NULL default 'fonctionnel',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
CREATE TABLE IF NOT EXISTS `matieres` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `periodes`
--

DROP TABLE IF EXISTS `periodes`;
CREATE TABLE IF NOT EXISTS `periodes` (
  `id` int(11) NOT NULL auto_increment,
  `type` enum('cours','vacances','partiels') collate latin1_general_ci NOT NULL,
  `date_debut` date default NULL,
  `date_fin` date default NULL,
  `id_classe` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `modele_planning`
--

DROP TABLE IF EXISTS `modele_planning`;
CREATE TABLE IF NOT EXISTS `modele_planning` (
  `id` int(11) NOT NULL auto_increment,
  `jour` enum('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi') collate latin1_general_ci NOT NULL,
  `heure_debut` time default NULL,
  `heure_fin` time default NULL,
  `id_matiere` int(11) NOT NULL,
  `id_enseignant` int(11) default NULL,
  `id_classe` int(11) NOT NULL,
  `id_periode` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `modele_planning_ibfk_classe` (`id_classe`),
  KEY `modele_planning_ibfk_matiere` (`id_matiere`),
  KEY `modele_planning_ibfk_enseignant` (`id_enseignant`),
  KEY `modele_planning_ibfk_periode` (`id_periode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `operations`
--

DROP TABLE IF EXISTS `operations`;
CREATE TABLE IF NOT EXISTS `operations` (
  `id` int(11) NOT NULL auto_increment,
  `date_creation` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `date_origine` date default NULL,
  `date_report` datetime default NULL,
  `etat` enum('en attente','validée','refusée') NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_modele_planning` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `operations_ibfk_modele_planning` (`id_modele_planning`),
  KEY `operations_ibfk_enseignant` (`id_enseignant`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int(11) NOT NULL auto_increment,
  `date_creation` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `date_heure_debut` datetime NOT NULL,
  `date_heure_fin` datetime NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_materiel` int(11) NOT NULL,
  `etat` enum('en attente','validée') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `reservations_ibfk_materiel` (`id_materiel`),
  KEY `reservations_ibfk_enseignant` (`id_enseignant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `supports`
--

DROP TABLE IF EXISTS `supports`;
CREATE TABLE IF NOT EXISTS `supports` (
  `id` int(11) NOT NULL auto_increment,
  `date_creation` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `titre` varchar(256) NOT NULL,
  `nom_fichier` varchar(256) NOT NULL,
  `tags` varchar(256) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `supports_ibfk_enseignant` (`id_enseignant`),
  KEY `supports_ibfk_matiere` (`id_matiere`),
  KEY `supports_ibfk_classe` (`id_classe`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(64) NOT NULL,
  `civility` enum('M.','Mme','Mlle') NULL,
  `nom` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `droits` enum('eleve','enseignant','superviseur','administrateur') collate latin1_general_ci NOT NULL,
  `derniere_connexion` timestamp NULL default NULL,
  `charte_signee` tinyint(1) NOT NULL default 0,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `eleves_classes`
--
ALTER TABLE `eleves_classes`
  ADD CONSTRAINT `eleves_classes_ibfk_classe` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eleves_classes_ibfk_eleve` FOREIGN KEY (`id_eleve`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `enseignants_matieres_classes`
--
ALTER TABLE `enseignants_matieres_classes`
  ADD CONSTRAINT `enseignants_matieres_classes_ibfk_classe` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enseignants_matieres_classes_ibfk_enseignant` FOREIGN KEY (`id_enseignant`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enseignants_matieres_classes_ibfk_matiere` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `modele_planning`
--
ALTER TABLE `modele_planning`
  ADD CONSTRAINT `modele_planning_ibfk_classe` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `modele_planning_ibfk_enseignant` FOREIGN KEY (`id_enseignant`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `modele_planning_ibfk_matiere` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `modele_planning_ibfk_periode` FOREIGN KEY (`id_periode`) REFERENCES `periodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `operations`
--
ALTER TABLE `operations`
  ADD CONSTRAINT `operations_ibfk_enseignant` FOREIGN KEY (`id_enseignant`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `operations_ibfk_modele_planning` FOREIGN KEY (`id_modele_planning`) REFERENCES `modele_planning` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_enseignant` FOREIGN KEY (`id_enseignant`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_materiel` FOREIGN KEY (`id_materiel`) REFERENCES `materiels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `supports`
--
ALTER TABLE `supports`
  ADD CONSTRAINT `supports_ibfk_classe` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supports_ibfk_enseignant` FOREIGN KEY (`id_enseignant`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supports_ibfk_matiere` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
