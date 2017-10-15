
CREATE TABLE `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` bigint(20) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `urls` text,
  `clientid` varchar(32) DEFAULT NULL,
  `secret` varchar(64) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE `content` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) DEFAULT '-1',
  `author` bigint(20) DEFAULT '-1',
  `category` int(11) DEFAULT '-1',
  `published` datetime DEFAULT NULL,
  `lang` varchar(8) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `content` VALUES (1,-1,-1,-1,'2016-05-13 22:21:35','en','home','Veev Framework','<p>Minimalistic php + Node JS framework for rapid web weaving</p>'),(2,-1,-1,-1,'2016-05-14 11:47:13','en','about','About Us','<p>About Us content here</p>'),(3,-1,-1,-1,'2016-05-14 11:58:08','en','contact','Contact Us','<p>vishva@villvay.com</p>\r\n<p>094 77 944 79 15</p>');

CREATE TABLE `login` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `cookie` varchar(32) DEFAULT NULL,
  `remember` int(1) NOT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `session` varchar(32) DEFAULT NULL,
  `useragent` text,
  PRIMARY KEY (`id`)
);

CREATE TABLE `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `domain` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `organization` VALUES (1,'VILLVAY.com','villvay.com','vishva@villvay.com'),(2,'VILLVAY.org','villvay.org','opensource@villvay.org');

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `organization` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `lang` varchar(8) NOT NULL DEFAULT 'en',
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC',
  `auth` text,
  `reset_code` varchar(50) NOT NULL,
  `groups` text,
  `google_id` varchar(40) DEFAULT NULL,
  `google_token` text,
  `picture` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `user` VALUES (1,1,'admin','c5d06a24b81f64ecd21a66e3cd8940a1','admin@tinyfx.com','en','Asia/Kolkata','{\"dashboard\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"dashboard/developer\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"admin\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"index\":[\"view\"],\"user\":[\"view\"]}','','4',NULL,NULL,NULL),(2,1,'user','cdf6db7a570d6a469c4f2f1763ea4dc1','0','en','Asia/Kolkata','{\"admin/developer\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"index\":[\"view\"],\"user\":[\"view\",\"edit\"]}','','16',NULL,NULL,NULL),(4,1,'Admins','[GROUP]','','en','UTC','{\"admin\":[\"view\"],\"admin/tunnel\":[\"view\"],\"admin/developer\":[\"view\"],\"index\":[\"view\"],\"user\":[\"view\"]}','',NULL,NULL,NULL,NULL),(9,1,'Vishva Kumara','90b09d81b2bb3b2d0bf65ba3d1699f3b','vishva@villvay.com','en','Asia/Kolkata','{\"admin\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"admin/developer\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"index\":[\"view\"],\"dashboard\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"dashboard/developer\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"user\":[\"view\"]}','','16','111215713319316327512','{\"access_token\":\"ya29.GluZBLe2_5DTN6oZW6WB7z1vJZSNbgoFkfFfpPh3aFze6jwQpDf9D93YTLPuUbtOL6qLUPoz5dG6IxrqJUF36YDo_nO9lVPSYUPbYj_qIhDixamO03DFni5lZ-jB\",\"expires_in\":3600,\"id_token\":\"eyJhbGciOiJSUzI1NiIsImtpZCI6IjY3ODU2OGM4YWRiMmVjYzA3ZDE0M2RiNTE0Y2M3YTk5NTIwN2RmMzYifQ.eyJhenAiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTEyMTU3MTMzMTkzMTYzMjc1MTIiLCJoZCI6InZpbGx2YXkuY29tIiwiZW1haWwiOiJ2aXNodmFAdmlsbHZheS5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwiYXRfaGFzaCI6IktZcnl2ckt3S1plV2RjWjk2N1poSXciLCJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiaWF0IjoxNTAxNDkzOTQ1LCJleHAiOjE1MDE0OTc1NDV9.jm0IZPxUc996vmknkWP1mHNGv9xp-_pySooQgzomVAUftgG666cc9wC0PsHtQd5Utm6TOIdHmvtpVA7tFpHKh4RB8Ru9ZikqQawOTBmKMjRXQVr3oyk1FWnIDpkAv3gh5o0sUMtxLs-H5u6e57GApMAY_iQ4WvxkZkivo6ArDdvtHzDhjiBHIS2TtriszXJGxUgJQsJrmjD-XZq5rSdUl9OxNxBBwNpjaaj-SBvF1TXhxrrWRr5WtucZY8hULKwOpaOlumg3OxHTHQxbcwUYCnXY4i1IdkQiz8fL_jGA5MpgmK-G8QU3_sN1hss5nGMO5qXgvgph8Y9DyCDTtFMCrA\",\"refresh_token\":\"1/m6-UP7Y4Tq7PI3MLJFKDPMUxtmY4Z-Rl7BZ5kjjqOrw\",\"token_type\":\"Bearer\",\"created\":1501493945}','https://lh4.googleusercontent.com/-DE8Gp7AG4UM/AAAAAAAAAAI/AAAAAAAABu4/jRv9Ioq2MFk/photo.jpg'),(10,4,'Vishva Kumara','','vishva@villvay.org','en','Asia/Kolkata','{\"dashboard\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"]}','',NULL,'111678590146927628788','{\"access_token\":\"ya29.GlsXBHcOlpoX3U7H66TEqwgrMQA7eJOgJmxVYVkgJ_itgYhAJhrIXW-GxA62wqZYOxumm2o_R2r6hW_7zL67Iw-0xDoG7ZrJoFjuVn9QOBdC4tTI8RllcfieDMsR\",\"expires_in\":3600,\"id_token\":\"eyJhbGciOiJSUzI1NiIsImtpZCI6IjU5MzNhNmI0ZGFjNTRkZjIxMDBmYTE3OWNiZjVhMDE4ZTY4NTQ2YTcifQ.eyJhenAiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTE2Nzg1OTAxNDY5Mjc2Mjg3ODgiLCJoZCI6InZpbGx2YXkub3JnIiwiZW1haWwiOiJ2aXNodmFAdmlsbHZheS5vcmciLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwiYXRfaGFzaCI6IlpHaVhMeW5EdHBkcHMtZ2R6ZEZCUkEiLCJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiaWF0IjoxNDkwMjYxNTkwLCJleHAiOjE0OTAyNjUxOTB9.DCCBRXnqWMl_DURD3DcYmg029uFgr44CfxG8tEO1vVYItM4jeEZEu8dce0vetFbqjM1ihp5Al2wZuN-TZZo0tiTqXb3xAGrvx3PHH70P4eucZdgW67sP1hBCjrwcjGijINaVSa41bP8tuV81eAupopus9tFCLyVOdBGIv91AswNj_d1Z34mg-lCSuI1DnwYrXAxONsnRVNn9BHG4FMh035mQDfWYCT8lmN7Mxp6e-4FEH9c8p9D9xBW_12yLaplDrXjzM3hv0iLxTtJv70EM7ZAB9kuD86wb6HIwgI9eDE_uVz6EDsRFNsnvE7CoBJPaxJ_pTLfnSmizWuUMDv1z7Q\",\"refresh_token\":\"1/GBuKMs8ksX_dXBTOKS-kN5jPqFY4m-zWO03QtFgtw9k\",\"token_type\":\"Bearer\",\"created\":1490261591}','https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg'),(11,5,'Vishva Kumara','','vishva8kumara@gmail.com','en','UTC','{\"dashboard\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"]}','',NULL,'115006889285175629164','{\"access_token\":\"ya29.GlsVBB484UCujZ-y2tNnZwf-o3-gm_PPuC29xFtshwqrsXqIXnRx7_NVBlhVfIscjXAwomPIDxzC-TOTH-aYrrpixmPGqIzdjQvbqDgE-UD8aQHdkRjB3y9vyyhE\",\"expires_in\":3600,\"id_token\":\"eyJhbGciOiJSUzI1NiIsImtpZCI6IjQ5N2Y2MGIxMjRhNjc1NDI2NDhlYjIzYjc0YmY4YTg2MDJkY2I4YTYifQ.eyJhenAiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTUwMDY4ODkyODUxNzU2MjkxNjQiLCJlbWFpbCI6InZpc2h2YThrdW1hcmFAZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImF0X2hhc2giOiI3b0FucV9tX3VQS0tBWjFiZVJvM29nIiwiaXNzIjoiYWNjb3VudHMuZ29vZ2xlLmNvbSIsImlhdCI6MTQ5MDA4NDA3MywiZXhwIjoxNDkwMDg3NjczfQ.x9oFx109sKicB6YrS6cYlL9fvkc_Yl8G4fVPIvDl4AZ1nqYiu8QjWzfrZ3TTvGtx_jc9V1_vSkzmixcFVXPofAJu3eevPYNRii27Tz_9Nal7wsHM9SXDTe-BqRbeXInxCYmK0fQrqV1vCvOt9VXZZleCuyGZvESTgZTKUOVaFguSSp0Gk7zZR9H49hJXsmrqY8-glrmsjIOjxp3ufkyFX5eILuUoH86Vs65lfWN-YtPpexJ9sCOf1ODcZr5syCkiR4i1rjIZcgtvLY2FRlcve1O3xUUAfwNgofezU_2O8UMoU_Es-x1BN0nBARlsMQ35CdA43axA9sPRf5N_Ld9v4A\",\"refresh_token\":\"1/6c0w-rEvhIYIK6-x2hwM-a2jdwRZyGClyj0DzUFbXR0\",\"token_type\":\"Bearer\",\"created\":1490084073}','https://lh4.googleusercontent.com/-V5niVtQAJCo/AAAAAAAAAAI/AAAAAAAAB6w/J88nil5IvsQ/photo.jpg'),(12,6,'','','teacher_i2k8b-m9hdidvvm2pl2-@classroom-dev.com','en','UTC','{\"dashboard\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"]}','',NULL,'105632909752357496229','{\"access_token\":\"ya29.GlsVBNByJHUraqZIWMmJLfhO2pV_7YOPzrHKz5sPw2fQz8uebdOuFBUmdmfHv6DzE8QcSnrIU8zb8kQTSE0eweCkvHVP_fWi6J803xkxDH27U0aWXciMMD2MYAcs\",\"expires_in\":3600,\"id_token\":\"eyJhbGciOiJSUzI1NiIsImtpZCI6IjQ5N2Y2MGIxMjRhNjc1NDI2NDhlYjIzYjc0YmY4YTg2MDJkY2I4YTYifQ.eyJhenAiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMDU2MzI5MDk3NTIzNTc0OTYyMjkiLCJoZCI6ImNsYXNzcm9vbS1kZXYuY29tIiwiZW1haWwiOiJ0ZWFjaGVyX2kyazhiLW05aGRpZHZ2bTJwbDItQGNsYXNzcm9vbS1kZXYuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImF0X2hhc2giOiJ0ZUNXNjRKZGNIUHlHM0VMUmdEOVF3IiwiaXNzIjoiYWNjb3VudHMuZ29vZ2xlLmNvbSIsImlhdCI6MTQ5MDA4NDYwMywiZXhwIjoxNDkwMDg4MjAzfQ.X0SJE9GOu1MGMpjrZFwf-Uq2afo9009Cz8jvIw_PIYsNaVmJgddL8LH5TFW0R-FE3lVIu2vhjHbVbzaJ-g-w4G0zUk7wWzfUcxGjnfvGTOPtplM0XbkGNnB_CQ2EvNw4akGMpG1INaY5zp9B6B67QE4xbV6ofzkTqBH1T13qRBRjI6gy0cIS4AYhbitNxOOisO4znbuqBjf-jEsLajW4ipS-p3JK8H7I-3bj1jry_T3rM-CQNxA4xyuzSRQkd4EV_gP2jn8G97Yt7558LI_hqM6PfiTzvhlOW8sy5GaSoPLJNFYbdeMXARAPFVjJCElxbPkh7NAzQ09ViN6I2qiOeA\",\"refresh_token\":\"1/a555oXKdk99Nz520RZFblMSJJU7cKzAIQREXVqHobJR9DvB1F9oAWEEiNTP9GkRd\",\"token_type\":\"Bearer\",\"created\":1490084603}','https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg'),(13,0,'Chathura Kudahetti','chathura@villvay.org','','en','UTC',NULL,'',NULL,'114478245620782988493',NULL,NULL),(14,4,'','','shiran@villvay.org','en','UTC','{\"dashboard\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"]}','',NULL,'113665962544066845656','{\"access_token\":\"ya29.GlsYBESGjBFfDvhtJKbB0ntEQVmkXM3U_pPOvifsmmry1LZYi_dL60rGld1wHWP_lsRdJXLTwsHek6vYUTdWUpxjon9Mx8WJbQdnlIDsFgTnd8D21O6wmGpH2ykI\",\"expires_in\":3600,\"id_token\":\"eyJhbGciOiJSUzI1NiIsImtpZCI6Ijg5ODY1ZDBjOTJjYjI0ZDk4NmExMTU5MjU2YzBmZGQzMTBmOWRlNzAifQ.eyJhenAiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTM2NjU5NjI1NDQwNjY4NDU2NTYiLCJoZCI6InZpbGx2YXkub3JnIiwiZW1haWwiOiJzaGlyYW5AdmlsbHZheS5vcmciLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwiYXRfaGFzaCI6InRPV3U1QzVTTVR4M3ZIQkU2bk1jaHciLCJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiaWF0IjoxNDkwMzQ1NTE0LCJleHAiOjE0OTAzNDkxMTR9.C9x7v0uhBcZ37kteLwa8vTUZo1vEVF7cW7_AsAT0rjMYV6BEgWJy61T74Ihl5_PzI4vluoTBUX3dMxN6IQiIa4hyCaXFX9U0Rm3EDpTSk7m7b_Uyn_0ed0P3RBMSuH5qvYtKLoVrPAoxftaTzQyQh-zxlZAGPJS6qP5-VO_uJw7hGEiPi1nwTHmC7m7GhnjS0DDg2Tceh5mghOmMMhQIaLuIx4ZK3HiQ3DmFXvaMa71D9sAVptI_HmcN7mp3QgtJ9XT7SuHEYy9RV2r5gB6uAlkACfkz4fVxamwW1ZqaT8MvxJ7AcCj7lGrtPybJ1GhLWtIlSxfwOTh88YW4Kp9Ccg\",\"refresh_token\":\"1/4z_O5jmJnZaNu_ZWaZnAcFA5XDRhkuHkOHxXcr5dZHA\",\"token_type\":\"Bearer\",\"created\":1490345514}','https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg'),(15,7,'','','jeff@krauseinnovationcenter.org','en','UTC','{\"dashboard\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"dashboard/developer\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"admin\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"index\":[\"view\"],\"user\":[\"view\"]}','',NULL,'106245455711565449601','{\"access_token\":\"ya29.GluZBAFiMrdKZ4FqIkx4Gul7QkqwbVtNamTaSiav8OGncwaBR0sZkNm2uq87YhS-hgCVTbu1GmPDmzvBeALdW4rlIEmYMX1IV31xiddEp8wvrukIY7hXqJqZErNB\",\"expires_in\":3600,\"id_token\":\"eyJhbGciOiJSUzI1NiIsImtpZCI6IjY3ODU2OGM4YWRiMmVjYzA3ZDE0M2RiNTE0Y2M3YTk5NTIwN2RmMzYifQ.eyJhenAiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMDYyNDU0NTU3MTE1NjU0NDk2MDEiLCJoZCI6ImtyYXVzZWlubm92YXRpb25jZW50ZXIub3JnIiwiZW1haWwiOiJqZWZmQGtyYXVzZWlubm92YXRpb25jZW50ZXIub3JnIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImF0X2hhc2giOiJiNkVUU0w3V3ExaTNYM3R2eVlMTGZ3IiwiaXNzIjoiYWNjb3VudHMuZ29vZ2xlLmNvbSIsImlhdCI6MTUwMTQ4MzgwMywiZXhwIjoxNTAxNDg3NDAzfQ.fa3EFZP9GXtV4zyF7vePV91L9wgjXAoBorvMZNe4eQJ6J_99qkDIr8EmyBCHhzPo1hjDZUp_LhWY7NM776yTjvOt7Qi3knAQz3AYrvYvJgs_1AOetZslGAYcTnRODr1vvGXhKxhT4ldzhwHXbmaJEviDOAJI2yBN2DLLURKOrnZJik3X2eTZPVTC8s_Q9Wu1-mFdqkUO_Ufnfw4or00syOeiz7D8DH_btAan9n16eQhS4VzAvBzqBq8z2Sv5eo10JJTyPV_aOP-6p8BYjStwvIkfxV64Qqr9DokQJp1A2Uq22AHdJ0Tyl-PB5OAnOJ0Yr5O-06KJ6CXTNkGBJdlpUg\",\"refresh_token\":\"1/D4ypMAu_ZAICqLQYRuKYlu665VjwVo_UCqiOenlNbLU\",\"token_type\":\"Bearer\",\"created\":1501483803}','https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg'),(16,1,'Teachers','[GROUP]','','en','UTC','{\"index\":[\"view\"],\"user\":[\"view\"]}','',NULL,NULL,NULL,NULL),(17,1,'Students','[GROUP]','','en','UTC','{\"index\":[\"view\"],\"user\":[\"view\"]}','',NULL,NULL,NULL,NULL),(18,1,'Vasana Pathirana','','vasana.pathirana@gmail.com','en','Asia/Kolkata','{\"admin\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"index\":[\"view\"],\"dashboard\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"dashboard/developer\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"user\":[\"view\"]}','','16','116839159568590893082','{\"access_token\":\"ya29.GluhBNKY69q2a3u_z-VYDfpOlQn8bnnPCYhQEIQD3_sx5kmUtQh9qVn4qE152qv4wxQ_MEG7uLT2_mktqosfayaJprUNsby_AUE9S-lJ7c8bVxVqb9d5Js45hWHv\",\"expires_in\":3600,\"id_token\":\"eyJhbGciOiJSUzI1NiIsImtpZCI6IjNkODU5NTYxODE5YzY3MGM4MzQ5MTk4ZjQxY2UxMjAzN2VhZmJjODYifQ.eyJhenAiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIzODU5ODQ5MjY0ODQtaWgxaWdrNWU2YW0wcWw3b3A1aHFmMWFkMHNiM2RwZTUuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTY4MzkxNTk1Njg1OTA4OTMwODIiLCJlbWFpbCI6InZhc2FuYS5wYXRoaXJhbmFAZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImF0X2hhc2giOiJMMW5RRjF3SDJfb09QblZMRVVjdnF3IiwiaXNzIjoiYWNjb3VudHMuZ29vZ2xlLmNvbSIsImlhdCI6MTUwMjE3MjUyMiwiZXhwIjoxNTAyMTc2MTIyfQ.WqAlU_4Qmwbgl05V3kvxra2zeOsJC3M6wWykGhz0yYRLcAvvPnmy6YDDveOxLdkM1zAoRcYhlE30_v4B62d2QUGj2BuajRzO-d2RBgiyuR541iVUPLdA1XwNGWjxVVXYlCVsLnAi5w3MV0rmmm5e4mW-I58tW23y2yiseZnf5i8jmigN4_1sdEvyPCvHo6kM7LLCNmHjS_1-pkoz98z8Hp5TsDT6EFRj8owwrdVF5LFiSd7NgEeCEyW5qRMiAGx6WzsdxRVlZEZzgab6JApUQs0tiZ7IJVJ0d-i_AdomKga7v_I5SYZUKd6Fpme_-jU2pJBBjMnxvdqbMP8LKvfOkw\",\"refresh_token\":\"1/Q_WwDoS5NpjwoBYySrvWCQKEbw-oXKy7wPcFx68jaX0\",\"token_type\":\"Bearer\",\"created\":1502172522}','https://lh6.googleusercontent.com/-bNzmEJ2GwH8/AAAAAAAAAAI/AAAAAAAAANU/bek4sTk040E/photo.jpg');

