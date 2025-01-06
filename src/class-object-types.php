<?php
namespace meloniq\GMOP;

class Object_Types {

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
			__( 'Object Types', GMOP_TD ),
			__( 'Object Types', GMOP_TD ),
			'manage_options',
			'gmop-object-types',
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
		require_once( dirname( __FILE__ ) . '/class-object-type-list.php' );
		require_once( dirname( __FILE__ ) . '/class-object-type-add.php' );
		require_once( dirname( __FILE__ ) . '/class-object-type-edit.php' );

		$action = ! empty( $_GET['gmopaction'] ) ? $_GET['gmopaction'] : '';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Object Types', GMOP_TD ); ?></h1>
			<?php
				if ( $action == 'add' ) {
					$page = new Object_Type_Add();
				} else if ( $action == 'edit' ) {
					$page = new Object_Type_Edit();
				} else {
					$page = new Object_Type_List();
				}
			$page->render_page();
			?>
		</div>
		<?php
	}


}
