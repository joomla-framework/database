--
-- Table: assets
--
DROP TABLE IF EXISTS "assets" CASCADE;
CREATE TABLE "assets" (
  -- Primary Key
  "id" serial NOT NULL,
  -- Nested set parent.
  "parent_id" bigint DEFAULT 0 NOT NULL,
  -- Nested set lft.
  "lft" bigint DEFAULT 0 NOT NULL,
  -- Nested set rgt.
  "rgt" bigint DEFAULT 0 NOT NULL,
  -- The cached level in the nested tree.
  "level" integer NOT NULL,
  -- The unique name for the asset.\n
  "name" character varying(50) NOT NULL,
  -- The descriptive title for the asset.
  "title" character varying(100) NOT NULL,
  -- JSON encoded access control.
  "rules" character varying(5120) NOT NULL,
  PRIMARY KEY ("id"),
  CONSTRAINT "idx_asset_name" UNIQUE ("name")
);
CREATE INDEX "assets_idx_lft_rgt" on "assets" ("lft", "rgt");
CREATE INDEX "assets_idx_parent_id" on "assets" ("parent_id");

COMMENT ON COLUMN "assets"."id" IS 'Primary Key';
COMMENT ON COLUMN "assets"."parent_id" IS 'Nested set parent.';
COMMENT ON COLUMN "assets"."lft" IS 'Nested set lft.';
COMMENT ON COLUMN "assets"."rgt" IS 'Nested set rgt.';
COMMENT ON COLUMN "assets"."level" IS 'The cached level in the nested tree.';
COMMENT ON COLUMN "assets"."name" IS 'The unique name for the asset.\n';
COMMENT ON COLUMN "assets"."title" IS 'The descriptive title for the asset.';
COMMENT ON COLUMN "assets"."rules" IS 'JSON encoded access control.';

--
-- Table: categories
--
DROP TABLE IF EXISTS "categories" CASCADE;
CREATE TABLE "categories" (
  "id" serial NOT NULL,
  -- FK to the #__assets table.
  "asset_id" integer DEFAULT 0 NOT NULL,
  "parent_id" integer DEFAULT 0 NOT NULL,
  "lft" bigint DEFAULT 0 NOT NULL,
  "rgt" bigint DEFAULT 0 NOT NULL,
  "level" integer DEFAULT 0 NOT NULL,
  "path" character varying(255) DEFAULT '' NOT NULL,
  "extension" character varying(50) DEFAULT '' NOT NULL,
  "title" character varying(255) NOT NULL,
  "alias" character varying(255) DEFAULT '' NOT NULL,
  "note" character varying(255) DEFAULT '' NOT NULL,
  "description" character varying(5120) DEFAULT '' NOT NULL,
  "published" smallint DEFAULT 0 NOT NULL,
  "checked_out" bigint DEFAULT 0 NOT NULL,
  "checked_out_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "access" smallint DEFAULT 0 NOT NULL,
  "params" text NOT NULL,
  -- The meta description for the page.
  "metadesc" character varying(1024) NOT NULL,
  -- The meta keywords for the page.
  "metakey" character varying(1024) NOT NULL,
  -- JSON encoded metadata properties.
  "metadata" character varying(2048) NOT NULL,
  "created_user_id" integer DEFAULT 0 NOT NULL,
  "created_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "modified_user_id" integer DEFAULT 0 NOT NULL,
  "modified_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "hits" integer DEFAULT 0 NOT NULL,
  "language" character(7) NOT NULL,
  PRIMARY KEY ("id")
);
CREATE INDEX "categories_cat_idx" on "categories" ("extension", "published", "access");
CREATE INDEX "categories_idx_access" on "categories" ("access");
CREATE INDEX "categories_idx_checkout" on "categories" ("checked_out");
CREATE INDEX "categories_idx_path" on "categories" ("path");
CREATE INDEX "categories_idx_left_right" on "categories" ("lft", "rgt");
CREATE INDEX "categories_idx_alias" on "categories" ("alias");
CREATE INDEX "categories_idx_language" on "categories" ("language");

