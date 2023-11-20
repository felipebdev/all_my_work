-- email settings

INSERT INTO `cache_entries` (`id`, `name`, `description`, `default_value`, `created_at`, `updated_at`)
VALUES (1,'MAIL_PROVIDER_NAME','Provedor de e-mail utilizado','mailtrap',NULL,'2021-09-16 22:14:00');

INSERT INTO `email_providers` (`id`, `name`, `description`, `from_name`, `from_address`, `service_tags`, `driver`, `settings`, `created_at`, `updated_at`)
VALUES (1,'mailtrap','Mailtrap','From_name_goes_here','from_address@xgrow.com','[\"\"]','smtp','{\"host\": \"smtp.mailtrap.io\", \"port\": \"2525\", \"password\": \"PASSWORD\", \"username\": \"USERNAME\", \"_comment_\": \"encryption and headers are optional\", \"encryption\": \"tls\"}','2021-09-16 22:13:54','2021-09-16 22:13:54');

-- XGrow settings

INSERT INTO `configs` (`id`, `bank`, `branch`, `account`, `created_at`, `updated_at`, `name`, `email`, `document`, `recipient_id`)
VALUES (1,'341','0268','998754','2020-11-05 11:26:54','2021-08-31 03:47:31','XGROW TECNOLOGIA LTDA','feliped@fandone.com.br','40.190.903/0001-05','rp_0QJDrnwTxTM1ZvnN');

-- user settings

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `two_factor_enabled`, `two_factor_code`, `two_factor_expires_at`)
VALUES (1,'Arthur Dent','admin@xgrow.com','$2y$10$hBRRzCr4gZj/zSPIgidUdO3VkULCZKL30uHHXwhkJpVoOoDb/m1Nq','aEPQQhnKStglKwVn9INQ9zucEDfM3zfAluo8EFsjRCDXuQVW0MDnz3xPXKAN','2021-09-06 14:23:42','2021-09-06 14:23:42',0,'',NULL);

INSERT INTO `clients` (`id`, `first_name`, `last_name`, `email`, `password`, `verified`, `type_person`, `cpf`, `cnpj`, `fantasy_name`, `company_name`, `company_url`, `address`, `number`, `complement`, `district`, `city`, `state`, `zipcode`, `created_at`, `updated_at`, `percent_split`, `tax_transaction`, `bank`, `branch`, `account`, `recipient_id`, `customer_id`, `statement_descriptor`, `image_id`, `holder_name`, `account_type`, `branch_check_digit`, `account_check_digit`, `phone_number`, `is_default_antecipation_tax`, `phone_country_code`, `phone_area_code`, `phone_number_code`, `phone2`, `upload_directory`, `check_document_number`, `check_document_type`, `check_document_status`, `document_front_image_url`, `document_back_image_url`)
VALUES (1,'Ford','Prefect','cliente@xgrow.com','$2y$10$ErnePrfx17gqCUHuIxHxfeXh8mytEoNcJJsAmrbxt76ozjkLrbBUa',1,'F','012.345.678-90',NULL,'Megadodo Publications','Megadodo Publications Ltda',NULL,'Megadodo House','42',NULL,'Beta Ursae Minoris','Ursa Minor','AC','01234-567','2021-08-31 03:39:44','2021-08-31 03:44:38',95.00,1.50,'123','4567','0123-4',NULL,NULL,NULL,NULL,'FORD PREFECT','checking','8','8','(55) 99999-9999',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT INTO `platforms` (`id`, `name`, `url`, `name_slug`, `slug`, `template_id`, `customer_id`, `created_at`, `updated_at`, `deleted_at`, `active`, `segment`, `url_official`, `reply_to_email`, `reply_to_name`, `active_sales`, `pixel_id`, `template_schema`, `google_tag_id`, `notifications_whatsapp`, `restrict_ips`, `ips_available`, `recipient_id`, `recipient_status`, `cover`, `thumb_id`)
VALUES('00000000-0000-0000-0000-000000000000', 'Mochileiro das Galáxias', 'https://lav3.xgrow.com/00000000-0000-0000-0000-000000000000', 'hitchhikers', NULL, 12, 1, '2021-08-31 21:34:34', '2022-12-20 16:37:55', NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, 1, NULL, 1, 0, NULL, 'rp_D5gpmVYuxHaArWkP', 'active', NULL, 0);

INSERT INTO `platforms_users` (`id`, `name`, `email`, `password`, `platform_id`, `logout`, `created_at`, `updated_at`, `remember_token`, `thumb_id`, `permission_id`, `active`, `surname`, `display_name`, `whatsapp`, `linkedin`, `instagram`, `facebook`, `two_factor_enabled`, `two_factor_code`, `two_factor_expires_at`, `deleted_at`, `accepted_terms`)
VALUES (1,'Proprietario','cliente@xgrow.com','$2y$10$d30UdwxslkvZFNDbBEef9uYzaIR9MvmzH2YEmis3c.vQDI0MAjBSm','',0,'2021-08-31 18:38:33','2021-09-16 15:08:35',NULL,0,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,1);

INSERT INTO `platforms_users` (`id`, `name`, `email`, `password`, `platform_id`, `logout`, `created_at`, `updated_at`, `remember_token`, `thumb_id`, `permission_id`, `active`, `surname`, `display_name`, `whatsapp`, `linkedin`, `instagram`, `facebook`, `two_factor_enabled`, `two_factor_code`, `two_factor_expires_at`, `deleted_at`, `accepted_terms`)
VALUES (2,'Colaborador','colaborador@xgrow.com','$2y$10$d30UdwxslkvZFNDbBEef9uYzaIR9MvmzH2YEmis3c.vQDI0MAjBSm','',0,'2021-08-31 18:38:33','2021-09-16 15:08:35',NULL,0,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,1);

INSERT INTO `platform_user` (`platform_id`, `platforms_users_id`, `type_access`, `permission_id`)
VALUES ('00000000-0000-0000-0000-000000000000',1,'full',NULL);

-- platform settings

INSERT INTO `platform_site_configs` (`id`, `primary_color`, `secondary_color`, `background_color`, `login_template`, `platform_id`, `created_at`, `updated_at`, `image_logo_id`, `image_template_id`, `login_primary_color`, `login_background_color`, `rodape_primary_color`, `rodape_background_color`, `cabecalho_primary_color`, `cabecalho_background_color`, `welcome_template_id`, `second_background_color`, `search_background_color`, `search_color`, `button_color`, `cabecalho_secondary_color`, `image_logo_login_id`, `image_logo_rodape_id`, `seo_title`, `seo_description`, `seo_keywords`, `copyright`, `research_bar`, `suport`, `user_profile`, `favicon_id`, `button_font_color`, `background_image_id`, `status_background_image`, `card_color`, `course_primary_color`, `course_second_color`, `course_card_color`, `course_second_card_color`, `course_button_color`, `course_button_background`, `course_icon_id`, `course_module_content_color`, `email_support`, `status_background_image_login`, `approve_comments`)
VALUES (92,'#246EE9','#E9ECEF','#FFF','C','00000000-0000-0000-0000-000000000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,0,0,0,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,0);
