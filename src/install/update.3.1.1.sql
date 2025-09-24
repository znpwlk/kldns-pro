CREATE TABLE `kldns_point_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(40) NOT NULL,
  `uid` int unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `point` int NOT NULL DEFAULT 0,
  `status` tinyint NOT NULL DEFAULT 0,
  `pay_type` varchar(20) NOT NULL DEFAULT 'epay',
  `trade_no` varchar(64) DEFAULT NULL,
  `notify_data` text,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;