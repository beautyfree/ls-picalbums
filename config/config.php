<?php

$config = array ();

$config ['table'] ['album'] = '___db.table.prefix___picalbums_album';
$config ['table'] ['picture'] = '___db.table.prefix___picalbums_picture';
$config ['table'] ['comment'] = '___db.table.prefix___picalbums_comment';
$config ['table'] ['heart'] = '___db.table.prefix___picalbums_heart';
$config ['table'] ['note'] = '___db.table.prefix___picalbums_note';
$config ['table'] ['settings'] = '___db.table.prefix___picalbums_settings';
$config ['table'] ['blacklist'] = '___db.table.prefix___picalbums_blacklist';
$config ['table'] ['related'] = '___db.table.prefix___picalbums_related';
$config ['table'] ['category'] = '___db.table.prefix___picalbums_category';
$config ['table'] ['tag'] = '___db.table.prefix___picalbums_tag';

// URL к фотольабомам
$config ['albums_router_name'] = 'albums';
$config ['main_albums_router_name'] = 'mainalbums';

Config::Set('router.page.' . $config ['albums_router_name'], 'PluginPicalbums_ActionPicalbums');
Config::Set('router.page.' . $config ['main_albums_router_name'], 'PluginPicalbums_ActionMainalbums');
Config::Set('router.page.picalbums_settings', 'PluginPicalbums_ActionSettings');


// хук для вывода альбомов в профиле пользователя 
$config ['usehookname'] = 'template_profile_whois_item';
// Делать ли очистку БД при деактивации плагина
$config ['dropuninstall'] = false;

// максимальный размер описания и заголовка
$config ['text_form_max_characters'] = 240;
$config ['text_title_max_characters'] = 40;
$config ['text_tag_max_characters'] = 64;

// Количество элементов в различных блоках
// Блок случайные фотографии
$config ['block_random_count'] = 4;
// Блок лцчшие фотографии
$config ['block_best_count'] = 8;
// Блок последние фотографии
$config ['block_last_count'] = 8;
// Блок последние Альбомы
$config ['block_last_albums_count'] = 8;
// Блок лучшие фотографии за последние 5 дней
$config ['block_best_by_date_count'] = 8;
// Блок лучшие фотографии в профиле
$config ['block_profile_best_pictures'] = 8;
// Блок в профиле "Фотографии, на которых отмечен пользователь"
$config ['block_profile_mark'] = 8;
// Блок в профиле "Альбомы пользователя"
$config ['block_profile_albums'] = 8;
// Блок лучшие фотографии в профиле за последние 5 дней
$config ['top_images_by_days_day_count'] = 5;

// Разрешение на создание кол-во альбомов
$config ['rating_create_album_minimal_activate'] = true;

$config ['rating_create_album_minimal_1'] = 0.0;
$config ['rating_create_album_minimal_2'] = 0.2;
$config ['rating_create_album_minimal_3'] = 1.0;
$config ['rating_create_album_minimal_4'] = 3.0;

$config ['create_album_count_less_1'] = 10;
$config ['create_album_count_1_2'] = 20;
$config ['create_album_count_2_3'] = 30;
$config ['create_album_count_3_4'] = 40;
$config ['create_album_count_4_more'] = 50;

// Максимальное количество фоток в одном альбоме
$config ['max_picture_in_album'] = 100;

// Минимальное время между комментариями подряд
$config ['comment_limit_time'] = 15;
// Снятие ограничения тайминга при N рейтинге
$config ['comment_limit_time_off_rating'] = 0.3;

// Разрешено добавлять не более picture_count_limit_by_time фоток каждые picture_limit_time секунд
$config ['picture_limit_time'] = 60;
$config ['picture_count_limit_by_time'] = 20;

// Показывать форму комментирования при загрузке фотографии
$config ['show_comment_form_after_load_picture'] = false;
// Показывать комментарии при загрузке фотографии
$config ['show_comments_after_load_picture'] = true;

// Можно отмечать только друзей
$config ['notes_mark_only_friend'] = false;
// Отправлять запрос на добавления в друзья при отметке
$config ['notes_send_become_friend'] = true;
// Необходимость подтверждения меток
$config ['notes_mark_confirm'] = true;
// Максимальное количество меток на одного пользователя на одной фотографии, ноль - нет ограничений
$config ['max_mark_count_by_one_picture'] = 0;