COMMENT ON COLUMN "categories"."asset_id" IS 'FK to the #__assets table.';
COMMENT ON COLUMN "categories"."metadesc" IS 'The meta description for the page.';
COMMENT ON COLUMN "categories"."metakey" IS 'The meta keywords for the page.';
COMMENT ON COLUMN "categories"."metadata" IS 'JSON encoded metadata properties.';

--
-- Table: content
--
DROP TABLE IF EXISTS "content" CASCADE;
CREATE TABLE "content" (
  "id" serial NOT NULL,
  -- FK to the #__assets table.
  "asset_id" integer DEFAULT 0 NOT NULL,
  "title" character varying(255) DEFAULT '' NOT NULL,
  "alias" character varying(255) DEFAULT '' NOT NULL,
  "title_alias" character varying(255) DEFAULT '' NOT NULL,
  "introtext" text NOT NULL,
  "fulltext" text NOT NULL,
  "state" smallint DEFAULT 0 NOT NULL,
  "sectionid" integer DEFAULT 0 NOT NULL,
  "mask" integer DEFAULT 0 NOT NULL,
  "catid" integer DEFAULT 0 NOT NULL,
  "created" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "created_by" integer DEFAULT 0 NOT NULL,
  "created_by_alias" character varying(255) DEFAULT '' NOT NULL,
  "modified" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "modified_by" integer DEFAULT 0 NOT NULL,
  "checked_out" integer DEFAULT 0 NOT NULL,
  "checked_out_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "publish_up" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "publish_down" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "images" text NOT NULL,
  "urls" text NOT NULL,
  "attribs" character varying(5120) NOT NULL,
  "version" integer DEFAULT 1 NOT NULL,
  "parentid" integer DEFAULT 0 NOT NULL,
  "ordering" bigint DEFAULT 0 NOT NULL,
  "metakey" text NOT NULL,
  "metadesc" text NOT NULL,
  "access" integer DEFAULT 0 NOT NULL,
  "hits" integer DEFAULT 0 NOT NULL,
  "metadata" text NOT NULL,
  -- Set if article is featured.
  "featured" smallint DEFAULT 0 NOT NULL,
  -- The language code for the article.
  "language" character(7) NOT NULL,
  -- A reference to enable linkages to external data sets.
  "xreference" character varying(50) NOT NULL,
  PRIMARY KEY ("id")
);
CREATE INDEX "content_idx_access" on "content" ("access");
CREATE INDEX "content_idx_checkout" on "content" ("checked_out");
CREATE INDEX "content_idx_state" on "content" ("state");
CREATE INDEX "content_idx_catid" on "content" ("catid");
CREATE INDEX "content_idx_createdby" on "content" ("created_by");
CREATE INDEX "content_idx_featured_catid" on "content" ("featured", "catid");
CREATE INDEX "content_idx_language" on "content" ("language");
CREATE INDEX "content_idx_xreference" on "content" ("xreference");

COMMENT ON COLUMN "content"."asset_id" IS 'FK to the #__assets table.';
COMMENT ON COLUMN "content"."featured" IS 'Set if article is featured.';
COMMENT ON COLUMN "content"."language" IS 'The language code for the article.';
COMMENT ON COLUMN "content"."xreference" IS 'A reference to enable linkages to external data sets.';

--
-- Table: core_log_searches
--
DROP TABLE IF EXISTS "core_log_searches" CASCADE;
CREATE TABLE "core_log_searches" (
  "search_term" character varying(128) DEFAULT '' NOT NULL,
  "hits" integer DEFAULT 0 NOT NULL
);

--
-- Table: extensions
--
DROP TABLE IF EXISTS "extensions" CASCADE;
CREATE TABLE "extensions" (
  "extension_id" serial NOT NULL,
  "name" character varying(100) NOT NULL,
  "type" character varying(20) NOT NULL,
  "element" character varying(100) NOT NULL,
  "folder" character varying(100) NOT NULL,
  "client_id" smallint NOT NULL,
  "enabled" smallint DEFAULT 1 NOT NULL,
  "access" smallint DEFAULT 1 NOT NULL,
  "protected" smallint DEFAULT 0 NOT NULL,
  "manifest_cache" text NOT NULL,
  "params" text NOT NULL,
  "custom_data" text NOT NULL,
  "system_data" text NOT NULL,
  "checked_out" integer DEFAULT 0 NOT NULL,
  "checked_out_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "ordering" bigint DEFAULT 0,
  "state" bigint DEFAULT 0,
  PRIMARY KEY ("extension_id")
);
CREATE INDEX "extensions_element_clientid" on "extensions" ("element", "client_id");
CREATE INDEX "extensions_element_folder_clientid" on "extensions" ("element", "folder", "client_id");
CREATE INDEX "extensions_extension" on "extensions" ("type", "element", "folder", "client_id");

