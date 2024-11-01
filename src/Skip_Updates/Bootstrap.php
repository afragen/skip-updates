<?php
/**
 * Skip Updates
 *
 * @author    Andy Fragen
 * @license   MIT
 * @link      https://github.com/afragen/skip-updates
 * @package   skip-updates
 */

namespace Fragen\Skip_Updates;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Bootstrap
 */
class Bootstrap {
	/**
	 * Holds main plugin file.
	 *
	 * @var string $file
	 */
	protected $file;

	/**
	 * Holds main plugin directory.
	 *
	 * @var string $dir
	 */
	protected $dir;

	/**
	 * Constructor.
	 *
	 * @param  string $file Main plugin file.
	 * @return void
	 */
	public function __construct( $file ) {
		$this->file = $file;
		$this->dir  = dirname( $file );
	}

	/**
	 * Run the bootstrap.
	 *
	 * @return bool|void
	 */
	public function run() {
		add_filter( 'site_transient_update_plugins', [ $this, 'update_site_transient' ], 10, 1 );
		add_filter( 'site_transient_update_themes', [ $this, 'update_site_transient' ], 10, 1 );

		( new Settings() )->load_hooks();
	}

	/**
	 * Update the site update transient.
	 *
	 * @param \stdClass $transient Site update transient.
	 *
	 * @return \stdClass $transient
	 */
	public function update_site_transient( $transient ) {
		$skip_updates = get_site_option( 'skip_updates', [] );
		foreach ( $skip_updates as $skip ) {
			$unset = false;
			if ( isset( $transient->response[ $skip['slug'] ] ) ) {
				switch ( $skip['type'] ) {
					case 'plugin':
						if ( false !== strpos( $transient->response[ $skip['slug'] ]->package, 'downloads.wordpress.org' ) ) {
							$unset = true;
						}
						break;
					case 'theme':
						if ( false !== strpos( $transient->response[ $skip['slug'] ]['package'], 'downloads.wordpress.org' ) ) {
							$unset = true;
						}
						break;
				}
			}
			if ( $unset ) {
				unset( $transient->response[ $skip['slug'] ] );
			}
		}

		return $transient;
	}
}
