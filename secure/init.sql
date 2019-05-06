-- TODO: Put ALL SQL in between 'BEGIN TRANSACTION' and 'COMMIT'
BEGIN TRANSACTION;

-- admins table
CREATE TABLE admins (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	admin_id TEXT NOT NULL UNIQUE,
	password TEXT NOT NULL
);

-- sessions table
CREATE TABLE sessions (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  user_id INTEGER,
  session TEXT NOT NULL UNIQUE
);

-- images table
CREATE TABLE images (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  filename TEXT NOT NULL,
  ext TEXT NOT NULL,
  description TEXT,
  admin_id INTEGER NOT NULL -- ADMINISTRATOR'S ID IN 'admins' TABLE, NOT LOGIN INFORMATION
);

-- tags table
CREATE TABLE  tags (
   id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
   tag TEXT NOT NULL
);

-- image_tags table
CREATE TABLE  image_tags (
   id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
   tag_id TEXT NOT NULL,
   image_id TEXT NOT NULL
);

-- albums table
CREATE TABLE  albums (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  album TEXT NOT NULL
);

-- image_albums table
CREATE TABLE  image_albums (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  album_id TEXT NOT NULL,
  image_id TEXT NOT NULL
);

-- contact submissions table
CREATE TABLE  submissions (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  reason  NOT NULL,
  name  NOT NULL,
  email  TEXT NOT NULL,
  phone  TEXT NOT NULL,
  comment  TEXT
);


-- seed data
INSERT INTO admins (id, admin_id, password) VALUES (1, 'jgibson', '$2y$10$7J6OBlJQvj0Jy6hXJNkTSuD1ceC5fUE74bftOy57LVTus4c5kHmKi'); -- password: instagram

