-- Add LastLogin column to User table
ALTER TABLE User
ADD COLUMN LastLogin TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN LastModified TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- Update existing users with current timestamp for LastLogin
UPDATE User SET LastLogin = CURRENT_TIMESTAMP WHERE LastLogin IS NULL; 