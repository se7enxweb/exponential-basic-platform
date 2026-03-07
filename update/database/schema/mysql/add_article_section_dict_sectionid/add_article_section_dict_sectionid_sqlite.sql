-- Add SectionID column to eZArticle_ArticleSectionDict (SQLite)
-- This column is required by the section-linking query in kernel/classes/ezmodulelink.php
ALTER TABLE eZArticle_ArticleSectionDict ADD COLUMN SectionID integer NOT NULL DEFAULT 0;
