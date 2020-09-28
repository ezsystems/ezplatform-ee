-- EZEE-3244: Added path to site configuration --
ALTER TABLE `ezsite_public_access` ADD COLUMN site_matcher_path VARCHAR(255) DEFAULT NULL;
--
