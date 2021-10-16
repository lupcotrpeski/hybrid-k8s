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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'RDS_DBNAME' );

/** MySQL database username */
define( 'DB_USER', 'RDS_USERNAME' );

/** MySQL database password */
define( 'DB_PASSWORD', 'RDS_PASSWORD' );

/** MySQL hostname */
define( 'DB_HOST', 'RDS_DNS' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         'kcoezrD^rpP?`l`.K=:P2m9X7twd.K{E[r60Oa8<I@eO)XLYg3E3co3zSZhrG+o{' );
define( 'SECURE_AUTH_KEY',  'b*2HAJyI0Khea(V8jK1P3lCArB.zL,+72[yD[;R~oRtA]aS2^QBPdycBf;)f{_<L' );
define( 'LOGGED_IN_KEY',    'g!o,]mjo|k13lVRo=+;I#i<$t{YnA,p^q <g]jem[BjpBC6yn|*M1%aN@KjG*lF]' );
define( 'NONCE_KEY',        '4El:%DZe_PP!R#A@FM}#=GMSQ_:%l1 NLW30B!Icj:oIsD1g3UpP/0g{{L{`j@Po' );
define( 'AUTH_SALT',        '(gj_31}|P.9Q_x`n9Fe_8X03|siR2<1uN-Qu{I$&qD,GB%p-@r|:o^,C5t/S>IGQ' );
define( 'SECURE_AUTH_SALT', '<H3)A4|9]U!g2Nphh{FgDRA(L`O&|0r?{l=v(}mv-xg@3_dTRnr <c|M(~$7h%!N' );
define( 'LOGGED_IN_SALT',   'dxbGKna(VvW!>Q cmfuRKP8hwWfR#9kX1|{eyp7oEfZ-z3Z}pWTP*lDQT~8[Jk9@' );
define( 'NONCE_SALT',       '4KAPaq;kikZAb/@i@(@!Vw*jMkQI7HzEw@O.bYT/X&IJm20EoQA7~>StPaEo{MNg' );

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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';