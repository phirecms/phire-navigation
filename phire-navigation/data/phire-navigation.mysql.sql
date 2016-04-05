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
  PRIMARY KEY (`id`),
  INDEX `navigation_title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7001;

-- --------------------------------------------------------

--
-- Table structure for table `navigation_items`
--

CREATE TABLE IF NOT EXISTS `[{prefix}]navigation_items` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `navigation_id` int(16) NOT NULL,
  `parent_id` int(16),
  `item_id` int(16),
  `type` varchar(255),
  `name` text,
  `href` text,
  `attributes` text,
  `order` int(16),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_navigation` FOREIGN KEY (`navigation_id`) REFERENCES `[{prefix}]navigation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nav_item_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `[{prefix}]navigation_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8001;

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 1;