-- Stap 6: users tabel
CREATE TABLE IF NOT EXISTS users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  naam VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  wachtwoord VARCHAR(255) NOT NULL, -- gehasht met password_hash()
  aangemaakt_op TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
);

-- Testgebruiker: admin@portfolio.nl / admin123
INSERT IGNORE INTO users (naam, email, wachtwoord)
VALUES ('Admin', 'admin@portfolio.nl', '$2y$10$OnZRmGAZRyJcEfdHvVyrVO9ZC3lRj.oaGAmutPWpdhVzEDN6Zj.FG');

-- Stap 11: afbeelding bij een project
ALTER TABLE projecten ADD COLUMN IF NOT EXISTS afbeelding VARCHAR(255) NULL DEFAULT NULL;
