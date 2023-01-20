<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать файл в "wp-config.php"
 * и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки базы данных
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры базы данных: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'give_wp' );

/** Имя пользователя базы данных */
define( 'DB_USER', 'root' );

/** Пароль к базе данных */
define( 'DB_PASSWORD', '' );

/** Имя сервера базы данных */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу. Можно сгенерировать их с помощью
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}.
 *
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными.
 * Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ejn$*9tGFi5L&ch7{^__U-N?vIgej.E^bESF[s4}p<l6nA:$[M8: jGx+M)Vlr+6' );
define( 'SECURE_AUTH_KEY',  'ts!28r1a[siA>Rz84;=8zSR!rhuezVZ,Tlq@?qK)#nrK4qrC;VCYxJ4_a3KOrDV8' );
define( 'LOGGED_IN_KEY',    'JYnz-`iZ$^mJsXoWJ;WT#i%=TS5_~SPi!p%qd/LicJn|>I}^A0_d$7MMqd+]i#7P' );
define( 'NONCE_KEY',        'O=bE*&2c|t[uKQ[cZPX)46YiJ-$Avm&qXGl%ncW%_~+}V1#dBC*By$Ti~9 r2a9Z' );
define( 'AUTH_SALT',        'z92yZ-Fzz.iNluXUVq$I&}o,/61vb-T.]jyDB6;nrI% !I<]=EA+aV{#tQK(abTw' );
define( 'SECURE_AUTH_SALT', '^25cx2s,.u;[er%SDdx(aQYc_tYEt4:IK+.~Lz%&q:NeV8MBZraF6R3nTb^y%bP$' );
define( 'LOGGED_IN_SALT',   '4Ijqibt^e:)R-B2*j![~7bi= dojl;_*Ran0^{bFM{@[t$s/62#0EH/e/[*s;Mob' );
define( 'NONCE_SALT',       'PXQK43G3g5 nPmq !ulxZ<[hx!x<B74#-)Y4=b+_;;/|0>iDIoVf.orT3m{oW^XZ' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* Произвольные значения добавляйте между этой строкой и надписью "дальше не редактируем". */



/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';
