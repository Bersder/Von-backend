create database if not exists Article;
create database if not exists Note;
create database if not exists Comment;
create database if not exists Page;
create database if not exists User;
create database if not exists Tag;
create database if not exists Tmp;
create database if not exists Dynamic;
/*==================Article====================*/
use Article;
CREATE TABLE `article_content` (
  `aid` smallint(5) unsigned NOT NULL,
  `rawContent` varchar(15000) DEFAULT NULL,
  PRIMARY KEY (`aid`)
);

CREATE TABLE `article_content_tmp` (
  `aid` smallint(5) unsigned NOT NULL,
  `rawContent` varchar(15000) DEFAULT NULL,
  PRIMARY KEY (`aid`)
);

CREATE TABLE `article_info` (
  `aid` smallint(5) unsigned NOT NULL,
  `type` varchar(10) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `preview` varchar(150) DEFAULT NULL,
  `imgSrc` varchar(100) DEFAULT NULL,
  `author` varchar(20) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `lut` datetime DEFAULT NULL,
  `seriesID` smallint(5) unsigned DEFAULT NULL,
  `commentCount` smallint(5) unsigned DEFAULT 0,
  `readCount` int(10) unsigned DEFAULT 0,
  `liked` int(10) unsigned DEFAULT 0,
  `topped` tinyint(1) unsigned DEFAULT 0,
  PRIMARY KEY (`aid`)
);

CREATE TABLE `article_info_tmp` (
  `aid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `preview` varchar(150) DEFAULT NULL,
  `imgSrc` varchar(100) DEFAULT NULL,
  `author` varchar(20) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `lut` datetime DEFAULT NULL,
  `inputTags` varchar(50) DEFAULT '',
  `seriesID` smallint(5) unsigned DEFAULT NULL,
  `asbu` tinyint(1) unsigned DEFAULT 0,
  `asdraft` tinyint(1) unsigned DEFAULT 1,
  PRIMARY KEY (`aid`)
);

CREATE TABLE `series_link` (
  `sid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `seriesName` varchar(40) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`sid`)
);



/*==================Note====================*/
use Note;
CREATE TABLE `note_content` (
  `nid` smallint(5) unsigned NOT NULL,
  `rawContent` varchar(15000) DEFAULT NULL,
  PRIMARY KEY (`nid`)
);

CREATE TABLE `note_content_tmp` (
  `nid` smallint(5) unsigned NOT NULL,
  `rawContent` varchar(15000) DEFAULT NULL,
  PRIMARY KEY (`nid`)
);

CREATE TABLE `note_info` (
  `nid` smallint(5) unsigned NOT NULL,
  `type` varchar(10) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `preview` varchar(150) DEFAULT NULL,
  `imgSrc` varchar(100) DEFAULT NULL,
  `author` varchar(20) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `lut` datetime DEFAULT NULL,
  `catID` smallint(5) unsigned DEFAULT NULL,
  `commentCount` smallint(5) unsigned DEFAULT 0,
  `readCount` int(10) unsigned DEFAULT 0,
  `liked` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`nid`)
);

CREATE TABLE `note_info_tmp` (
  `nid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `preview` varchar(150) DEFAULT NULL,
  `imgSrc` varchar(100) DEFAULT NULL,
  `author` varchar(20) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `lut` datetime DEFAULT NULL,
  `inputTags` varchar(50) DEFAULT '',
  `catID` smallint(5) unsigned DEFAULT 1,
  `asbu` tinyint(1) unsigned DEFAULT 0,
  `asdraft` tinyint(1) unsigned DEFAULT 1,
  PRIMARY KEY (`nid`)
);

CREATE TABLE `note_category` (
  `cid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `catName_en` varchar(30) NOT NULL,
  `catName` varchar(20) NOT NULL,
  PRIMARY KEY (`cid`)
);
insert into note_category values(1,'uncat','未分类');



