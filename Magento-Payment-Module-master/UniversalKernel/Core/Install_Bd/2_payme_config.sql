CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}payme_config` (
  `kass_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `merchant_id` char(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Идентификатор касса',
  `merchant_key` char(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Key',
  `merchant_key_test` char(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Test Key',
  `endpoint_url` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'o	Опциональный выбор callback url ',
  `redirect` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Перенаправление URL',
  `endpoint_url_pay_sys` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'размещения ссылки на сайт интернет магазина',
  `is_flag_test` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'o	Вкл/Выкл тестового режима',
  `is_flag_send_tovar` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL COMMENT 'o	Вкл/Выкл отправки данных о товарах (объект Detail в форме отправки)',
  `callback_timeout` int(11) NOT NULL DEFAULT '0' COMMENT 'Вернуться после оплаты через: 15, 30, 60 сик',
  PRIMARY KEY (`kass_id`),
  UNIQUE KEY `merchant_id` (`merchant_id`),
  UNIQUE KEY `merchant_key` (`merchant_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Параметры метода' AUTO_INCREMENT=2 ;