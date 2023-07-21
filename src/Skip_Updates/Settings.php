<?php
/**
 * Skip Updates
 *
 * @author  Andy Fragen
 * @license MIT
 * @link    https://github.com/afragen/skip-updates
 * @package skip-updates
 */

namespace Fragen\Skip_Updates;

/**
 * Class Settings
 */
class Settings {

	/**
	 * Holds the values for options.
	 *
	 * @var array $options
	 */
	public static $options;

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		self::$options = get_site_option( 'skip_updates', [] );
	}

	/**
	 * Load needed action/filter hooks.
	 */
	public function load_hooks() {
		add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', [ $this, 'add_plugin_menu' ] );
		add_action( 'admin_init', [ $this, 'save_settings' ] );
		add_action( 'network_admin_edit_skip-updates', [ $this, 'save_settings' ] );
	}

	/**
	 * Add plugin menu.
	 */
	public function add_plugin_menu() {
		$parent     = is_multisite() ? 'settings.php' : 'options-general.php';
		$capability = is_multisite() ? 'manage_network_options' : 'manage_options';

		add_submenu_page(
			$parent,
			esc_html__( 'Skip Updates Settings', 'skip-updates' ),
			esc_html_x( 'Skip Updates', 'Menu item', 'skip-updates' ),
			$capability,
			'skip-updates',
			[ $this, 'add_admin_page' ]
		);
	}

	/**
	 * Save settings.
	 */
	public function save_settings() {
		if ( ( ! isset( $_POST['_wpnonce'] )
			|| ! \wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'skip_updates-options' ) )
		) {
			return;
		}

		$options   = get_site_option( 'skip_updates', [] );
		$duplicate = false;
		if ( isset( $_POST['option_page'] )
			&& 'skip_updates' === $_POST['option_page']
		) {
			$new_options = isset( $_POST['skip_updates']['slug'] )
			? sanitize_text_field( wp_unslash( $_POST['skip_updates']['slug'] ) )
			: [];

			$new_options = json_decode( $new_options );
			$new_options = $this->sanitize( $new_options );

			foreach ( $options as $option ) {
				$duplicate = in_array( $new_options[0]['ID'], $option, true );
				if ( $duplicate ) {
					$_POST['action'] = false;
					break;
				}
			}

			if ( ! $duplicate ) {
				$options = array_merge( $options, $new_options );
				update_site_option( 'skip_updates', $options );
			}

			$this->redirect_on_save();
		}
	}

	/**
	 * Add Settings page data.
	 *
	 * @param string $action Form action.
	 */
	public function add_admin_page( $action ) {
		$this->page_init();

		$action = add_query_arg( [ 'page' => 'skip-updates' ], $action );
		( new SU_List_Table() )->render_list_table();
		?>
			<form class="settings" method="post" action="<?php echo esc_attr( $action ); ?>">
		<?php
		settings_fields( 'skip_updates' );
		do_settings_sections( 'skip_updates' );
		submit_button();
		?>
			</form>
		<?php
	}

	/**
	 * Settings for Skip Updates.
	 */
	public function page_init() {
		register_setting(
			'skip_updates',
			'skip_updates',
			null
		);

		add_settings_section(
			'skip_updates',
			esc_html__( 'Skip Updates', 'skip-updates' ),
			[ $this, 'print_section_additions' ],
			'skip_updates'
		);

		add_settings_field(
			'skip',
			esc_html__( 'Select item to skip.', 'skip-updates' ),
			[ $this, 'callback_dropdown' ],
			'skip_updates',
			'skip_updates',
			[
				'id'      => 'skip_updates_skip',
				'setting' => 'slug',
				'plugins' => get_plugins(),
				'themes'  => wp_get_themes(),
			]
		);
	}

	/**
	 * Sanitize each setting field as needed.
	 *
	 * @param array $input Contains all settings fields as array keys.
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input = [];

		foreach ( (array) $input as $key => $value ) {
			$new_input[0][ $key ] = 'uri' === $key ? untrailingslashit( sanitize_url( trim( $value ) ) ) : sanitize_text_field( $value );
		}
		$new_input[0]['ID'] = md5( $new_input[0]['slug'] );

		return $new_input;
	}

	/**
	 * Print the text.
	 */
	public function print_section_additions() {
		echo '<p>';
		esc_html_e( 'If there are wp.org plugins or themes that you do not want to show updates, add them here.', 'skip-updates' );
		echo '</p>';
	}

	/**
	 * Dropdown callback.
	 *
	 * @param array $args Data passed from add_settings_field().
	 *
	 * @return void
	 */
	public function callback_dropdown( $args ) {
		?>
		<label for="<?php echo esc_attr( $args['id'] ); ?>">
		<select id="<?php echo esc_attr( $args['id'] ); ?>" name="skip_updates[<?php echo esc_attr( $args['setting'] ); ?>]">
		<?php

		foreach ( $args['plugins'] as $slug => $plugin ) {
			$plugin_dropdown[ $plugin['Name'] ] = $slug;
		}
		foreach ( $args['themes'] as $slug => $theme ) {
			$theme_dropdown[ $theme->get( 'Name' ) ] = $slug;
		}
		$dropdown['plugin'] = $plugin_dropdown;
		$dropdown['theme']  = $theme_dropdown;

		foreach ( $dropdown as $label => $items ) {
			// Add dropdown type label.
			echo '<option disabled>' . esc_attr( ucwords( $label ) ) . 's</option>';
			foreach ( $items as $name => $slug ) {
				if ( ! $this->is_dot_org( $label, $slug ) ) {
					continue;
				}
				printf(
					'<option value="%s" %s>%s%s</option>',
					esc_attr(
						json_encode(
							[
								'name' => $name,
								'slug' => $slug,
								'type' => $label,
							]
						)
					),
					selected( $label, $name, false ),
					'&nbsp;&nbsp;',
					esc_html( $name )
				);

			}
		}

		?>
		</select>
		</label>
		<?php
	}

	/**
	 * Redirect to correct Settings tab on Save.
	 */
	protected function redirect_on_save() {
		$update = false;

		if ( ! isset( $_POST['_wpnonce'] ) || ! \wp_verify_nonce( \sanitize_key( \wp_unslash( $_POST['_wpnonce'] ), 'skip_updates-options' ) ) ) {
			return;
		}
		$is_option_page = isset( $_POST['option_page'] ) && 'skip_updates' === $_POST['option_page'];
		if ( ( isset( $_POST['action'] ) && 'update' === $_POST['action'] ) && $is_option_page ) {
			$update = true;
		}
		$redirect_url = is_multisite() ? network_admin_url( 'settings.php' ) : admin_url( 'options-general.php' );

		if ( $is_option_page ) {
			$location = add_query_arg(
				[
					'page'    => 'skip-updates',
					'updated' => $update,
				],
				$redirect_url
			);
			wp_safe_redirect( $location );
			exit;
		}
	}

	/**
	 * Query wp.org for plugin/theme information.
	 *
	 * @param string $type plugin|theme.
	 * @param string $slug Item slug.
	 *
	 * @return bool|\WP_Error
	 */
	private function is_dot_org( $type, $slug ) {
		$slug   = 'plugin' === $type ? dirname( $slug ) : $slug;
		$option = get_site_option( 'skip_updates_dot_org', [] );

		if ( ! isset( $option[ $slug ] )
			|| ( empty( $option[ $slug ]['timeout'] ) || time() > $option[ $slug ]['timeout'] )
		) {
			$url      = "https://api.wordpress.org/{$type}s/info/1.2/";
			$url      = add_query_arg(
				[
					'action'                        => "{$type}_information",
					rawurlencode( 'request[slug]' ) => $slug,
				],
				$url
			);
			$response = wp_remote_head( $url );

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$code     = wp_remote_retrieve_response_code( $response );
			$response = 200 === $code ? 'in dot org' : 'not in dot org';

			$option[ $slug ]['dot_org'] = 'in dot org' === $response;
			$timeout                    = $option[ $slug ]['dot_org'] ? '+6 months' : '+10 days';
			$option[ $slug ]['timeout'] = strtotime( $timeout );
			update_site_option( 'skip_updates_dot_org', $option );
		}

		return $option[ $slug ]['dot_org'];
	}

}