/*==================Comment====================*/
use Comment;
CREATE TABLE `comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `topic_id` smallint(5) unsigned NOT NULL,
  `topic_type` varchar(10) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `to_uid` int(10) unsigned DEFAULT NULL,
  `notify` tinyint(1) unsigned NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
);



/*==================Page====================*/
use Page;
CREATE TABLE `album` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `imgSrc` varchar(100) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `bangumi` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `nameCN` varchar(50) DEFAULT NULL,
  `link` varchar(150) NOT NULL,
  `cover` varchar(150) DEFAULT NULL,
  `airDate` date DEFAULT NULL,
  `curNum` smallint(5) unsigned DEFAULT 0,
  `epsNum` smallint(5) unsigned DEFAULT NULL,
  `comment` varchar(300) DEFAULT NULL,
  `fin` tinyint(1) unsigned DEFAULT NULL,
  `finDate` date DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `gossip` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `content` varchar(200) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `header_area` (
  `id` tinyint(3) unsigned NOT NULL,
  `imgSrc` varchar(150) NOT NULL,
  `title` varchar(20) NOT NULL,
  `type` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
);
insert into header_area values(null,'/site/header/anime.jpg','Anime','anime','这就是二次螈? 爱了');
insert into header_area values(null,'/site/header/code.jpg','极客','code','一天快乐的源泉（伪）');
insert into header_area values(null,'/site/header/game.jpg','游民','game','Do You Like Van 游戏');
insert into header_area values(null,'/site/header/trivial.jpg','随写','trivial','记录生活与思考的小本本');
insert into header_area values(null,'/site/header/note.jpg','笔记','note','高中时代留下来的“坏习惯”');
insert into header_area values(null,'/site/header/link.jpg','友链','link','Boy♂Next♂Door');
insert into header_area values(null,'/site/header/archive.png','归档','archive','');
insert into header_area values(null,'/site/header/about.jpg','关于','about','');

CREATE TABLE `link` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `url` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `notice` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `content` varchar(200) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);




/*==================User====================*/
use User;
CREATE TABLE `me` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(50) NOT NULL,
  `match_` varchar(32) NOT NULL,
  `name` varchar(20) NOT NULL,
  `avatar` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sign` varchar(50) DEFAULT NULL,
  `status` smallint(5) unsigned DEFAULT 0,
  `memo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ulink` varchar(100) DEFAULT NULL,
  `avatar` varchar(150) DEFAULT NULL,
  `message` varchar(50) DEFAULT NULL,
  `points` int(10) unsigned DEFAULT 1,
  `isFriend` tinyint(1) DEFAULT 0,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);




/*==================Tag====================*/
use Tag;
CREATE TABLE `tag_cloud` (
  `tid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `tagName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`tid`)
);

CREATE TABLE `tag_map` (
  `xid` smallint(5) unsigned NOT NULL,
  `type` varchar(10) NOT NULL,
  `tid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`xid`,`type`,`tid`)
);

CREATE TABLE `tag_map_tmp` (
  `xid` smallint(5) unsigned NOT NULL,
  `type` varchar(10) NOT NULL,
  `tid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`xid`,`type`,`tid`)
);

create view tm_tc
as select xid,type,tc.tid,tc.tagName from tag_map as tm,tag_cloud as tc where tm.tid=tc.tid;

create view tm_tc_tmp
as select xid,type,tc.tid,tc.tagName from tag_map_tmp as tmt,tag_cloud as tc where tmt.tid=tc.tid;





/*==================Tmp====================*/
use Tmp;
CREATE TABLE `auth_log` (
  `typeCode` tinyint(3) unsigned NOT NULL,
  `notes` varchar(100) DEFAULT NULL,
  `remain` tinyint(3) unsigned DEFAULT NULL,
  `LLIp` varchar(40) DEFAULT NULL,
  `LLLoc` varchar(50) DEFAULT NULL,
  `LLTime` datetime DEFAULT NULL,
  PRIMARY KEY (`typeCode`)
);
insert into auth_log values(0,'登录限制',3,null,null,null),(1,'授权删除限制',3,null,null,null);

CREATE TABLE `visit_log` (
  `ip` varchar(40) NOT NULL,
  `location` varchar(50) DEFAULT NULL,
  `xtype` varchar(10) NOT NULL,
  `xid` smallint(5) unsigned NOT NULL,
  `pv` tinyint(3) unsigned DEFAULT NULL,
  `lastVisit` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`ip`,`xtype`,`xid`)
);



