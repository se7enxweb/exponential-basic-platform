-- Add SectionID column to eZArticle_ArticleSectionDict
-- This column is required by the section-linking query in kernel/classes/ezmodulelink.php
ALTER TABLE eZArticle_ArticleSectionDict ADD COLUMN SectionID int(11) NOT NULL DEFAULT 0;
