-- Run this once in MySQL Workbench against the career_ai database.
-- password_hash() hashes may be longer than the old plain-text password column.
ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL;