// Уведомление через личные сообщения, отправлять ли напоминание по email
$config ['talk_notify_send_mail'] = true;
// Уведомление через личные сообщения, когда пользователь отметил другого пользователя
$config ['talk_notify_when_user_mark'] = true;
// Уведомление через личные сообщения, когда пользователь подтвердил отметку
$config ['talk_notify_when_user_mark_confirm'] = true;
// Уведомление через личные сообщения, когда пользователь удаляет отметку
$config ['talk_notify_when_user_mark_delete'] = true;

// Альбомы только для авторизированных пользователей
$config ['picalbums_only_for_auth'] = false;
// Миннимальный ретингй для добавления картинки в альбом
$config ['minimal_rating_for_append_picture'] = 0.0;
// Активировать возможность аджакс навигации
$config ['enable_ajax_navigation'] = true;

// Макимальный размер файла который можно загружать через ajax
$config ['ajax_max_size_upload_file'] = 8 * 1024 * 1024;
// Количество параллельных запросов во воремя загрузки ajax
$config ['ajax_upload_max_connections'] = 3;

// Максимальный размер фотографии (если ноль, то ограничений нет, нулем могут быть только оба параметра)
$config ['picture_max_height_size'] = 0;
$config ['picture_max_width_size'] = 0;
// Размер картинки после ресайза
$config ['picture_resize_width_value'] = 500;
$config ['picture_resize_height_value'] = null;
// Размер картинки миниатюры после ресайза
$config ['miniature_resize_width_value'] = 130;
$config ['miniature_resize_height_value'] = 97;
// Вырезать ли максимально возможный прямоугольник
$config ['miniature_crop'] = true;
// Вырезать прямоугольник по центру картинки?
$config ['miniature_crop_middle'] = false;
// Размер картинки миниатюры, которая используется в блоках после ресайза
$config ['miniature_block_resize_width_value'] = 80;
$config ['miniature_block_resize_height_value'] = 60;

// Отключить ли индикатор загрузки через ajax?
$config ['ajax_upload_progress_disable'] = false;
// Отключить ли индикатор загрузки через flash?
$config ['flash_upload_progress_disable'] = false;

// Шаг загрузки новых фотографий во френд-ленте (а так же на странице все приватные альбомы всех пользователей)
$config ['friend_page_step'] = 50;
// Количество фотографий в альбоме во вренд ленте при начальной загрузке (а так же на странице все приватные альбомы всех пользователей)
$config ['friend_page_start_count'] = 10;
// Показывать информацию о количестве в меню
$config ['show_count_info_in_menu'] = true;

// Сохранять и дать возможность показать оригинал фотографий в альбоме?
$config ['is_save_original'] = true;
// Красивая галерея, установить?
$config ['paraloid_enable'] = true;
// Сохранять ли даныне exif при загрузке фото
$config ['exif_enable'] = false;

// Переименовывать ли url при переименовании картинки и фотографии
$config ['url_rename_after_edit'] = true;
// если переименование запрещено, то переименовывать ли если фотка имет начально название
$config ['url_rename_picture_default_name'] = false;

// Использовать ли навигацию картинок в оригинале, работает только безе ajax
$config ['original_photo_navigation'] = true;
// Длина случайного имени при сохранении картинки
$config ['func_generator_length'] = 10;

// настройка слайдера лучших фотографий
$config ['best_pictures_slider_enable'] = true;
$config ['best_pictures_slider_piccnt'] = 20;

$config['jevix']=require(dirname(__FILE__).'/jevix.php');

// Параметры водяных знаков
$config ['image'] ['round_corner'] = false;
$config ['image'] ['watermark_use'] = false;
$config ['image'] ['watermark_text'] = 'Livestreet albums';
$config ['image'] ['watermark_font'] = 'Arial';
$config ['image'] ['watermark_position'] = '0,24';
$config ['image'] ['watermark_font_color'] = '255,255,255';
$config ['image'] ['watermark_font_size'] = '8';
$config ['image'] ['watermark_font_alfa'] = '0';
$config ['image'] ['watermark_back_color'] = '0,0,0';
$config ['image'] ['watermark_back_alfa'] = '40';
$config ['image'] ['watermark_min_width'] = '200';
$config ['image'] ['watermark_min_height'] = '130';
$config ['image'] ['jpg_quality'] = 100;

/**
 * Настройки вывода блоков на главную сайта
 */
Config::Set('block.rule_index_blog.blocks', array(
	'right' => array(
            'stream'=>array('priority'=>100),
            'tags'=>array('priority'=>90),
            'blogs'=>array('params'=>array(),'priority'=>50),
			'PicalbumsLastAlbums'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
            'PicalbumsLastPictures'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
			'PicalbumsBestPictures'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
			'PicalbumsBestPicturesByDays'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
			'PicalbumsRandomPictures'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
		)
));

