DROP TABLE `prefix_picalbums_album`;
DROP TABLE `prefix_picalbums_picture`;
DROP TABLE `prefix_picalbums_heart`;
DROP TABLE `prefix_picalbums_note`;
DROP TABLE `prefix_picalbums_settings`;
DROP TABLE `prefix_picalbums_blacklist`;
DROP TABLE `prefix_picalbums_related`;
DROP TABLE `prefix_picalbums_tag`;
DELETE FROM `prefix_comment` WHERE `target_type` = 'picalbums';
DELETE FROM `prefix_comment_online` WHERE `target_type` = 'picalbums';


