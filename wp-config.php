<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'anandaindb89ykh');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'mysql');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'n4p5wsip&&CVnbn1&3U,Y G[c8**8Ty{g<:j,{VL,-iUjn[p1Q,4MW@Bifb4KU.Y');
define('SECURE_AUTH_KEY',  'oqdbIOSGE@J^LO]dB^u!A~I5D-k(O<a2bncV;MccT31i6^<gxmU,zZq_EG%GEzHO');
define('LOGGED_IN_KEY',    'hg0jP2E+g[0G?tt(O{BHRE&LPcuLY@}c?Gyu;FEjuvOkf@RPvL,%_MySGvcZmC%x');
define('NONCE_KEY',        '3>anx|At(f}%~8q(<R+BBsbMOUH`vh#KdIc`VqHCd0p]IVaX&.yZwn5<{WlK^!yJ');
define('AUTH_SALT',        'G4.n/?dis{5&@U07hfDJUu%I.nyyG3;}#v* .J:nCqH|2ZWvT1/i_B>6k+k<zU!,');
define('SECURE_AUTH_SALT', 'jC`)!o4P_>h),BI_OOgr_i-bNG,^L)+^4s&CWnPu`B]n-$R+I4(:od1l) HaQo68');
define('LOGGED_IN_SALT',   '<>G.i?;6lUU`=-pHfa=o0k!gMeBB>(n+??}D2e8bS*3nn=4u4lWxi(Z6v[04wi(B');
define('NONCE_SALT',       '6a#BqEI~&%K&C#j 3sYhjIoKAM58bOvLY%WW`%4gnvfRv;Zlg5m`;%n ~.Va Q(I');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
