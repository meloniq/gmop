<?php
namespace meloniq\GMOP;

class Objects {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
	}

	/**
	 * Add menu page.
	 *
	 * @return void
	 */
	public function add_menu_page() : void {
		add_submenu_page(
			'gmop',
			__( 'Objects', GMOP_TD ),
			__( 'Objects', GMOP_TD ),
			'manage_options',
			'gmop-objects',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render page.
	 *
	 * @return void
	 */
	public function render_page() : void {
		// load classes
		require_once( dirname( __FILE__ ) . '/class-object-list.php' );
		require_once( dirname( __FILE__ ) . '/class-object-add.php' );
		require_once( dirname( __FILE__ ) . '/class-object-edit.php' );
		require_once( dirname( __FILE__ ) . '/class-object-cache.php' );

		$action = ! empty( $_GET['gmopaction'] ) ? $_GET['gmopaction'] : '';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Objects', GMOP_TD ); ?></h1>
			<?php
				if ( $action == 'add' ) {
					$page = new Object_Add();
				} else if ( $action == 'edit' ) {
					$page = new Object_Edit();
				} else if ( $action == 'regenerate' ) {
					$page = new Object_Cache();
				} else {
					$page = new Object_List();
				}
			$page->render_page();
			?>
		</div>
		<?php
	}

}
