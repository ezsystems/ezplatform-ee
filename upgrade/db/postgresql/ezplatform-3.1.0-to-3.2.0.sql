-- EZEE-3244: Added path to site configuration --
ALTER TABLE `ezsite_public_access` ADD COLUMN site_matcher_path VARCHAR(255) DEFAULT NULL;
--

DROP TABLE IF EXISTS ibexa_segment_group_map;
CREATE TABLE ibexa_segment_group_map (
  segment_id int NOT NULL,
  group_id int NOT NULL,
  PRIMARY KEY (segment_id,group_id)
);

DROP TABLE IF EXISTS ibexa_segment_groups;
CREATE TABLE ibexa_segment_groups (
  id SERIAL NOT NULL,
  identifier varchar(255) NOT NULL,
  name varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id,identifier),
  CONSTRAINT ibexa_segment_groups_identifier UNIQUE (identifier)
);

DROP TABLE IF EXISTS ibexa_segment_user_map;
CREATE TABLE ibexa_segment_user_map (
  segment_id int NOT NULL,
  user_id int NOT NULL,
  PRIMARY KEY (segment_id,user_id)
);

DROP TABLE IF EXISTS ibexa_segments;
CREATE TABLE ibexa_segments (
  id SERIAL NOT NULL,
  identifier varchar(255) NOT NULL,
  name varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id,identifier),
  CONSTRAINT ibexa_segments_identifier UNIQUE (identifier)
);
