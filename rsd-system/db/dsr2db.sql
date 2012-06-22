-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 22, 2012 at 05:36 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rsddb`
--

-- --------------------------------------------------------

--
-- Table structure for table `rsd_acl_roles`
--

DROP TABLE IF EXISTS `rsd_acl_roles`;
CREATE TABLE IF NOT EXISTS `rsd_acl_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rsd_acl_roles`
--

INSERT INTO `rsd_acl_roles` (`id`, `name`, `description`) VALUES
(1, 'role1', '1111 11 1 1 1 11 111  1  1 1'),
(2, 'role2', '111222 1 1 1 11 111  1  1 1'),
(3, 'role3', 'three 3333');

-- --------------------------------------------------------

--
-- Table structure for table `rsd_acl_role_user`
--

DROP TABLE IF EXISTS `rsd_acl_role_user`;
CREATE TABLE IF NOT EXISTS `rsd_acl_role_user` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission` tinyint(4) NOT NULL DEFAULT '0',
  UNIQUE KEY `role_id` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rsd_acl_role_user`
--

INSERT INTO `rsd_acl_role_user` (`role_id`, `user_id`, `permission`) VALUES
(1, 1, 0),
(1, 2, 0),
(1, 3, 1),
(2, 1, 0),
(2, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rsd_projects`
--

DROP TABLE IF EXISTS `rsd_projects`;
CREATE TABLE IF NOT EXISTS `rsd_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `rsd_projects`
--

INSERT INTO `rsd_projects` (`id`, `name`, `description`) VALUES
(1, 'DTV', 'Testing for DTV'),
(2, 'DSR 2.0', 'Development DSR tool.');

-- --------------------------------------------------------

--
-- Table structure for table `rsd_teams`
--

DROP TABLE IF EXISTS `rsd_teams`;
CREATE TABLE IF NOT EXISTS `rsd_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `rsd_teams`
--

INSERT INTO `rsd_teams` (`id`, `name`, `description`, `project_id`) VALUES
(1, 'HR2X', 'HR2X platform', 2),
(2, 'HR34', 'HR34 platform', 1),
(3, 'HOS', 'Hospital ', 1),
(4, 'RnD', 'RnD team', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rsd_team_user`
--

DROP TABLE IF EXISTS `rsd_team_user`;
CREATE TABLE IF NOT EXISTS `rsd_team_user` (
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `team_id` (`team_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rsd_team_user`
--

INSERT INTO `rsd_team_user` (`team_id`, `user_id`) VALUES
(1, 2),
(1, 3),
(2, 1),
(4, 1),
(4, 2),
(4, 3),
(4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `rsd_users`
--

DROP TABLE IF EXISTS `rsd_users`;
CREATE TABLE IF NOT EXISTS `rsd_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `password` char(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `rsd_users`
--

INSERT INTO `rsd_users` (`id`, `user_id`, `password`) VALUES
(1, 'gcs_an', 'c0e855b9be0718123e680f4c032fc038'),
(2, 'gcs_ha', 'c0e855b9be0718123e680f4c032fc038'),
(3, 'gcs_thanhm', 'c0e855b9be0718123e680f4c032fc038'),
(4, 'admin', 'c0e855b9be0718123e680f4c032fc038');

-- --------------------------------------------------------

--
-- Table structure for table `rsd_user_info`
--

DROP TABLE IF EXISTS `rsd_user_info`;
CREATE TABLE IF NOT EXISTS `rsd_user_info` (
  `id` int(11) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `birthday` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rsd_user_info`
--

INSERT INTO `rsd_user_info` (`id`, `first_name`, `last_name`, `short_name`, `email`, `note`, `birthday`, `location`, `extension`, `mobile`) VALUES
(1, 'Vo Nguyen', 'Thien An', 'An Vo', 'heosuax@gmail.com', 'Nothing to note', '1988-05-22', '2B.2', '0', '01674560306');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
