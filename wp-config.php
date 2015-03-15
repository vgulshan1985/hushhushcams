<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'amgiacov_hushhush');

/** MySQL database username */
define('DB_USER', 'amgiacov_hushhus');

/** MySQL database password */
define('DB_PASSWORD', 'anthony88');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '1:oA!Fv9PLkQd9#P.R06dEPkx-U*k0+4L?#i~UI~5>8rT)RFxbqvA(W|]Gd/gCuA');
define('SECURE_AUTH_KEY',  'aU24zok:M?h?HQv[{AoEgB9l<(C,a=xs{sQz(2?wYYYc W20|4*e_rgwvQ4a[uRG');
define('LOGGED_IN_KEY',    '?P*P{v$_*a?]Zmm9vq$/5B{wc^|+wtzElFDb+W13?;i{z,qc2NdBnD f5aTwR(y.');
define('NONCE_KEY',        'mgF9%hPN1o;+-pZ+1a;;_cpB_X,~=BaM+J0Gg0^m7Kw6.!ya57+h:`~^[5L_U[(;');
define('AUTH_SALT',        '`ZgPaWdT8 N^q[e]yFiU5M}@?vSz`4j!nw+2(aNSz)H5VRE!n4LzQ@D c? BCY$#');
define('SECURE_AUTH_SALT', '~4#&-yygtX2@]8E.ZpV@B2.UqFR&<AiF:CaCe<s|);HanV9ATq]qm%z/^^f>G#XK');
define('LOGGED_IN_SALT',   'Sx|.esu-]Ebkj+c>hZs-ft3r<x+xM==|W@$hk;/ 2-L-1kGA*L)RXMesntv6f,YS');
define('NONCE_SALT',       'I+jD7 Z9.tt.%~UdtMAFw%t! X/eS2$XG_?.-0K.f@+ya~yu9lFpW@*TN(a1K-30');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
