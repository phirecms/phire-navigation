--
-- Navigation Module SQLite Database for Phire CMS 2.0
--

--  --------------------------------------------------------

--
-- Set database encoding
--

PRAGMA encoding = "UTF-8";
PRAGMA foreign_keys = ON;

-- --------------------------------------------------------

--
-- Table structure for table "navigation"
--

CREATE TABLE IF NOT EXISTS "[{prefix}]navigation" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "title" varchar NOT NULL,
  "top_node" varchar,
  "top_id" varchar,
  "top_class" varchar,
  "top_attributes" varchar,
  "parent_node" varchar,
  "parent_id" varchar,
  "parent_class" varchar,
  "parent_attributes" varchar,
  "child_node" varchar,
  "child_id" varchar,
  "child_class" varchar,
  "child_attributes" varchar,
  "on_class" varchar,
  "off_class" varchar,
  "indent" varchar,
  "tree" text,
  UNIQUE ("id")
) ;

INSERT INTO "sqlite_sequence" ("name", "seq") VALUES ('[{prefix}]navigation', 7000);
CREATE INDEX "navigation_title" ON "[{prefix}]navigation" ("title");