/**
 * Настройки вывода блоков по урлу mainalbums
 */
Config::Set('block.rule_mainpicalbums', array(
	'action'  => array( $config ['main_albums_router_name'], ),
	'blocks'  => array( 
		'right' => array(
			'PicalbumsLastAlbums'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
            'PicalbumsLastPictures'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
			'PicalbumsBestPictures'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
			'PicalbumsBestPicturesByDays'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
			'PicalbumsRandomPictures'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>20),
            'PicalbumsTags'=>array('params'=>array('plugin'=>'picalbums'),'priority'=>10),
		)
	),
	'clear' => false,
));

/**
 * Настройки вывода блоков по урлу albums
 */
Config::Set('block.rule_picalbums', array(
	'action'  => array( $config ['albums_router_name'], ),
	'blocks'  => array( 
		'right' => array(
			'PicalbumsProfileAlbums'=>array('params'=>array('plugin'=>'picalbums','priority'=>90)),
			'PicalbumsProfileBestPictures'=>array('params'=>array('plugin'=>'picalbums','priority'=>90)),
			'PicalbumsProfileMarked'=>array('params'=>array('plugin'=>'picalbums','priority'=>90)),
			'PicalbumsAlbumGuestMarked'=>array('params'=>array('plugin'=>'picalbums','priority'=>90)),
		)
	),
	'clear' => false,
));

/**
 * Настройки вывода блоков в профиле юзера
 */
 
Config::Set('block.rule_profile', array(
	'action'  => array( 'profile', ),
	'blocks'  => array( 
		'right' => array(
			'actions/ActionProfile/sidebar.tpl'=>array('priority'=>100),
			'PicalbumsProfileAlbums'=>array('params'=>array('plugin'=>'picalbums','priority'=>90)),
			'PicalbumsProfileBestPictures'=>array('params'=>array('plugin'=>'picalbums','priority'=>90)),
			'PicalbumsProfileCommentedPictures'=>array('params'=>array('plugin'=>'picalbums','priority'=>90)),
			'PicalbumsProfileCommentedAlbums'=>array('params'=>array('plugin'=>'picalbums','priority'=>90)),
			'PicalbumsProfileMarked'=>array('params'=>array('plugin'=>'picalbums','priority'=>90)),
		)
	),
	'clear' => false,
));

// настройки добавленные в версии 0.2
// Настройки паджинации
$config ['all_picture_page_cnt'] = 50;
$config ['albums_listing_page_cnt'] = 10;
$config ['friendpage_page_cnt'] = 10;
$config ['allprofilepictures_page_cnt'] = 10;
$config ['albumshow_page_cnt'] = 50;
$config ['categories_listing_page_cnt'] = 10;
$config ['categories_listing_max_albums_preview_cnt'] = 10;
// Использовать ли pushstate при нафигации по картинкам
$config ['pjax_for_picture_listing'] = true;
// Настройки amason	s3					
$config ['amasons3_enable'] = false;
$config ['awsAccessKey'] = 'enter awsAccessKey';
$config ['awsSecretKey'] = 'enter awsSecretKey';
$config ['bucketName'] = 'enter bucketName';
$config ['amason_suffix_url'] = '.s3.amazonaws.com';
// Настройки imageshack
$config ['imageshack_enable'] = false;
$config ['imageshack_devkey'] = '';
// Активизировать галерею slidergallary
$config ['slidergallary_enable'] = true;
// Модераторы галереи
$config ['moderators'] = array('admin', 'sebastianprelesniy');
$config ['create_moderated_only_for_moderators'] = false;
// Слать ли превьюшки в кроне для напоминаний модераторов
$config ['cron_need_images'] = true;
// вкл/выкл tooltips в списке категорий
$config ['album_preview_tooltip_enable'] = true;
$config ['album_preview_tooltip_link_enable'] = true;
// Количество фоток в тултипе
$config ['image_count_in_tooltip'] = 6;
// Размер аватарок при посмотре лайкнувших фотку
$config ['heart_users_avatar_size'] = 24;
// Осуществлять ли предзагрузку следующих фотографий
$config ['preload_images_emable'] = true;
// функционал копирования фотографий к себе в профиль
$config ['functional_copy_picture_enable'] = true;

// Настройки для разработчика
$config ['virtual_main_user_id'] = 2147483647;
$config['sitemap'] = array (
    'cache_lifetime' => 60 * 60 * 24, // 24 hours
    'sitemap_priority' => '0.8',
    'sitemap_changefreq' => 'weekly'
);

return $config;

?>
