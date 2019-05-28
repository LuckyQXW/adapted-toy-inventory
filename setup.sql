-- Name: Wen Qiu
-- Date: May 30, 2019
-- Section: CSE 154 AJ
-- This sql file sets up an inventory table for the Adaptable Toy Inventory and
-- inserts the first row.

USE inventorydb;

-- Creates the inventory table in the database
CREATE TABLE inventory(
  id INT NOT NULL AUTO_INCREMENT,
  item VARCHAR(255) NOT NULL,
  function VARCHAR(255) NOT NULL,
  total INT NOT NULL,
  adapted INT NOT NULL,
  unadapted INT NOT NULL,
  broken INT NOT NULL,
  other INT NOT NULL,
  donated INT NOT NULL,
  available INT NOT NULL,
  image TEXT,
  PRIMARY KEY(id)
);

-- Handles image as empty string to stay consistent with other imported rows
INSERT INTO inventory(id, item, function, total, adapted, unadapted, broken,
  other, donated, available, image)
VALUES (0, "Fire Engine", "Lights , Sound , Motion , Bump N' Go", 9, 8, 0, 0, 1, 2, 6, "");
