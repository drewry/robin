CREATE TABLE IF NOT EXISTS  `follows` (
 `uid` VARCHAR( 100 ) NOT NULL ,
 `fid` VARCHAR( 100 ) NOT NULL
) ENGINE = INNODB DEFAULT CHARSET = latin1;# MySQL returned an empty result set (i.e. zero rows).
CREATE TABLE IF NOT EXISTS  `msgs` (
 `gid` VARCHAR( 100 ) NOT NULL ,
 `uid` VARCHAR( 100 ) NOT NULL ,
 `msg` VARCHAR( 140 ) NOT NULL ,
PRIMARY KEY (  `gid` )
) ENGINE = INNODB DEFAULT CHARSET = latin1;# MySQL returned an empty result set (i.e. zero rows).
CREATE TABLE IF NOT EXISTS  `nodes` (
 `nid` INT( 10 ) NOT NULL ,
 `url` VARCHAR( 255 ) NOT NULL ,
 `last` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY (  `nid` )
) ENGINE = INNODB DEFAULT CHARSET = latin1;# MySQL returned an empty result set (i.e. zero rows).
CREATE TABLE IF NOT EXISTS  `users` (
 `uid` VARCHAR( 100 ) NOT NULL ,
 `name` VARCHAR( 20 ) NOT NULL ,
 `pass` VARCHAR( 32 ) NOT NULL ,
PRIMARY KEY (  `uid` )
) ENGINE = INNODB DEFAULT CHARSET = latin1;# MySQL returned an empty result set (i.e. zero rows).