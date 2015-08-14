--
-- Navigation Module MySQL Database for Phire CMS 2.0
--

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------

--
-- Table structure for table `navigation`
--

CREATE TABLE IF NOT EXISTS `[{prefix}]navigation` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `top_node` varchar(255),
  `top_id` varchar(255),
  `top_class` varchar(255),
  `top_attributes` varchar(255),
  `parent_node` varchar(255),
  `parent_id` varchar(255),
  `parent_class` varchar(255),
  `parent_attributes` varchar(255),
  `child_node` varchar(255),
  `child_id` varchar(255),
  `child_class` varchar(255),
  `child_attributes` varchar(255),
  `on_class` varchar(255),
  `off_class` varchar(255),
  `indent` varchar(255),
  `tree` text,
  PRIMARY KEY (`id`),
  INDEX `navigation_title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7001;

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 1;
