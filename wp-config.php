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
define( 'DB_NAME', 'bruden' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

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
define( 'AUTH_KEY',         '|5FD~;F!fBXi{si85J.):.yezw Ctg{G5:wh+_uU5!u?2^JdUk{-Q$hkDQ/JKh^s' );
define( 'SECURE_AUTH_KEY',  'Cq,$~E}Vl[m>ixMd]R?iv=_NeM|N!TJ$.3~ob4hN9-qo`PXnHEN.es_~$F[3c]t&' );
define( 'LOGGED_IN_KEY',    'q*>D*7^Ngo9znL*|nn8ol`c.dbV$ec$Kx~b-A2rp)Q2^w+myV.q7JdAN9]*PmL2R' );
define( 'NONCE_KEY',        '-usN4Y+6}m_;E#LB<N92}/n`Xm+f-A=Oma7Wz&a&01gNF+52pAV.YA!mhV1qjMua' );
define( 'AUTH_SALT',        'X{y[Yy{Fb&-((%y|Yc0~T5+L)rUK?k?n$4G&pWD*b[`Sx/PwA5KH;h}>N?1-*Sai' );
define( 'SECURE_AUTH_SALT', 'dS,2WPmj-t=.1%S@&8|8^>+a}wDqmuEz$dX<=9_wvCK;}p,_:mFXEjYpm$1,jh<A' );
define( 'LOGGED_IN_SALT',   'dt7Qt~$fsj4K/eU|A$/8D;`dvg*TW5f,H>O}E3lG9$ZoPKE7?e:,ws+?#GSQO9(*' );
define( 'NONCE_SALT',       'lCEkno?>i)0-$S$g7STLv>?CNq!dYLpFz7Be?Fu>35PvD$}os9c#NTgcV{AIMt=:' );

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
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
