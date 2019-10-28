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
define( 'DB_NAME', 'encyclov_jonhouses' );

/** MySQL database username */
define( 'DB_USER', 'encyclov_jonas' );

/** MySQL database password */
define( 'DB_PASSWORD', 'encyclov_jonas' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'we$hu6C3N_N%6;d+`7v%R7IHeO-&qpT;x`&Mc0d>r(OQV8mrk^oMH<3KrAL?;N&a' );
define( 'SECURE_AUTH_KEY',  '-VYwE=B;(q_4d4F]OB[5f{f:E3iS=JO=ycb.O9iueV~(CWfx7jjc[paWjd[n4n|L' );
define( 'LOGGED_IN_KEY',    'Rgv-dzz-JX)Y:*[+xJQFoNiy:&>OLiWkNCh1n3=CN`9_>_F$)D=;/hp`0hH_+d+X' );
define( 'NONCE_KEY',        'U3oYL}TK|Z!i._|4Anu e}C4~Qm`^*t,-[~SXSeMy-<@cPh(gm_!]VBJ_9XoB@_}' );
define( 'AUTH_SALT',        'D4Tb@yMbYLQTk`)Y!<;L)e-B/q:x/f@Gz&TVNd}}L;CX*y[rS;O^tyz;@wUz7zCU' );
define( 'SECURE_AUTH_SALT', 'Y5FXG$[6Wav^#5)fN=@e]1C2^j7G=Bd4NS,>ax@IEj=+(oEG!;:Y(bU#;z9 SX}&' );
define( 'LOGGED_IN_SALT',   'J0sZC=v1P5Lr1$7_db7NQH+dW1.HsogX|(|~0^~F}<-^6)fu# ,:B2_E*.p(0JHN' );
define( 'NONCE_SALT',       'm`unwBXNvwktU&1VIi>9.z&l?#`6mY_b{7*wobQUY$Q>9%O*a:Y-z`1`c>NnPL{t' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
