ALTER TABLE `events` ADD COLUMN details BLOB;
ALTER TABLE `smtp` ADD COLUMN ignore_cert_errors BOOLEAN;
ALTER TABLE `results` ADD COLUMN position VARCHAR(255);
ALTER TABLE `pages` ADD COLUMN capture_credentials BOOLEAN;
ALTER TABLE `pages` ADD COLUMN capture_passwords BOOLEAN;

ALTER TABLE `campaigns` ADD COLUMN smtp_id bigint;
-- Create a new table to store smtp records
DROP TABLE `smtp`;
CREATE TABLE `smtp`(
	id integer primary key auto_increment,
	user_id bigint,
	interface_type varchar(255),
	name varchar(255),
	host varchar(255),
	username varchar(255),
	password varchar(255),
	from_address varchar(255),
	modified_date datetime,
	ignore_cert_errors BOOLEAN
);

ALTER TABLE `pages` ADD COLUMN redirect_url VARCHAR(255);


ALTER TABLE `campaigns` ADD COLUMN launch_date DATETIME;

UPDATE `campaigns` SET launch_date = created_date;

ALTER TABLE `attachments` MODIFY content LONGTEXT;

UPDATE `results`
SET status = "Submitted Data"
WHERE id IN (
        SELECT results_tmp.id
        FROM (SELECT * FROM results) AS results_tmp, events
        WHERE results.status = "Success"
                AND events.message="Submitted Data"
                AND results_tmp.email = events.email
                AND results_tmp.campaign_id = events.campaign_id);

UPDATE `results`
SET status = "Clicked Link"
WHERE id IN (
        SELECT results_tmp.id
        FROM (SELECT * FROM results) as results_tmp, events
        WHERE results_tmp.status = "Success"
                AND events.message="Clicked Link"
                AND results_tmp.email = events.email
                AND results_tmp.campaign_id = events.campaign_id);

                CREATE TABLE IF NOT EXISTS `headers` (
	id integer primary key auto_increment,
	`key` varchar(255),
	`value` varchar(255),
	`smtp_id` bigint
);

UPDATE `campaigns` SET `created_date`=CONVERT_TZ(`created_date`, @@session.time_zone, '+00:00');
UPDATE `campaigns` SET `completed_date`=CONVERT_TZ(`completed_date`, @@session.time_zone, '+00:00');
UPDATE `campaigns` SET `launch_date`=CONVERT_TZ(`launch_date`, @@session.time_zone, '+00:00');
UPDATE `events` SET `time`=CONVERT_TZ(`time`, @@session.time_zone, '+00:00');
UPDATE `groups` SET `modified_date`=CONVERT_TZ(`modified_date`, @@session.time_zone, '+00:00');
UPDATE `templates` SET `modified_date`=CONVERT_TZ(`modified_date`, @@session.time_zone, '+00:00');
UPDATE `pages` SET `modified_date`=CONVERT_TZ(`modified_date`, @@session.time_zone, '+00:00');
UPDATE `smtp` SET `modified_date`=CONVERT_TZ(`modified_date`, @@session.time_zone, '+00:00');

CREATE TABLE IF NOT EXISTS `mail_logs` (
    `id` integer primary key auto_increment,
    `campaign_id` integer,
    `user_id` integer,
    `send_date` datetime,
    `send_attempt` integer,
    `r_id` varchar(255),
    `processing` boolean);



ALTER TABLE `results` ADD COLUMN reported boolean default 0;
ALTER TABLE `results` ADD COLUMN send_date DATETIME;

ALTER TABLE `results` ADD COLUMN modified_date DATETIME;

UPDATE `results`
    SET `modified_date`= (
        SELECT max(events.time) FROM events
        WHERE events.email=results.email
        AND events.campaign_id=results.campaign_id
    );

CREATE TABLE IF NOT EXISTS `email_requests` (
    `id` integer primary key auto_increment,
    `user_id` integer,
    `template_id` integer,
    `page_id` integer,
    `first_name` varchar(255),
    `last_name` varchar(255),
    `email` varchar(255),
    `position` varchar(255),
    `url` varchar(255),
    `r_id` varchar(255),
    `from_address` varchar(255)
);
ALTER TABLE `templates` MODIFY html MEDIUMTEXT;
ALTER TABLE `pages` MODIFY html MEDIUMTEXT;
ALTER TABLE `campaigns` ADD COLUMN send_by_date DATETIME;

CREATE TABLE IF NOT EXISTS `roles` (
    `id`          INTEGER PRIMARY KEY AUTO_INCREMENT,
    `slug`        VARCHAR(255) NOT NULL UNIQUE,
    `name`        VARCHAR(255) NOT NULL UNIQUE,
    `description` VARCHAR(255)
);

ALTER TABLE `users` ADD COLUMN `role_id` INTEGER;

CREATE TABLE IF NOT EXISTS `permissions` (
    `id`          INTEGER PRIMARY KEY AUTO_INCREMENT,
    `slug`        VARCHAR(255) NOT NULL UNIQUE,
    `name`        VARCHAR(255) NOT NULL UNIQUE,
    `description` VARCHAR(255)
);


CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id`       INTEGER NOT NULL,
    `permission_id` INTEGER NOT NULL
);

INSERT INTO `roles` (`slug`, `name`, `description`)
VALUES
    ("admin", "Admin", "System administrator with full permissions"),
    ("user", "User", "User role with edit access to objects and campaigns");

INSERT INTO `permissions` (`slug`, `name`, `description`)
VALUES
    ("view_objects", "View Objects", "View objects in Gophish"),
    ("modify_objects", "Modify Objects", "Create and edit objects in Gophish"),
    ("modify_system", "Modify System", "Manage system-wide configuration");

-- Our rules for generating the admin user are:
-- * The user with the name `admin`
-- * OR the first user, if no `admin` user exists
-- MySQL apparently makes these queries gross. Thanks MySQL.
UPDATE `users` SET `role_id`=(
    SELECT `id` FROM `roles` WHERE `slug`="admin")
WHERE `id`=(
    SELECT `id` FROM (
        SELECT * FROM `users`
    ) as u WHERE `username`="admin"
    OR `id`=(
        SELECT MIN(`id`) FROM (
            SELECT * FROM `users`
        ) as u
    ) LIMIT 1);

-- Every other user will be considered a standard user account. The admin user
-- will be able to change the role of any other user at any time.
UPDATE `users` SET `role_id`=(
    SELECT `id` FROM `roles` AS role_id WHERE `slug`="user")
WHERE role_id IS NULL;

-- Our default permission set will:
-- * Allow admins the ability to do anything
-- * Allow users to modify objects

-- Allow any user to view objects
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles AS r, `permissions` AS p
WHERE r.id IN (SELECT `id` FROM roles WHERE `slug`="admin" OR `slug`="user")
AND p.id=(SELECT `id` FROM `permissions` WHERE `slug`="view_objects");

-- Allow admins and users to modify objects
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles AS r, `permissions` AS p
WHERE r.id IN (SELECT `id` FROM roles WHERE `slug`="admin" OR `slug`="user")
AND p.id=(SELECT `id` FROM `permissions` WHERE `slug`="modify_objects");

-- Allow admins to modify system level configuration
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles AS r, `permissions` AS p
WHERE r.id IN (SELECT `id` FROM roles WHERE `slug`="admin")
AND p.id=(SELECT `id` FROM `permissions` WHERE `slug`="modify_system");
ALTER TABLE `pages` MODIFY redirect_url TEXT;