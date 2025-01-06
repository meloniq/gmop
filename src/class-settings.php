<?php
namespace meloniq\GMOP;

class Settings {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'init_settings' ), 10 );
	}

	/**
	 * Add menu page.
	 *
	 * @return void
	 */
	public function add_menu_page() : void {
		add_submenu_page(
			'gmop',
			__( 'GMOP Settings', GMOP_TD ),
			__( 'Settings', GMOP_TD ),
			'manage_options',
			'gmop-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Initialize settings.
	 *
	 * @return void
	 */
	public function init_settings() : void {
		// Section: Google Maps.
		add_settings_section(
			'gmop_settings_section',
			__( 'Google Maps', GMOP_TD ),
			array( $this, 'render_settings_section' ),
			'gmop_settings'
		);

		// Option: Google Maps API Key.
		$this->register_field_api_key();

		// Option: Google Maps Location.
		$this->register_field_gmaps_loc();

		// Option: Default Latitude.
		$this->register_field_default_latitude();

		// Option: Default Longitude.
		$this->register_field_default_longitude();

	}

	/**
	 * Render page.
	 *
	 * @return void
	 */
	public function render_page() : void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'GMOP Settings', GMOP_TD ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'gmop_settings' );
				do_settings_sections( 'gmop_settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render settings section.
	 *
	 * @return void
	 */
	public function render_settings_section() : void {
		esc_html_e( 'Settings for Google Maps objects.', GMOP_TD );
	}

	/**
	 * Register settings field API key.
	 *
	 * @return void
	 */
	public function register_field_api_key() : void {
		$field_name   = 'gmop_api_key';
		$section_name = 'gmop_settings_section';

		register_setting(
			'gmop_settings',
			$field_name,
			array(
				'label'             => __( 'Google Maps API Key', GMOP_TD ),
				'description'       => sprintf( __( 'Get free API key for <a href="%s" target="_new">Google Maps</a>.', GMOP_TD ), 'https://code.google.com/apis/maps/signup.html' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
				'show_in_rest'      => false,
			),
		);

		add_settings_field(
			$field_name,
			__( 'Google Maps API Key', GMOP_TD ),
			array( $this, 'render_field_api_key' ),
			'gmop_settings',
			$section_name,
			array(
				'label_for' => $field_name,
			),
		);
	}

	/**
	 * Register settings field Google Maps Location.
	 *
	 * @return void
	 */
	public function register_field_gmaps_loc() : void {
		$field_name   = 'gmop_gmaps_loc';
		$section_name = 'gmop_settings_section';

		register_setting(
			'gmop_settings',
			$field_name,
			array(
				'label'             => __( 'Google Maps Location', GMOP_TD ),
				'description'       => __( 'Select the Google Maps Location.', GMOP_TD ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
				'show_in_rest'      => false,
			),
		);

		add_settings_field(
			$field_name,
			__( 'Google Maps Location', GMOP_TD ),
			array( $this, 'render_field_gmaps_loc' ),
			'gmop_settings',
			$section_name,
			array(
				'label_for' => $field_name,
			),
		);
	}

	/**
	 * Register settings field Default Latitude.
	 *
	 * @return void
	 */
	public function register_field_default_latitude() : void {
		$field_name   = 'gmop_default_latitude';
		$section_name = 'gmop_settings_section';

		register_setting(
			'gmop_settings',
			$field_name,
			array(
				'label'             => __( 'Default latitude', GMOP_TD ),
				'description'       => __( 'Default latitude on add object page.', GMOP_TD ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
				'show_in_rest'      => false,
			),
		);

		add_settings_field(
			$field_name,
			__( 'Default latitude', GMOP_TD ),
			array( $this, 'render_field_default_latitude' ),
			'gmop_settings',
			$section_name,
			array(
				'label_for' => $field_name,
			),
		);
	}

	/**
	 * Register settings field Default Longitude.
	 *
	 * @return void
	 */
	public function register_field_default_longitude() : void {
		$field_name   = 'gmop_default_longitude';
		$section_name = 'gmop_settings_section';

		register_setting(
			'gmop_settings',
			$field_name,
			array(
				'label'             => __( 'Default longitude', GMOP_TD ),
				'description'       => __( 'Default longitude on add object page.', GMOP_TD ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
				'show_in_rest'      => false,
			),
		);

		add_settings_field(
			$field_name,
			__( 'Default longitude', GMOP_TD ),
			array( $this, 'render_field_default_longitude' ),
			'gmop_settings',
			$section_name,
			array(
				'label_for' => $field_name,
			),
		);
	}

	/**
	 * Render settings field API Key.
	 *
	 * @return void
	 */
	public function render_field_api_key() : void {
		$field_name = 'gmop_api_key';

		$api_key = get_option( $field_name, '' );
		?>
		<input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="<?php echo $api_key; ?>" class="regular-text">
		<p class="description"><?php printf( __( 'Get free API key for <a href="%s" target="_new">Google Maps</a>.', GMOP_TD ), 'https://code.google.com/apis/maps/signup.html' ); ?></p>
		<?php
	}

	/**
	 * Render settings field Google Maps Location.
	 *
	 * @return void
	 */
	public function render_field_gmaps_loc() : void {
		$field_name = 'gmop_gmaps_loc';

		$gmaps_loc = get_option( $field_name, '' );
		?>
		<select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>">
			<?php foreach ( $this->get_domains() as $key ) : ?>
				<option value="<?php echo $key; ?>" <?php selected( $gmaps_loc, $key ); ?>><?php echo $key; ?></option>
			<?php endforeach; ?>
		</select>
		<p class="description"><?php esc_html_e( 'Select the Google Maps Location.', GMOP_TD ); ?></p>
		<?php
	}

	/**
	 * Render settings field Default Latitude.
	 *
	 * @return void
	 */
	public function render_field_default_latitude() : void {
		$field_name = 'gmop_default_latitude';

		$default_latitude = get_option( $field_name, '' );
		?>
		<input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="<?php echo $default_latitude; ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Default latitude on add object page.', GMOP_TD ); ?></p>
		<?php
	}

	/**
	 * Render settings field Default Longitude.
	 *
	 * @return void
	 */
	public function render_field_default_longitude() : void {
		$field_name = 'gmop_default_longitude';

		$default_longitude = get_option( $field_name, '' );
		?>
		<input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="<?php echo $default_longitude; ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Default longitude on add object page.', GMOP_TD ); ?></p>
		<?php
	}

	/**
	 * Get Google Maps domains.
	 *
	 * @return array
	 */
	protected function get_domains() : array {
		$domains = array(
			'https://maps.google.com',
			'https://maps.google.at',
			'https://maps.google.com.au',
			'https://maps.google.com.ba',
			'https://maps.google.be',
			'https://maps.google.com.br',
			'https://maps.google.ca',
			'https://maps.google.ch',
			'https://maps.google.cz',
			'https://maps.google.de',
			'https://maps.google.dk',
			'https://maps.google.es',
			'https://maps.google.fi',
			'https://maps.google.fr',
			'https://maps.google.it',
			'https://maps.google.co.jp',
			'https://maps.google.nl',
			'https://maps.google.no',
			'https://maps.google.co.nz',
			'https://maps.google.pl',
			'https://maps.google.ru',
			'https://maps.google.se',
			'https://maps.google.tw',
			'https://maps.google.co.uk',
		);

		return $domains;
	}

}
