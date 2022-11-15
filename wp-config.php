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
define( 'DB_NAME', 'wordpress_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'jeRUxfR;j^4gg~+nK80V|w9B 9hrcd]v@76hudGk3j=Q+z2-F7G]p@QIe@=:dnXp' );
define( 'SECURE_AUTH_KEY',  'A-DAe^p$Jw8{~ZcU]Ts*k]K~6SKnkN3*gSn(Px7H10N%nPWk<e:lom)(4.P^_O04' );
define( 'LOGGED_IN_KEY',    '9@M;Llt@bW^[XyJ-A5v(W$zTk.T4BhNy{0M)c]PK`@;%oi[tw0r+`sftgEHvfhno' );
define( 'NONCE_KEY',        'zIAYn}2EAo|3R&~JFInjku[/7r7W&:Q{jdxlE<3DF*PD1c#$$Y=EndQfsu*cxvlz' );
define( 'AUTH_SALT',        'tLR14r]E[W@h6ARa-2WVyW%I}^zt#vD`XtZ/?p@~*zu*$zzibhY~iqKDn+r0.:C&' );
define( 'SECURE_AUTH_SALT', '$A=Ow{cgBa&Z/>Yp{_{-e{H02CjkN-r-phEbeyHIzIHQ]#e XJIU#w&jpcfqxR`9' );
define( 'LOGGED_IN_SALT',   'Sk.5]Icr~F+itUIf`ti-C<Z%TlGj>G@udv2^n$#mI1qox0q#lUUuK]R4Cg[j(YrK' );
define( 'NONCE_SALT',       '6EXopic&gdQM,^6W;{QifUp+defsrDW*qRfzlA&PA!w=Gv_Lu2qn-B/bhn8Ii0{u' );

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