--
-- Table: languages
--
DROP TABLE IF EXISTS "languages" CASCADE;
CREATE TABLE "languages" (
  "lang_id" serial NOT NULL,
  "lang_code" character(7) NOT NULL,
  "title" character varying(50) NOT NULL,
  "title_native" character varying(50) NOT NULL,
  "sef" character varying(50) NOT NULL,
  "image" character varying(50) NOT NULL,
  "description" character varying(512) NOT NULL,
  "metakey" text NOT NULL,
  "metadesc" text NOT NULL,
  "published" bigint DEFAULT 0 NOT NULL,
  PRIMARY KEY ("lang_id"),
  CONSTRAINT "idx_sef" UNIQUE ("sef")
);

--
-- Table: log_entries
--
DROP TABLE IF EXISTS "log_entries" CASCADE;
CREATE TABLE "log_entries" (
  "priority" bigint DEFAULT NULL,
  "message" character varying(512) DEFAULT NULL,
  "date" timestamp without time zone DEFAULT NULL,
  "category" character varying(255) DEFAULT NULL
);

--
-- Table: menu
--
DROP TABLE IF EXISTS "menu" CASCADE;
CREATE TABLE "menu" (
  "id" serial NOT NULL,
  -- The type of menu this item belongs to. FK to #__menu_types.menutype
  "menutype" character varying(24) NOT NULL,
  -- The display title of the menu item.
  "title" character varying(255) NOT NULL,
  -- The SEF alias of the menu item.
  "alias" character varying(255) NOT NULL,
  "note" character varying(255) DEFAULT '' NOT NULL,
  -- The computed path of the menu item based on the alias field.
  "path" character varying(1024) NOT NULL,
  -- The actually link the menu item refers to.
  "link" character varying(1024) NOT NULL,
  -- The type of link: Component, URL, Alias, Separator
  "type" character varying(16) NOT NULL,
  -- The published state of the menu link.
  "published" smallint DEFAULT 0 NOT NULL,
  -- The parent menu item in the menu tree.
  "parent_id" integer DEFAULT 1 NOT NULL,
  -- The relative level in the tree.
  "level" integer DEFAULT 0 NOT NULL,
  -- FK to #__extensions.id
  "component_id" integer DEFAULT 0 NOT NULL,
  -- The relative ordering of the menu item in the tree.
  "ordering" bigint DEFAULT 0 NOT NULL,
  -- FK to #__users.id
  "checked_out" integer DEFAULT 0 NOT NULL,
  -- The time the menu item was checked out.
  "checked_out_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  -- The click behaviour of the link.
  "browserNav" smallint DEFAULT 0 NOT NULL,
  -- The access level required to view the menu item.
  "access" smallint DEFAULT 0 NOT NULL,
  -- The image of the menu item.
  "img" character varying(255) NOT NULL,
  "template_style_id" integer DEFAULT 0 NOT NULL,
  -- JSON encoded data for the menu item.
  "params" text NOT NULL,
  -- Nested set lft.
  "lft" bigint DEFAULT 0 NOT NULL,
  -- Nested set rgt.
  "rgt" bigint DEFAULT 0 NOT NULL,
  -- Indicates if this menu item is the home or default page.
  "home" smallint DEFAULT 0 NOT NULL,
  "language" character(7) DEFAULT '' NOT NULL,
  "client_id" smallint DEFAULT 0 NOT NULL,
  PRIMARY KEY ("id"),
  CONSTRAINT "idx_client_id_parent_id_alias" UNIQUE ("client_id", "parent_id", "alias")
);
CREATE INDEX "menu_idx_componentid" on "menu" ("component_id", "menutype", "published", "access");
CREATE INDEX "menu_idx_menutype" on "menu" ("menutype");
CREATE INDEX "menu_idx_left_right" on "menu" ("lft", "rgt");
CREATE INDEX "menu_idx_alias" on "menu" ("alias");
CREATE INDEX "menu_idx_path" on "menu" ("path");
-- path(333));
CREATE INDEX "menu_idx_language" on "menu" ("language");

