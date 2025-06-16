<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'autopole3144' );

/** Database username */
define( 'DB_USER', 'autopole3144' );
//define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'autopole1@' );
//define( 'DB_PASSWORD', '1q2w3e4r' );

/** Database hostname */
//For test
//define( 'DB_HOST', 'localhost' );
//define( 'DB_HOST', '192.168.0.11' );
//operation
//define( 'DB_HOST', '183.111.199.224' );
//hy_dev
define( 'DB_HOST', '183.111.154.138' );

///For test

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

// 추가 데이터베이스 설정 (예: "custom_database" 연결)
//define('CUSTOM_DB_NAME', 'autoMallServer');
//define('CUSTOM_DB_USER', 'autopole3144');
//define('CUSTOM_DB_PASSWORD', 'autopole1@');
//define('CUSTOM_DB_HOST', '192.168.0.11');


/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'x9Bfm2B>WjP_U)0)oJO*,!<V My0mPP0)rqnAZ>Z Q>#)(aTY_-O/KvvQ!1QI7M[' );
define( 'SECURE_AUTH_KEY',  'OB/1>J0j~vCtNhNI[ByKux[l&-5k`2@3GJnjeb%F.><aKHs^UBc$6<v%;dWlg<z{' );
define( 'LOGGED_IN_KEY',    'SV#`=hZWk.(TCZ^crV,;XyY[e*zvw9ju_]d7ngb`049m{:/vkas2?A6cCcxtx89!' );
define( 'NONCE_KEY',        'W(K2kV00EhX{RHrb]lHfvm}],%f~p.j$g{<sOtF1$PxA#oq[61c2:gBviaGd}=XB' );
define( 'AUTH_SALT',        'ERZiie227o}3Sa4oDL_4>LRV,F1?^wSfqSSSY%M~p?P`%NtdKkB`bBR]%1x*Efsx' );
define( 'SECURE_AUTH_SALT', 'ko7ei6^/}@kI:i][JHlE3y]q2ZH|$2HaldkV5V.Dwg%t0T3p>z/)m=Z{zDLTo$<m' );
define( 'LOGGED_IN_SALT',   'PsJ.%*Nd9d?mOodem$Cgf4BMgO!)3z17Xck8rGCT+ZZ$V9rsqP4O1Ab%wMDm7X/w' );
define( 'NONCE_SALT',       '2>CG-R}rrcJ_3h$9kzb2L7k_lU0<DQ:MpZ,ecC+@nZ1LG<g[?iW r.~(.<!w2,da' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
/* start update 241024  sizeof 경고 안나오게끔 수정, 배포시에는 주석처리해야함*/
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG_DISPLAY', false);
/* end update 241024 */

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
