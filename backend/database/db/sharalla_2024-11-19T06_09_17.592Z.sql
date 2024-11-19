CREATE TABLE `tags` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`label` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE `content_types` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`label` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE `users` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`email` VARCHAR(255) NOT NULL,
	`username` VARCHAR(255) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`last_login` TIMESTAMP() NOT NULL,
	`rank` INTEGER() NOT NULL,
	`avatar` VARCHAR(255) NOT NULL,
	`signup_date` TIMESTAMP() NOT NULL,
	`auth_token` VARCHAR(64),
	PRIMARY KEY(`id`)
);


CREATE TABLE `files` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`title` VARCHAR(255) NOT NULL,
	`description` VARCHAR()(2000) NOT NULL,
	`size` INTEGER() NOT NULL COMMENT 'files size',
	`path` VARCHAR()(255) NOT NULL,
	`upload_date` TIMESTAMP(),
	`uploader_id` INTEGER() NOT NULL,
	`extension_id` INTEGER() NOT NULL,
	`type_id` INTEGER() NOT NULL COMMENT 'reference content type id',
	PRIMARY KEY(`id`)
);


CREATE TABLE `comments` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`content` VARCHAR()(2000) NOT NULL,
	`author_id` INTEGER() NOT NULL,
	`file_id` INTEGER() NOT NULL,
	`post_date` TIMESTAMP(),
	PRIMARY KEY(`id`)
);


CREATE TABLE `ranks` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`label` VARCHAR(255) NOT NULL,
	`power` INTEGER() NOT NULL DEFAULT 0 COMMENT '0-10 invite
11-20 user
>50 moderator
100 admin',
	PRIMARY KEY(`id`)
);


CREATE TABLE `friends` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`user_id` INTEGER() NOT NULL,
	`friend_id` INTEGER() NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE `extension` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`label` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE `file_tags` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`file_id` INTEGER() NOT NULL,
	`tag_id` INTEGER() NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE `liked_content` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`user_id` INTEGER() NOT NULL,
	`file_id` INTEGER() NOT NULL,
	PRIMARY KEY(`id`)
) COMMENT='Count like on given file id';


CREATE TABLE `bookmarks` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`user_id` INTEGER() NOT NULL,
	`file_id` INTEGER() NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE `followed` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`user_id` INTEGER(),
	`followed_id` INTEGER(),
	PRIMARY KEY(`id`)
);


CREATE TABLE `status` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`label_id` INTEGER() NOT NULL,
	`user_id` INTEGER() NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE `status_label` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`label` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE `message` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`content` VARCHAR(2000) NOT NULL,
	`sender_id` INTEGER() NOT NULL,
	`recipient_id` INTEGER() NOT NULL,
	`send_date` TIMESTAMP() NOT NULL,
	`is_read` BOOLEAN() NOT NULL,
	PRIMARY KEY(`id`)
);


ALTER TABLE `friends`
ADD FOREIGN KEY(`user_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `friends`
ADD FOREIGN KEY(`friend_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `files`
ADD FOREIGN KEY(`uploader_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `files`
ADD FOREIGN KEY(`extension_id`) REFERENCES `extension`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `comments`
ADD FOREIGN KEY(`author_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `comments`
ADD FOREIGN KEY(`file_id`) REFERENCES `files`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `files`
ADD FOREIGN KEY(`type_id`) REFERENCES `content_types`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `file_tags`
ADD FOREIGN KEY(`file_id`) REFERENCES `files`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `file_tags`
ADD FOREIGN KEY(`tag_id`) REFERENCES `tags`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `users`
ADD FOREIGN KEY(`rank`) REFERENCES `ranks`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `bookmarks`
ADD FOREIGN KEY(`user_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `bookmarks`
ADD FOREIGN KEY(`file_id`) REFERENCES `files`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `liked_content`
ADD FOREIGN KEY(`file_id`) REFERENCES `files`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `liked_content`
ADD FOREIGN KEY(`user_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `followed`
ADD FOREIGN KEY(`user_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `followed`
ADD FOREIGN KEY(`followed_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `status`
ADD FOREIGN KEY(`label_id`) REFERENCES `status_label`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `status`
ADD FOREIGN KEY(`user_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `message`
ADD FOREIGN KEY(`sender_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `message`
ADD FOREIGN KEY(`recipient_id`) REFERENCES `users`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;