COMMENT ON COLUMN "menu"."menutype" IS 'The type of menu this item belongs to. FK to #__menu_types.menutype';
COMMENT ON COLUMN "menu"."title" IS 'The display title of the menu item.';
COMMENT ON COLUMN "menu"."alias" IS 'The SEF alias of the menu item.';
COMMENT ON COLUMN "menu"."path" IS 'The computed path of the menu item based on the alias field.';
COMMENT ON COLUMN "menu"."link" IS 'The actually link the menu item refers to.';
COMMENT ON COLUMN "menu"."type" IS 'The type of link: Component, URL, Alias, Separator';
COMMENT ON COLUMN "menu"."published" IS 'The published state of the menu link.';
COMMENT ON COLUMN "menu"."parent_id" IS 'The parent menu item in the menu tree.';
COMMENT ON COLUMN "menu"."level" IS 'The relative level in the tree.';
COMMENT ON COLUMN "menu"."component_id" IS 'FK to #__extensions.id';
COMMENT ON COLUMN "menu"."ordering" IS 'The relative ordering of the menu item in the tree.';
COMMENT ON COLUMN "menu"."checked_out" IS 'FK to #__users.id';
COMMENT ON COLUMN "menu"."checked_out_time" IS 'The time the menu item was checked out.';
COMMENT ON COLUMN "menu"."browserNav" IS 'The click behaviour of the link.';
COMMENT ON COLUMN "menu"."access" IS 'The access level required to view the menu item.';
COMMENT ON COLUMN "menu"."img" IS 'The image of the menu item.';
COMMENT ON COLUMN "menu"."params" IS 'JSON encoded data for the menu item.';
COMMENT ON COLUMN "menu"."lft" IS 'Nested set lft.';
COMMENT ON COLUMN "menu"."rgt" IS 'Nested set rgt.';
COMMENT ON COLUMN "menu"."home" IS 'Indicates if this menu item is the home or default page.';

--
-- Table: menu_types
--
DROP TABLE IF EXISTS "menu_types" CASCADE;
CREATE TABLE "menu_types" (
  "id" serial NOT NULL,
  "menutype" character varying(24) NOT NULL,
  "title" character varying(48) NOT NULL,
  "description" character varying(255) DEFAULT '' NOT NULL,
  PRIMARY KEY ("id"),
  CONSTRAINT "idx_menutype" UNIQUE ("menutype")
);

--
-- Table: modules
--
DROP TABLE IF EXISTS "modules" CASCADE;
CREATE TABLE "modules" (
  "id" serial NOT NULL,
  "title" character varying(100) DEFAULT '' NOT NULL,
  "note" character varying(255) DEFAULT '' NOT NULL,
  "content" text NOT NULL,
  "ordering" bigint DEFAULT 0 NOT NULL,
  "position" character varying(50) DEFAULT NULL,
  "checked_out" integer DEFAULT 0 NOT NULL,
  "checked_out_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "publish_up" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "publish_down" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "published" smallint DEFAULT 0 NOT NULL,
  "module" character varying(50) DEFAULT NULL,
  "access" smallint DEFAULT 0 NOT NULL,
  "showtitle" smallint DEFAULT 1 NOT NULL,
  "params" text NOT NULL,
  "client_id" smallint DEFAULT 0 NOT NULL,
  "language" character(7) NOT NULL,
  PRIMARY KEY ("id")
);
CREATE INDEX "modules_published" on "modules" ("published", "access");
CREATE INDEX "modules_newsfeeds" on "modules" ("module", "published");
CREATE INDEX "modules_idx_language" on "modules" ("language");

--
-- Table: modules_menu
--
DROP TABLE IF EXISTS "modules_menu" CASCADE;
CREATE TABLE "modules_menu" (
  "moduleid" bigint DEFAULT 0 NOT NULL,
  "menuid" bigint DEFAULT 0 NOT NULL,
  PRIMARY KEY ("moduleid", "menuid")
);

