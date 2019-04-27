-- TODO: Put ALL SQL in between `BEGIN TRANSACTION` and `COMMIT`
BEGIN TRANSACTION;

-- TODO: create tables
-- user table
CREATE TABLE 'users' (
    'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'admin_id' TEXT NOT NULL UNIQUE,
    'password' TEXT NOT NULL,
    'session' TEXT UNIQUE
);

-- contact table
CREATE TABLE 'questions' (
  'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  'reason' NOT NULL,
  'name' NOT NULL,
  'email' TEXT NOT NULL,
  'phone' TEXT NOT NULL,
  'comment' TEXT
);

-- gallery table
CREATE TABLE 'gallery' (

);



-- TODO: initial seed data
INSERT INTO 'users' (id, admin_id, password) VALUES (1, 'jgibson', '$2y$10$7J6OBlJQvj0Jy6hXJNkTSuD1ceC5fUE74bftOy57LVTus4c5kHmKi'); -- password: instagram

-- TODO: FOR HASHED PASSWORDS, LEAVE A COMMENT WITH THE PLAIN TEXT PASSWORD!












COMMIT;
