-- Fix eZLink_Hit table: was incorrectly created with eZLink_Link columns.
-- Correct schema has ID, Link (FK to eZLink_Link), Time, RemoteIP.
-- Safe to run: eZLink_Hit is a hit-tracking table with no persistent data.
DROP TABLE IF EXISTS eZLink_Hit;
CREATE TABLE eZLink_Hit (
  ID int NOT NULL,
  Link int default NULL,
  Time int NOT NULL,
  RemoteIP varchar(15) default NULL,
  PRIMARY KEY (ID)
);
