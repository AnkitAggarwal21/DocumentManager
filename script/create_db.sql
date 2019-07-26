# create_db.sql (Taken from Horde/IMP setup and modified)
# You can simply direct this file to mysql as STDIN:
# mysql [user/pass/host args] database < mysql_create.sql

CREATE DATABASE documents;
USE documents;
# ACL for the documents
CREATE TABLE ACL (
  document_id	INT UNSIGNED NOT NULL,
  user_id	INT UNSIGNED NOT NULL,
  level		ENUM('R','W','G') NOT NULL DEFAULT 'R',
  PRIMARY KEY	(document_id,user_id)
);

# For the message board
CREATE TABLE chat (
  id		INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ref_id	INT UNSIGNED NOT NULL,
  user		INT UNSIGNED NOT NULL,
  subject	VARCHAR(128) NOT NULL,
  content	TEXT NOT NULL,
  date		DATETIME NOT NULL,
  PRIMARY KEY	(id),
  INDEX		(ref_id),
  INDEX		(user),
  INDEX		(subject)
);

# Main document info; a fair few indices, but that ought to make the ORDER BY
# in the list & search a bit faster.
CREATE TABLE documents (
  id		INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name		VARCHAR(64) NOT NULL,
  type		VARCHAR(64) NOT NULL,
  size		INT UNSIGNED NOT NULL,
  author	INT UNSIGNED NOT NULL,
  maintainer	INT UNSIGNED NOT NULL,
  revision	INT UNSIGNED NOT NULL,
  created	DATETIME NOT NULL,
  modified	DATETIME NOT NULL,
  PRIMARY KEY	(id),
  INDEX		(name),
  INDEX		(size),
  INDEX		(author),
  INDEX		(maintainer),
  INDEX		(revision),
  INDEX		(created),
  INDEX		(modified)
);

# Actual document content, just for storage.
CREATE TABLE documents_content (
  id		INT UNSIGNED NOT NULL,
  content	longblob NOT NULL,
  INDEX		(id),
  UNIQUE	(id)
);

CREATE TABLE actions_content (
  id    INT UNSIGNED NOT NULL,
  docid INT UNSIGNED NOT NULL,
  content mediumtext NULL,
  comments longtext NOT NULL,
  PRIMARY KEY (id, docid)
);

# Document descriptions, seperate table for no apparent reason :)
CREATE TABLE documents_info (
  id		INT UNSIGNED NOT NULL,
  info		tinytext NOT NULL,
  INDEX		(id),
  UNIQUE	(id)
);

# Keywords table.
CREATE TABLE documents_keywords (
  id		INT UNSIGNED NOT NULL,
  keyword	VARCHAR(64) NOT NULL,
  PRIMARY KEY	(id,keyword)
);

# Download log.
CREATE TABLE documents_log (
  id		INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user		INT UNSIGNED NOT NULL,
  document	INT UNSIGNED NOT NULL,
  revision	INT UNSIGNED NOT NULL,
  date		TIMESTAMP NOT NULL,
  PRIMARY KEY	(id),
  INDEX		(user),
  INDEX		(document),
  INDEX		(revision),
  INDEX		(date)
);

# God privs. There is a reason for a seperate table, but I don't remember what it was.
CREATE TABLE gods (
  user		INT UNSIGNED NOT NULL,
  PRIMARY KEY	(user)
);

# The users table.
CREATE TABLE users (
  id		INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user		VARCHAR(16) NOT NULL,
  pass		VARCHAR(64) NOT NULL,
  name		VARCHAR(64) NOT NULL,
  email		VARCHAR(64) NOT NULL,
  PRIMARY KEY	(id),
  INDEX		(user),
  UNIQUE	(user)
);

# Add a default admin user.
INSERT INTO users ( user, pass, name, email )
   VALUES (
      'Admin',
      SHA1('docmgr'),
      'Admin User',
      'root@localhost'
);

# And finally, elevate the admin user.
INSERT INTO gods ( user )
    VALUES (
       1
);

# done! *whee!*