--
-- Table: schemas
--
DROP TABLE IF EXISTS "schemas" CASCADE;
CREATE TABLE "schemas" (
  "extension_id" bigint NOT NULL,
  "version_id" character varying(20) NOT NULL,
  PRIMARY KEY ("extension_id", "version_id")
);

--
-- Table: session
--
DROP TABLE IF EXISTS "session" CASCADE;
CREATE TABLE "session" (
  "session_id" character varying(32) DEFAULT '' NOT NULL,
  "client_id" smallint DEFAULT 0 NOT NULL,
  "guest" smallint DEFAULT 1,
  "time" character varying(14) DEFAULT '',
  "data" character varying(20480) DEFAULT NULL,
  "userid" bigint DEFAULT 0,
  "username" character varying(150) DEFAULT '',
  "usertype" character varying(50) DEFAULT '',
  PRIMARY KEY ("session_id")
);
CREATE INDEX "session_whosonline" on "session" ("guest", "usertype");
CREATE INDEX "session_userid" on "session" ("userid");
CREATE INDEX "session_time" on "session" ("time");

--
-- Table: updates
--

-- Comments:
-- Available Updates
--
DROP TABLE IF EXISTS "updates" CASCADE;
CREATE TABLE "updates" (
  "update_id" serial NOT NULL,
  "update_site_id" bigint DEFAULT 0,
  "extension_id" bigint DEFAULT 0,
  "categoryid" bigint DEFAULT 0,
  "name" character varying(100) DEFAULT '',
  "description" text NOT NULL,
  "element" character varying(100) DEFAULT '',
  "type" character varying(20) DEFAULT '',
  "folder" character varying(20) DEFAULT '',
  "client_id" smallint DEFAULT 0,
  "version" character varying(10) DEFAULT '',
  "data" text NOT NULL,
  "detailsurl" text NOT NULL,
  PRIMARY KEY ("update_id")
);
COMMENT ON TABLE "updates" IS 'Available Updates';

--
-- Table: update_categories
--

-- Comments:
-- Update Categories
--
DROP TABLE IF EXISTS "update_categories" CASCADE;
CREATE TABLE "update_categories" (
  "categoryid" serial NOT NULL,
  "name" character varying(20) DEFAULT '',
  "description" text NOT NULL,
  "parent" bigint DEFAULT 0,
  "updatesite" bigint DEFAULT 0,
  PRIMARY KEY ("categoryid")
);
COMMENT ON TABLE "update_categories" IS 'Update Categories';

--
-- Table: update_sites
--

-- Comments:
-- Update Sites
--
DROP TABLE IF EXISTS "update_sites" CASCADE;
CREATE TABLE "update_sites" (
  "update_site_id" serial NOT NULL,
  "name" character varying(100) DEFAULT '',
  "type" character varying(20) DEFAULT '',
  "location" text NOT NULL,
  "enabled" bigint DEFAULT 0,
  PRIMARY KEY ("update_site_id")
);
COMMENT ON TABLE "update_sites" IS 'Update Sites';

--
-- Table: update_sites_extensions
--

-- Comments:
-- Links extensions to update sites
--
DROP TABLE IF EXISTS "update_sites_extensions" CASCADE;
CREATE TABLE "update_sites_extensions" (
  "update_site_id" bigint DEFAULT 0 NOT NULL,
  "extension_id" bigint DEFAULT 0 NOT NULL,
  PRIMARY KEY ("update_site_id", "extension_id")
);
COMMENT ON TABLE "update_sites_extensions" IS 'Links extensions to update sites';

--
-- Table: usergroups
--
DROP TABLE IF EXISTS "usergroups" CASCADE;
CREATE TABLE "usergroups" (
  -- Primary Key
  "id" serial NOT NULL,
  -- Adjacency List Reference Id
  "parent_id" integer DEFAULT 0 NOT NULL,
  -- Nested set lft.
  "lft" bigint DEFAULT 0 NOT NULL,
  -- Nested set rgt.
  "rgt" bigint DEFAULT 0 NOT NULL,
  "title" character varying(100) DEFAULT '' NOT NULL,
  PRIMARY KEY ("id"),
  CONSTRAINT "idx_usergroup_parent_title_lookup" UNIQUE ("parent_id", "title")
);
CREATE INDEX "usergroups_idx_usergroup_title_lookup" on "usergroups" ("title");
CREATE INDEX "usergroups_idx_usergroup_adjacency_lookup" on "usergroups" ("parent_id");
CREATE INDEX "usergroups_idx_usergroup_nested_set_lookup" on "usergroups" ("lft", "rgt");

