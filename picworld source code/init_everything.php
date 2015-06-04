<?php
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);

	include('includes/init_config.inc.php');
	
	$query = "CREATE DATABASE IF NOT EXISTS	$dbname";
	
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("1. PicWorld faced some internal error. Please try after sometime.");
	}
	
	mysqli_select_db($db, $dbname);
	if(mysqli_connect_errno()) {
			die("2. PicWorld faced some internal error. Please try after sometime.");
	}
	
	// creating user
	$query = 'create table if not exists user(
		user_id integer auto_increment unique,
		fname varchar(50) not null,
		lname varchar(50),
		emailid varchar(100) primary key,
		user_passwd blob(50) not null,
		bday date,
		sex enum("M", "F", "O") not null,
		place_of_work varchar(50),
		location varchar(50),
		college varchar(150),
		description mediumtext
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("3. PicWorld faced some internal error. Please try after sometime.");
	}
	
	//creating album
	$query = 'create table if not exists album(
		aid integer auto_increment primary key,
		date_of_creat datetime not null,
		name varchar(64) default "my album",
		owner varchar(100) not null,
		description mediumtext,
		type enum("private", "public", "friends") default "public",
		foreign key(owner) references user(emailid) on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("5. PicWorld faced some internal error. Please try after sometime.");
	}
	
	// creating photo
	$query = 'create table if not exists photos(
		pid bigint primary key auto_increment,
		no_likes integer default 0,
		owner varchar(100) not null,
		aid integer not null,
		pic_path varchar(255),
		upload_date datetime not null,
		type enum("private", "public", "friends") default "private",
		story mediumtext,
		height integer,
		width integer,
		foreign key(aid) references album(aid)  on delete cascade  on update cascade,
		foreign key(owner) references user(emailid)  on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("6. PicWorld faced some internal error. Please try after sometime.");
	}
	
	// profile pics
	$query = 'create table if not exists profile_pics(
		owner varchar(100) primary key,
		pro_pic varchar(255) default "pictures/profile_pictures/default_dp_M.jpg" not null,
		set_date datetime,
		height integer default 200,
		width integer default 200,
		ppid bigint,
		foreign key(ppid) references photos(pid) on delete cascade  on update cascade,
		foreign key(owner) references user(emailid) on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("4. PicWorld faced some internal error. Please try after sometime.");
	}
	
	// creating friends
	$query = 'create table if not exists friends(
		sentby varchar(100) not null,
		acceptedby varchar(100) not null,
		requested_date date not null,
		accepted_date date not null,
		primary key(sentby, acceptedby),
		foreign key(sentby) references user(emailid)  on delete cascade on update cascade,
		foreign key(acceptedby) references user(emailid)  on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("7. PicWorld faced some internal error. Please try after sometime.");
	}
	
	// creating comments
	$query = 'create table if not exists comments(
		cid bigint primary key auto_increment,
		pid bigint,
		comment mediumtext,
		comment_by varchar(100),
		date_of_comment datetime,
		foreign key(comment_by) references user(emailid) on delete cascade on update cascade,
		foreign key(pid) references photos(pid) on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("8. PicWorld faced some internal error. Please try after sometime.");
	}
	
	// creating likes
	$query = 'create table if not exists likes(
		pid bigint,
		liked_by varchar(100),
		primary key(pid, liked_by),
		foreign key(liked_by) references user(emailid) on delete cascade on update cascade,
		foreign key(pid) references photos(pid) on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("9. PicWorld faced some internal error. Please try after sometime.");
	}
	
	
	// creating pending requests
	$query = 'create table if not exists pending(
		sent_by varchar(100),
		sent_to varchar(100),
		requested_date datetime,
		primary key(sent_by, sent_to),
		foreign key(sent_by) references user(emailid)  on delete cascade on update cascade,
		foreign key(sent_to) references user(emailid)  on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("10. PicWorld faced some internal error. Please try after sometime.");
	}
	
	// Notifications
	$query = 'create table if not exists notifications(
		notif_id bigint primary key auto_increment,
		emailid varchar(100),
		notification mediumtext,
		notif_date datetime,
		read_state boolean default false,
		foreign key(emailid) references user(emailid) on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("11. PicWorld faced some internal error. Please try after sometime.");
	}
	
	
	// activities 
	$query = 'create table if not exists activities(
		activity_id bigint primary key auto_increment,
		emailid varchar(100),
		activity mediumtext,
		activity_date datetime,
		foreign key(emailid) references user(emailid) on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("12. PicWorld faced some internal error. Please try after sometime.");
	}
	
	
	//Thumbnails
	
	$query = 'create table if not exists thumbs(
		thumbid bigint primary key auto_increment,
		thumb_path varchar(255),
		pid bigint unique,
		foreign key(pid) references photos(pid) on delete cascade on update cascade
	);';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("13. PicWorld faced some internal error. Please try after sometime.");
	}
	
	mysqli_close($db);
	echo "$dbname has been sucessfully set up on your server!";
?>