/*==================Dynamic====================*/
use Dynamic;
CREATE TABLE `dyn_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `dyn_id` smallint(5) unsigned NOT NULL,
  `content` varchar(1000) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `to_uid` int(10) unsigned DEFAULT NULL,
  `notify` tinyint(1) unsigned NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `dyn_like_log` (
  `ip` varchar(40) NOT NULL,
  `dyn_id` smallint(5) unsigned NOT NULL,
  `location` varchar(50) DEFAULT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`ip`,`dyn_id`)
);

CREATE TABLE `dyn_record` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned DEFAULT 0,
  `topic` varchar(50) DEFAULT NULL,
  `content` varchar(1000) NOT NULL,
  `imgs` varchar(900) DEFAULT NULL,
  `commentCount` smallint(5) unsigned DEFAULT 0,
  `liked` smallint(5) unsigned DEFAULT 0,
  `time` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
);

CREATE TABLE `recommend` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `url` varchar(150) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
);







#select host,user,authentication_string from mysql.user;
/*==================Create Public User And Grant====================*/
create user 'public'@'127.0.0.1' identified by 'pawsllswa';
create user 'public'@'localhost' identified by 'pawsllswa';

grant select on Article.* to 'public'@'127.0.0.1';grant select on Article.* to 'public'@'localhost';
grant select on Note.* to 'public'@'127.0.0.1';grant select on Note.* to 'public'@'localhost';
grant select on Comment.* to 'public'@'127.0.0.1';grant select on Comment.* to 'public'@'localhost';
grant select on Tag.* to 'public'@'127.0.0.1';grant select on Tag.* to 'public'@'localhost';
grant select on Page.* to 'public'@'127.0.0.1';grant select on Page.* to 'public'@'localhost';
grant select on Dynamic.* to 'public'@'127.0.0.1';grant select on Dynamic.* to 'public'@'localhost';
grant select(name,avatar,sign,status) on User.me to 'public'@'127.0.0.1';grant select(name,avatar,sign,status) on User.me to 'public'@'localhost';

grant update(readCount,commentCount,liked) on Note.note_info to 'public'@'127.0.0.1';grant update(readCount,commentCount,liked) on Note.note_info to 'public'@'localhost';
grant update(readCount,commentCount,liked) on Article.article_info to 'public'@'127.0.0.1';grant update(readCount,commentCount,liked) on Article.article_info to 'public'@'localhost';
grant update(commentCount,liked) on Dynamic.dyn_record to 'public'@'127.0.0.1';grant update(commentCount,liked) on Dynamic.dyn_record to 'public'@'localhost';

grant insert on Comment.* to 'public'@'127.0.0.1';grant insert on Comment.* to 'public'@'localhost';
grant insert on Dynamic.dyn_comment to 'public'@'127.0.0.1';grant insert on Dynamic.dyn_comment to 'public'@'localhost';
grant insert on Dynamic.dyn_like_log to 'public'@'127.0.0.1';grant insert on Dynamic.dyn_like_log to 'public'@'localhost';

grant select,update,insert on User.user to 'public'@'127.0.0.1';grant select,update,insert on User.user to 'public'@'localhost';

grant select,update,insert on Tmp.* to 'public'@'127.0.0.1';grant select,update,insert on Tmp.* to 'public'@'localhost';

/*==================Create Limit User And Grant====================*/
create user 'limit'@'127.0.0.1' identified by 'lawsllswa';
create user 'limit'@'localhost' identified by 'lawsllswa';

grant select on *.* to 'limit'@'127.0.0.1';grant select on *.* to 'limit'@'localhost';
grant update,insert on Article.* to 'limit'@'127.0.0.1';grant update,insert on Article.* to 'limit'@'localhost';
grant update,insert on Note.* to 'limit'@'127.0.0.1';grant update,insert on Note.* to 'limit'@'localhost';
grant insert,delete on Tag.* to 'limit'@'127.0.0.1';grant insert,delete on Tag.* to 'limit'@'localhost';

/*==================Create Secret User And Grant====================*/
create user 'secret'@'127.0.0.1' identified by 'sawsllswa';
create user 'secret'@'localhost' identified by 'sawsllswa';

grant select on *.* to 'secret'@'127.0.0.1';grant select on *.* to 'secret'@'localhost';
grant update,insert,delete on Article.* to 'secret'@'127.0.0.1';grant update,insert,delete on Article.* to 'secret'@'localhost';
grant update,insert,delete on Note.* to 'secret'@'127.0.0.1';grant update,insert,delete on Note.* to 'secret'@'localhost';
grant update,insert,delete on Page.* to 'secret'@'127.0.0.1';grant update,insert,delete on Page.* to 'secret'@'localhost';
grant update,insert,delete on Dynamic.dyn_record to 'secret'@'127.0.0.1';grant update,insert,delete on Dynamic.dyn_record to 'secret'@'localhost';
grant update,insert,delete on Dynamic.recommend to 'secret'@'127.0.0.1';grant update,insert,delete on Dynamic.recommend to 'secret'@'localhost';
grant insert,delete on Tag.* to 'secret'@'127.0.0.1';grant insert,delete on Tag.* to 'secret'@'localhost';
grant update on User.* to 'secret'@'127.0.0.1';grant update on User.* to 'secret'@'localhost';
grant delete on Comment.* to 'secret'@'127.0.0.1';grant delete on Comment.* to 'secret'@'localhost';
grant delete on Dynamic.dyn_comment to 'secret'@'127.0.0.1';grant delete on Dynamic.dyn_comment to 'secret'@'localhost';
grant delete on Dynamic.dyn_like_log to 'secret'@'127.0.0.1';grant delete on Dynamic.dyn_like_log to 'secret'@'localhost';
grant drop,update,insert,delete on Tmp.* to 'secret'@'127.0.0.1';grant drop,update,insert,delete on Tmp.* to 'secret'@'localhost';