COMMENT ON COLUMN "usergroups"."id" IS 'Primary Key';
COMMENT ON COLUMN "usergroups"."parent_id" IS 'Adjacency List Reference Id';
COMMENT ON COLUMN "usergroups"."lft" IS 'Nested set lft.';
COMMENT ON COLUMN "usergroups"."rgt" IS 'Nested set rgt.';

--
-- Table: users
--
DROP TABLE IF EXISTS "users" CASCADE;
CREATE TABLE "users" (
  "id" serial NOT NULL,
  "name" character varying(255) DEFAULT '' NOT NULL,
  "username" character varying(150) DEFAULT '' NOT NULL,
  "email" character varying(100) DEFAULT '' NOT NULL,
  "password" character varying(100) DEFAULT '' NOT NULL,
  "usertype" character varying(25) DEFAULT '' NOT NULL,
  "block" smallint DEFAULT 0 NOT NULL,
  "sendEmail" smallint DEFAULT 0,
  "registerDate" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "lastvisitDate" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "activation" character varying(100) DEFAULT '' NOT NULL,
  "params" text NOT NULL,
  PRIMARY KEY ("id")
);
CREATE INDEX "users_usertype" on "users" ("usertype");
CREATE INDEX "users_idx_name" on "users" ("name");
CREATE INDEX "users_idx_block" on "users" ("block");
CREATE INDEX "users_username" on "users" ("username");
CREATE INDEX "users_email" on "users" ("email");

--
-- Table: user_profiles
--

-- Comments:
-- Simple user profile storage table
--
DROP TABLE IF EXISTS "user_profiles" CASCADE;
CREATE TABLE "user_profiles" (
  "user_id" bigint NOT NULL,
  "profile_key" character varying(100) NOT NULL,
  "profile_value" character varying(255) NOT NULL,
  "ordering" bigint DEFAULT 0 NOT NULL,
  CONSTRAINT "idx_user_id_profile_key" UNIQUE ("user_id", "profile_key")
);
COMMENT ON TABLE "user_profiles" IS 'Simple user profile storage table';

--
-- Table: user_usergroup_map
--
DROP TABLE IF EXISTS "user_usergroup_map" CASCADE;
CREATE TABLE "user_usergroup_map" (
  -- Foreign Key to #__users.id
  "user_id" integer DEFAULT 0 NOT NULL,
  -- Foreign Key to #__usergroups.id
  "group_id" integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ("user_id", "group_id")
);

COMMENT ON COLUMN "user_usergroup_map"."user_id" IS 'Foreign Key to #__users.id';
COMMENT ON COLUMN "user_usergroup_map"."group_id" IS 'Foreign Key to #__usergroups.id';

--
-- Table: viewlevels
--
DROP TABLE IF EXISTS "viewlevels" CASCADE;
CREATE TABLE "viewlevels" (
  -- Primary Key
  "id" serial NOT NULL,
  "title" character varying(100) DEFAULT '' NOT NULL,
  "ordering" bigint DEFAULT 0 NOT NULL,
  -- JSON encoded access control.
  "rules" character varying(5120) NOT NULL,
  PRIMARY KEY ("id"),
  CONSTRAINT "idx_assetgroup_title_lookup" UNIQUE ("title")
);

COMMENT ON COLUMN "viewlevels"."id" IS 'Primary Key';
COMMENT ON COLUMN "viewlevels"."rules" IS 'JSON encoded access control.';

--
-- Table: dbtest
--
DROP TABLE IF EXISTS "dbtest" CASCADE;
CREATE TABLE "dbtest" (
  "id" serial NOT NULL,
  "title" character varying(50) NOT NULL,
  "start_date" timestamp without time zone NOT NULL,
  "end_date" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "description" text NOT NULL,
  "data" bytea,
  PRIMARY KEY ("id")
);

