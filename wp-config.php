<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', '');

/** MySQL database username */
define('DB_USER', '');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '>AtUZB%o0!h*-cA|(#kLlErw^Bi-;<#dU;*_@e*nU+AQ#ON|CBz[dUz!O={_DCrF');
define('SECURE_AUTH_KEY',  'ueKHM==+OEX|}r:--p#XJ%x0$dv~A,)sH;!&p^Y#-?s*$-DoB+7Z4sr&dI4i5VDw');
define('LOGGED_IN_KEY',    's[u+vf`V6N8d[J.oH]]b9Y h(v0KqONFo-cO{?^h-SLn-m~uS.fX`,fB?k,-_&Ix');
define('NONCE_KEY',        'BF,LNw>`ktbM]-*#~72Q(+:z]`+WNw6yuBrMa6>,JwvYjIL}rR|si!| iNN2}/o8');
define('AUTH_SALT',        'y+[}(98gE+zT,r))m8IJC~1m|5PdEN2Phrf*p-Gj[}+;7u>(6wb=I1i+afLV$bWR');
define('SECURE_AUTH_SALT', 'B:>f5+;I@Pm_gjCxb/P|Tsv;owi>0`4 --sRF}Y,KihR gEk{1-} </A?)XFMu?2');
define('LOGGED_IN_SALT',   'V>4+=Fex{~nfxNb`IZo|MOIiYl9t56=3|aWPz5IwQ_^yc%!K_f.-h0ID~Ac|8s(l');
define('NONCE_SALT',       'vAIxQv?h[&x08,+Wp}78h}pHP3eYBTo10#)`f<0-)L8jhLRLFRfMPbGHs9ko$k,;');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

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
