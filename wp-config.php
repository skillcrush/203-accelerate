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
define('DB_NAME', 'accelerate');

/** MySQL database username */
define('DB_USER', 'root1');

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
define('AUTH_KEY',         '-$*lTFBW|Z0CM_FjIP%0+S*R%8W> Ap+m5QL|Ie38VZ0+SVg@cbpHtw!QR]lG6,<');
define('SECURE_AUTH_KEY',  'a{F%}^,]}P)q1p:g5aCV3$|f_fW@H=DZ:iVfqJ?h~2.D-s-R Rnn+V_7ZDIG7?J3');
define('LOGGED_IN_KEY',    ']:vk#._iK,v-g ;Smjquk`E`m`(7,L3~i/|4pub7b2s~44)Fj]h~?-;|(EtK$$@|');
define('NONCE_KEY',        'o4qum!]a5F.Kx=fJt[SH;OWQVlY5_X)^$VCWv*Bia=sQ|]B`_Lj.$.h.++:A*]B6');
define('AUTH_SALT',        'fWM`T=Jx=jfrW:Ac}6%T>Wt7fF0yEFpP9?(m|J.xy!v9b%GxAY<B&=Tv~*%0i=|.');
define('SECURE_AUTH_SALT', 'R=i4<3 FhmYoG 1@dkEiCv&ERS^hQ((#po>|Yg]:mKT$GZ|!_6Qz8%F4!u8V1Pxz');
define('LOGGED_IN_SALT',   '9o7K@~hJe,(/tE|T<fHeg^W[V8Hf1/gaeb7*.>%4[SqXf`m ZF$>B|T?B<4:6<hF');
define('NONCE_SALT',       'H)E+=Rh3f>BY)E`gB#TI4HIEB-XP*- x8yZ!zV$P6?c%{-l%8(TR(Db$++IXl(ru');

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
