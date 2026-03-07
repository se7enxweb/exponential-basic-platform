-- Add SectionID column to eZArticle_ArticleSectionDict (PostgreSQL)
-- This column is required by the section-linking query in kernel/classes/ezmodulelink.php
ALTER TABLE eZArticle_ArticleSectionDict ADD COLUMN SectionID int NOT NULL DEFAULT 0;

-- Also create eZArticle_LinkSection if it does not exist
CREATE TABLE IF NOT EXISTS eZArticle_LinkSection (
  ID int NOT NULL,
  Name varchar(30) default NULL,
  PRIMARY KEY (ID)
);
