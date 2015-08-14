--
-- Navigation Module PostgreSQL Database for Phire CMS 2.0
--

-- --------------------------------------------------------

--
-- Table structure for table "navigation"
--

CREATE SEQUENCE navigation_id_seq START 7001;

CREATE TABLE IF NOT EXISTS "[{prefix}]navigation" (
  "id" integer NOT NULL DEFAULT nextval('navigation_id_seq'),
  "title" varchar(255) NOT NULL,
  "top_node" varchar(255),
  "top_id" varchar(255),
  "top_class" varchar(255),
  "top_attributes" varchar(255),
  "parent_node" varchar(255),
  "parent_id" varchar(255),
  "parent_class" varchar(255),
  "parent_attributes" varchar(255),
  "child_node" varchar(255),
  "child_id" varchar(255),
  "child_class" varchar(255),
  "child_attributes" varchar(255),
  "on_class" varchar(255),
  "off_class" varchar(255),
  "indent" varchar(255),
  "tree" text,
  PRIMARY KEY ("id")
) ;

ALTER SEQUENCE navigation_id_seq OWNED BY "[{prefix}]navigation"."id";
CREATE INDEX "navigation_title" ON "[{prefix}]navigation" ("title");