INSERT INTO images (id, filename, ext, description, admin_id) VALUES (1, 'stone-church', 'jpg', 'watercolor illustration of a stone church.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (2, 'goshen-jewelers', 'jpg', 'watercolor illustration of goshen jewelers.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (3, 'fall-creek-in-april', 'jpg', 'watercolor illustration of fall creek in april.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (4, 'aub-sculpture-painting', 'jpg', 'watercolor painting of the aub sculpture.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (5, 'chittenango-creek', 'jpg', 'watercolor illustration of chittenango creek.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (6, 'inn', 'jpg', 'watercolor illustration of the front of an inn.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (7, 'ottawa-house-view', 'jpg', 'watercolor illustration of view from a house in ottowa.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (8, 'maine', 'jpg', 'watercolor illustration of a view in maine.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (9, 'chris', 'jpg', 'watercolor illustration of chris.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (10, 'ray', 'jpg', 'watercolor illustration of ray.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (11, 'maria', 'jpg', 'watercolor illustration of a stone church.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (12, 'grandmother', 'jpg', 'pencil sketch of a grandmother.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (13, '9', 'jpg', 'pencil sketch of a child.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (14, 'skipping-rope', 'jpg', 'watercolor illustration of a girl skipping rope.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (15, 'sonnet', 'jpg', 'watercolor piece illustrating a parent and child reading a sonnet.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (16, 'gramophone-love', 'jpg', 'flowery pencil illustration of a couple and a gramaphone.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (17, 'mustaches', 'jpg', 'pencil illustration of a whole lot of fake mustaches.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (18, 'cat-walk', 'jpg', 'pencil illustration of a woman walking with a cat on a hill.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (19, 'wolf', 'jpg', 'pencil watercolor illustration of a man and a wolf in the snow.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (20, 'cheese-plate', 'jpg', 'a illustration of a delicious cheese plate.', 1);
INSERT INTO images (id, filename, ext, description, admin_id) VALUES (21, 'sleep-song', 'jpg', 'a whimsical illustration of a woman allowing the night to come into her room.', 1);

INSERT INTO albums (album) VALUES ('available');
INSERT INTO albums (album) VALUES ('outdoor');
INSERT INTO albums (album) VALUES ('portrait');
INSERT INTO albums (album) VALUES ('illustration');
INSERT INTO albums (album) VALUES ('personal');

INSERT INTO image_albums (album_id, image_id) VALUES (1,  1);
INSERT INTO image_albums (album_id, image_id) VALUES (1,  2);
INSERT INTO image_albums (album_id, image_id) VALUES (1,  3);
INSERT INTO image_albums (album_id, image_id) VALUES (1,  4);
INSERT INTO image_albums (album_id, image_id) VALUES (1,  5);
INSERT INTO image_albums (album_id, image_id) VALUES (1,  7);
INSERT INTO image_albums (album_id, image_id) VALUES (2,  1);
INSERT INTO image_albums (album_id, image_id) VALUES (2,  2);
INSERT INTO image_albums (album_id, image_id) VALUES (2,  3);
INSERT INTO image_albums (album_id, image_id) VALUES (2,  4);
INSERT INTO image_albums (album_id, image_id) VALUES (2,  5);
INSERT INTO image_albums (album_id, image_id) VALUES (2,  6);
INSERT INTO image_albums (album_id, image_id) VALUES (2,  7);
INSERT INTO image_albums (album_id, image_id) VALUES (2,  8);
INSERT INTO image_albums (album_id, image_id) VALUES (3,  9);
INSERT INTO image_albums (album_id, image_id) VALUES (3,  10);
INSERT INTO image_albums (album_id, image_id) VALUES (3,  11);
INSERT INTO image_albums (album_id, image_id) VALUES (3,  12);
INSERT INTO image_albums (album_id, image_id) VALUES (3,  13);
INSERT INTO image_albums (album_id, image_id) VALUES (4,  14);
INSERT INTO image_albums (album_id, image_id) VALUES (4,  15);
INSERT INTO image_albums (album_id, image_id) VALUES (4,  16);
INSERT INTO image_albums (album_id, image_id) VALUES (4,  17);
INSERT INTO image_albums (album_id, image_id) VALUES (4,  18);
INSERT INTO image_albums (album_id, image_id) VALUES (4,  19);
INSERT INTO image_albums (album_id, image_id) VALUES (4,  20);
INSERT INTO image_albums (album_id, image_id) VALUES (5,  21);

INSERT INTO tags (tag) VALUES ('watercolor');
INSERT INTO tags (tag) VALUES ('pencil');
INSERT INTO tags (tag) VALUES ('nature');
INSERT INTO tags (tag) VALUES ('facade');
INSERT INTO tags (tag) VALUES ('landscape');
INSERT INTO tags (tag) VALUES ('children');

INSERT INTO image_tags (tag_id, image_id) VALUES (1,  1);
INSERT INTO image_tags (tag_id, image_id) VALUES (1,  2);
INSERT INTO image_tags (tag_id, image_id) VALUES (1,  3);
INSERT INTO image_tags (tag_id, image_id) VALUES (1,  4);
INSERT INTO image_tags (tag_id, image_id) VALUES (1,  5);
INSERT INTO image_tags (tag_id, image_id) VALUES (1,  6);
INSERT INTO image_tags (tag_id, image_id) VALUES (1,  7);
INSERT INTO image_tags (tag_id, image_id) VALUES (1,  8);
INSERT INTO image_tags (tag_id, image_id) VALUES (1,  9);
INSERT INTO image_tags (tag_id, image_id) VALUES (1,  14);
INSERT INTO image_tags (tag_id, image_id) VALUES (2,  11);
INSERT INTO image_tags (tag_id, image_id) VALUES (2,  12);
INSERT INTO image_tags (tag_id, image_id) VALUES (2,  13);
INSERT INTO image_tags (tag_id, image_id) VALUES (2,  15);
INSERT INTO image_tags (tag_id, image_id) VALUES (2,  17);
INSERT INTO image_tags (tag_id, image_id) VALUES (2,  18);
INSERT INTO image_tags (tag_id, image_id) VALUES (2,  19);
INSERT INTO image_tags (tag_id, image_id) VALUES (2,  21);
INSERT INTO image_tags (tag_id, image_id) VALUES (3,  3);
INSERT INTO image_tags (tag_id, image_id) VALUES (3,  5);
INSERT INTO image_tags (tag_id, image_id) VALUES (3,  7);
INSERT INTO image_tags (tag_id, image_id) VALUES (3,  8);
INSERT INTO image_tags (tag_id, image_id) VALUES (4,  1);
INSERT INTO image_tags (tag_id, image_id) VALUES (4,  2);
INSERT INTO image_tags (tag_id, image_id) VALUES (4,  4);
INSERT INTO image_tags (tag_id, image_id) VALUES (4,  6);
INSERT INTO image_tags (tag_id, image_id) VALUES (5,  3);
INSERT INTO image_tags (tag_id, image_id) VALUES (5,  7);
INSERT INTO image_tags (tag_id, image_id) VALUES (5,  8);
INSERT INTO image_tags (tag_id, image_id) VALUES (6,  14);
INSERT INTO image_tags (tag_id, image_id) VALUES (6,  15);
INSERT INTO image_tags (tag_id, image_id) VALUES (6,  16);
INSERT INTO image_tags (tag_id, image_id) VALUES (6,  17);
INSERT INTO image_tags (tag_id, image_id) VALUES (6,  18);
INSERT INTO image_tags (tag_id, image_id) VALUES (6,  19);
INSERT INTO image_tags (tag_id, image_id) VALUES (6,  20);
INSERT INTO image_tags (tag_id, image_id) VALUES (6,  21);


COMMIT;
