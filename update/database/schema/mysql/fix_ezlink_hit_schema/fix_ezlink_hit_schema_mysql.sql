-- Fix eZLink_Hit table: was incorrectly created with eZLink_Link columns.
-- Correct schema has ID, Link (FK to eZLink_Link), Time, RemoteIP.
-- Safe to run: eZLink_Hit is a hit-tracking table with no persistent data.
DROP TABLE IF EXISTS eZLink_Hit;
CREATE TABLE eZLink_Hit (
  `ID` int(11) NOT NULL DEFAULT 0,
  Link int(11) DEFAULT NULL,
  Time int(11) NOT NULL DEFAULT 0,
  RemoteIP varchar(15) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);
