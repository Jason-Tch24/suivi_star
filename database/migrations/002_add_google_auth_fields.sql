-- Make password_hash nullable for Google users
ALTER TABLE users
MODIFY COLUMN password_hash VARCHAR(255) NULL;
