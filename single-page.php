<?php
/*
Plugin Name: Single Page
Description: Simple Content Holder For Single Page Website.
Author: Arkaprava Majumder
Version: 0.1
Author Uri: http://arkapravamajumder.com
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class SINGLEPAGE {

	function __construct() {
		add_action( 'init', array(&$this, 'sp_register_post_type_section') );
		add_action( 'init', array(&$this, 'sp_register_post_type_portfolio') );
		add_action( 'init', array(&$this, 'sp_register_post_type_client') );
		add_action( 'init',array(&$this,'sp_register_taxonomy_portfolio_category')  );
		add_action( 'init',array(&$this,'sp_register_taxonomy_client_category')  );
		add_filter( 'post_updated_messages', array(&$this, 'sp_updated_messages') );
		add_action( 'admin_head', array(&$this, 'sp_preview_changes_hide') );
		add_filter( 'manage_edit-section_columns', array(&$this, 'sp_add_new_section_columns') );
		add_action( 'manage_section_posts_custom_column', array(&$this, 'sp_grid_modify') );
		add_filter( 'get_sample_permalink_html', array(&$this,'sp_modify_permalink') );
		add_filter( 'pre_get_shortlink', array(&$this,'sp_hide_get_shortlink') );
		add_action( 'admin_menu', array(&$this,'sp_option_menu_page') ); 
		add_shortcode( 'single-page', array(&$this, 'sp_shortcode') );
	}
	
	function sp_register_post_type_section() {
		$labels = array(
			'name'               => _x( 'Sections', 'post type general name', 'single-page' ),
			'singular_name'      => _x( 'Section', 'post type singular name', 'single-page' ),
			'menu_name'          => _x( 'Single Page', 'admin menu', 'single-page' ),
			'name_admin_bar'     => _x( 'Single Page', 'add new on admin bar', 'single-page' ),
			'add_new'            => _x( 'Add New Section', 'Product', 'single-page' ),
			'add_new_item'       => __( 'Add New Section', 'single-page' ),
			'new_item'           => __( 'New Section', 'single-page' ),
			'edit_item'          => __( 'Edit Section', 'single-page' ),
			'view_item'          => __( 'View Section', 'single-page' ),
			'all_items'          => __( 'All Sections', 'single-page' ),
			'search_items'       => __( 'Search Sections', 'single-page' ),
			'parent_item_colon'  => __( 'Parent Sections:', 'single-page' ),
			'not_found'          => __( 'No Sections found.', 'single-page' ),
			'not_found_in_trash' => __( 'No Sections found in Trash.', 'single-page' ),
		);
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'section' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' )
		);
		register_post_type( 'section', $args );
	}
	function sp_register_taxonomy_portfolio_category() {
		$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Category' ),
		'all_items'         => __( 'Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ),
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Category Name' ),
		'menu_name'         => __( 'Portfolio Categories' ),
	);

		$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'portfolio-category' ),
		);

		register_taxonomy( 'portfolio-category', array( 'portfolio' ), $args );
	}
	function sp_register_taxonomy_client_category() {
		$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Category' ),
		'all_items'         => __( 'Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ),
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Category Name' ),
		'menu_name'         => __( 'Client Categories' ),
	);

		$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'client-category' ),
		);

		register_taxonomy( 'client-category', array( 'client' ), $args );
	

	}
	function sp_updated_messages( $messages ) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages['section'] = array(
			0  => '', 
			1  => __( 'Section updated.', 'single-page' ),
			2  => __( 'Custom field updated.', 'single-page' ),
			3  => __( 'Custom field deleted.', 'single-page' ),
			4  => __( 'Section updated.', 'single-page' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Section restored to revision from %s', 'single-page' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Section published.', 'single-page' ),
			7  => __( 'Section saved.', 'single-page' ),
			8  => __( 'Section submitted.', 'single-page' ),
			9  => sprintf(
				__( 'Section scheduled for: <strong>%1$s</strong>.', 'single-page' ),
				date_i18n( __( 'M j, Y @ G:i', 'single-page' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Section draft updated.', 'single-page' ),
		);
		
		$messages['portfolio'] = array(
			0  => '', 
			1  => __( 'Portfolio updated.', 'single-page' ),
			2  => __( 'Custom field updated.', 'single-page' ),
			3  => __( 'Custom field deleted.', 'single-page' ),
			4  => __( 'Portfolio updated.', 'single-page' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Portfolio restored to revision from %s', 'single-page' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Portfolio published.', 'single-page' ),
			7  => __( 'Portfolio saved.', 'single-page' ),
			8  => __( 'Portfolio submitted.', 'single-page' ),
			9  => sprintf(
				__( 'Portfolio scheduled for: <strong>%1$s</strong>.', 'single-page' ),
				date_i18n( __( 'M j, Y @ G:i', 'single-page' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Portfolio draft updated.', 'single-page' ),
		);
		$messages['client'] = array(
			0  => '', 
			1  => __( 'Client updated.', 'single-page' ),
			2  => __( 'Custom field updated.', 'single-page' ),
			3  => __( 'Custom field deleted.', 'single-page' ),
			4  => __( 'Client updated.', 'single-page' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Portfolio restored to revision from %s', 'single-page' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Client published.', 'single-page' ),
			7  => __( 'Client saved.', 'single-page' ),
			8  => __( 'Client submitted.', 'single-page' ),
			9  => sprintf(
				__( 'Client scheduled for: <strong>%1$s</strong>.', 'single-page' ),
				date_i18n( __( 'M j, Y @ G:i', 'single-page' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Client draft updated.', 'single-page' ),
		);
		if ( $post_type_object->publicly_queryable ) {
			$permalink = get_permalink( $post->ID );

			$view_link = "";
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;

			$preview_link = "";
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;
		}

		return $messages;
	}
	function sp_register_post_type_portfolio() {
		$labels = array(
			'name'               => _x( 'Portfolio', 'post type general name', 'single-page' ),
			'singular_name'      => _x( 'Portfolio', 'post type singular name', 'single-page' ),
			'menu_name'          => _x( 'Portfolio', 'admin menu', 'single-page' ),
			'name_admin_bar'     => _x( 'Portfolio', 'add new on admin bar', 'single-page' ),
			'add_new'            => _x( 'Add New Portfolio', 'Product', 'single-page' ),
			'add_new_item'       => __( 'Add New Portfolio', 'single-page' ),
			'new_item'           => __( 'New Portfolio', 'single-page' ),
			'edit_item'          => __( 'Edit Portfolio', 'single-page' ),
			'view_item'          => __( 'View Portfolio', 'single-page' ),
			'all_items'          => __( 'Portfolio', 'single-page' ),
			'search_items'       => __( 'Search Portfolio', 'single-page' ),
			'parent_item_colon'  => __( 'Parent Portfolio:', 'single-page' ),
			'not_found'          => __( 'No portfolio found.', 'single-page' ),
			'not_found_in_trash' => __( 'No portfolio found in Trash.', 'single-page' ),
		);
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'portfolio' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'show_in_menu'  => 'edit.php?post_type=section',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' )
		);
		register_post_type( 'portfolio', $args );
	}
	function sp_register_post_type_client() {
		$labels = array(
			'name'               => _x( 'Clients', 'post type general name', 'single-page' ),
			'singular_name'      => _x( 'Client', 'post type singular name', 'single-page' ),
			'menu_name'          => _x( 'Clients', 'admin menu', 'single-page' ),
			'name_admin_bar'     => _x( 'Clients', 'add new on admin bar', 'single-page' ),
			'add_new'            => _x( 'Add New Client', 'Product', 'single-page' ),
			'add_new_item'       => __( 'Add New Client', 'single-page' ),
			'new_item'           => __( 'New Client', 'single-page' ),
			'edit_item'          => __( 'Edit Client', 'single-page' ),
			'view_item'          => __( 'View Client', 'single-page' ),
			'all_items'          => __( 'Clients', 'single-page' ),
			'search_items'       => __( 'Search Client', 'single-page' ),
			'parent_item_colon'  => __( 'Parent Client:', 'single-page' ),
			'not_found'          => __( 'No client found.', 'single-page' ),
			'not_found_in_trash' => __( 'No client found in Trash.', 'single-page' )
		);
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'client' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'show_in_menu'  => 'edit.php?post_type=section',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' )
		);
		register_post_type( 'client', $args );
	}

	function sp_preview_changes_hide(){
		$post		  = get_post();
		$post_type        = get_post_type( $post );
		if( ( $post_type == 'section' ) || ( $post_type == 'portfolio' ) || ( $post_type == 'client' ) ){
		?>
			<style>
				#minor-publishing-actions,#view-post-btn { display:none; }
			</style>
		<?php
		}
	}

	function sp_grid_modify($column_name) {
		if( $column_name == 'my_slug' ) {
			$post             = get_post();
			$post_id 	  = $post->ID;
			$shortcode = "<strong><a class='thickbox'> [single-page section=".$post_id."] </a></strong>";
			echo $shortcode;
		}
	}

	function sp_add_new_section_columns($section_columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = _x('Section Name', 'column name');
		$new_columns['my_slug'] = __('Shortcode');
		$new_columns['date'] = _x('Date', 'column name');

		return $new_columns;
	}
	function sp_shortcode( $atts ) {
		$section="";
		$post_id=$atts['section'];
		if( !empty( $post_id ) ) {
			$post_data = get_post($post_id); 
			$section = $post_data->post_content;
			if( empty( $section ) ) {
				$section="No Data Found For This Section!";
			}
		} else {
			$section="No Section Found!";
		}
		return $section;
	}
	function sp_modify_permalink($in) {
		global $post;
		switch($post->post_type){
			case "section":
				$post_id= $post->ID;
				if(!empty($post_id)) {
					$out = "<strong>Shortcode:</strong><a class='thickbox'> [single-page section=".$post_id."] </a>";
				} else {
					$out = "";
				}
			break;
			
			case "portfolio":
			$out = "";
			break;
				
			case "client":
			$out = "";
			break;		
			
			default:
			$out=$in;
		
		}
		return $out;	
	}
	function sp_hide_get_shortlink($in) {
		global $post;
		if( ( $post->post_type == 'section' ) || ( $post->post_type == 'portfolio' ) || ( $post->post_type == 'client' ) ){
			$out="";
		} else {
			$out=$in;		
		}
		return $out;
	}
	function sp_option_menu_page() {
		add_submenu_page('edit.php?post_type=section', __('Single Page Options','sp-options'), __('Options','sp-options'), 'manage_options', 'options', 'sp_options');
		function sp_options() {
			?>
			    <div class="wrap">
				<?php screen_icon('themes'); ?> <h2>Single Page Options</h2>
			 	<form method="POST" action="">
				    <table class="form-table">
					<tr valign="top">
					    <th scope="row">
						<label for="num_elements">
						    Number of elements on a row:
						</label>
					    </th>
					    <td>
						<input type="text" name="num_elements" size="25" />
					    </td>
					</tr>
				    </table>
				</form>
			    </div>	
			<?php		
		}
	}
}
new SINGLEPAGE;
?>
