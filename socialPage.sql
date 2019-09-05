CREATE TABLE `engine_module_social_page` (
  `id` int(11) NOT NULL,
  `socialId` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

ALTER TABLE `engine_module_social_page`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `engine_social_publishing_status` ADD `pageId` INT(11) NOT NULL AFTER `status`;

CREATE TABLE `engine_module_instagram_image` (
  `id` int(11) NOT NULL,
  `image` text NOT NULL,
  `instagramId` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `engine_module_instagram_image`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `engine_module_instagram_image` ADD `pageSocialId` VARCHAR(255) NOT NULL AFTER `instagramId`;

CREATE TABLE `engine_module_instagram_images_holder` (
  `id` int(11) NOT NULL,
  `pageSocialId` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `engine_module_instagram_images_holder`
  ADD PRIMARY KEY (`id`);