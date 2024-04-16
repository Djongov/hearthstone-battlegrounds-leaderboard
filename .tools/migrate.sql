CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(512) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `last_ips` text,
  `origin_country` varchar(25) DEFAULT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime,
  `theme` varchar(20) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cache` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` varchar(5000) COLLATE utf8mb4_general_ci NOT NULL,
  `expiration` datetime NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `unique_property` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `firewall` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_cidr` varchar(256) NOT NULL,
  `created_by` varchar(1000) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `comment` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

INSERT INTO `firewall`(`ip_cidr`, `created_by`, `created_at`, `comment`)
VALUES 
    ('127.0.0.1/32', 'System', NOW(), 'private range'),
    ('10.0.0.0/8', 'System', NOW(), 'private range'),
    ('172.16.0.0/12', 'System', NOW(), 'private range'),
    ('192.168.0.0/16', 'System', NOW(), 'private range');

CREATE TABLE `csp_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `data` json NOT NULL,
  `domain` varchar(60) DEFAULT NULL,
  `url` varchar(2500) DEFAULT NULL,
  `referrer` varchar(2500) DEFAULT NULL,
  `violated_directive` text,
  `effective_directive` varchar(2500) DEFAULT NULL,
  `original_policy` varchar(5000) DEFAULT NULL,
  `disposition` varchar(60) DEFAULT NULL,
  `blocked_uri` text,
  `line_number` int DEFAULT NULL,
  `column_number` int DEFAULT NULL,
  `source_file` varchar(1500) DEFAULT NULL,
  `status_code` int DEFAULT NULL,
  `script_sample` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `csp_approved_domains` (
  `id` int NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `created_by` varchar(60) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `system_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `client_ip` varchar(256) NOT NULL,
  `user-agent` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `uri` text NOT NULL,
  `method` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;