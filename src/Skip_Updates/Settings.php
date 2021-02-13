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
	 * Supported types.
	 *
	 * @var array $types
	 */
	public static $types = [
		'plugin',
		'theme',
	];

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->load_options();
	}

	/**
	 * Load site options.
	 */
	private function load_options() {
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
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$post_data = wp_unslash( $_POST );
		$options   = get_site_option( 'skip_updates', [] );
		$duplicate = false;
		if ( isset( $post_data['option_page'] )
			&& 'skip_updates' === $post_data['option_page']
		) {
			$new_options = isset( $post_data['skip_updates'] )
			? $post_data['skip_updates']
			: [];

			$new_options = $this->sanitize( $new_options );

			foreach ( $options as $option ) {
				$duplicate = in_array( $new_options[0]['ID'], $option, true );
				if ( $duplicate ) {
					break;
				}
			}

			if ( ! $duplicate ) {
				$options = array_merge( $options, $new_options );
				update_site_option( 'skip_updates', $options );
			}

			$this->redirect_on_save( $post_data );
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
			<form class="settings" method="post" action="<?php esc_attr_e( $action ); ?>">
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
			'type',
			esc_html__( 'Repository Type', 'skip-updates' ),
			[ $this, 'callback_dropdown' ],
			'skip_updates',
			'skip_updates',
			[
				'id'      => 'skip_updates_type',
				'setting' => 'type',
			]
		);

		add_settings_field(
			'slug',
			esc_html__( 'Repository Slug', 'skip-updates' ),
			[ $this, 'callback_field' ],
			'skip_updates',
			'skip_updates',
			[
				'id'      => 'skip_updates_slug',
				'setting' => 'slug',
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
			$new_input[0][ $key ] = 'uri' === $key ? untrailingslashit( esc_url_raw( trim( $value ) ) ) : sanitize_text_field( $value );
		}
		$new_input[0]['ID'] = md5( $new_input[0]['slug'] );

		return $new_input;
	}

	/**
	 * Print the text.
	 */
	public function print_section_additions() {
		echo '<p>';
		esc_html_e( 'If there are dot org plugins or themes that you do not want to show updates, add them here.', 'skip-updates' );
		echo '</p>';
	}

	/**
	 * Field callback.
	 *
	 * @param array $args Data passed from add_settings_field().
	 *
	 * @return void
	 */
	public function callback_field( $args ) {
		?>
		<label for="<?php esc_attr_e( $args['id'] ); ?>">
			<input type="text" style="width:50%;" id="<?php esc_attr( $args['id'] ); ?>" name="skip_updates[<?php esc_attr_e( $args['setting'] ); ?>]" value="" placeholder="plugin-slug/plugin-file.php">
			<br>
			<span class="description">
			<?php esc_html_e( 'Ensure proper slug for plugin or theme.', 'skip-updates' ); ?>
			</span>
		</label>
		<?php
	}

	/**
	 * Dropdown callback.
	 *
	 * @param array $args Data passed from add_settings_field().
	 *
	 * @return void
	 */
	public function callback_dropdown( $args ) {
		$options['type'] = [ 'plugin' ];
		?>
		<label for="<?php esc_attr_e( $args['id'] ); ?>">
		<select id="<?php esc_attr_e( $args['id'] ); ?>" name="skip_updates[<?php esc_attr_e( $args['setting'] ); ?>]">
		<?php
		foreach ( self::$types as $item ) {
			$selected = ( 'plugin' === $item ) ? 'selected="selected"' : '';
			echo '<option value="' . esc_attr( $item ) . '" $selected>' . esc_attr( $item ) . '</option>';
		}
		?>
		</select>
		</label>
		<?php
	}

	/**
	 * Redirect to correct Settings tab on Save.
	 *
	 * @param array $post_data Array from $_POST.
	 */
	protected function redirect_on_save( $post_data ) {
		$update = false;

    	 // phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ( isset( $post_data['action'] ) && 'update' === $post_data['action'] )
			&& ( isset( $post_data['option_page'] ) && 'skip_updates' === $post_data['option_page'] )
		) {
			$update = true;
		}
 	    // phpcs:enable

		$redirect_url = is_multisite() ? network_admin_url( 'settings.php' ) : admin_url( 'options-general.php' );

		if ( $update ) {
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
}
