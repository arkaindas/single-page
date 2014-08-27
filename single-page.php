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
		add_action( 'init',array(&$this,'sp_register_post_type_section')  );
		add_filter( 'post_updated_messages',array(&$this,'sp_section_updated_messages')  );
		add_action( 'admin_head',array(&$this,'sp_section_preview_changes_hide')  );
		add_filter('manage_edit-section_columns', array(&$this,'add_new_section_columns') );
		add_action('manage_section_posts_custom_column', array(&$this,'custom_column_data') );
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
			'supports'           => array( 'title', 'editor', 'thumbnail' )
		);
		register_post_type( 'section', $args );
	}

	function sp_section_updated_messages( $messages ) {
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

	function sp_section_preview_changes_hide(){
		$post             = get_post();
		$post_type        = get_post_type( $post );
		if( $post_type == "section" ) {
			$post_slug = $post->post_name;
			$shortcode = "<strong>Shortcode:</strong><span class='add-new-h2'> [single-page ".$post_slug."] </span>";
		?>
			<style>
				#minor-publishing-actions,#view-post-btn { display:none; }
			</style>
			<script>
			jQuery(document).ready(function($){
				jQuery("#edit-slug-box").html("<?php echo $shortcode; ?>");
				jQuery('table#test tbody').sortable();
			});
			</script>

		<?php
		}
	}

	function custom_column_data($column_name) {
		if( $column_name == 'my_slug' ) {
			$post             = get_post();
			$post_slug 	  = $post->post_name;
			$shortcode = "<span ><strong> [single-page ".$post_slug."] </strong></span>";
			echo $shortcode;
		}
	}

	function add_new_section_columns($section_columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = _x('Section Name', 'column name');
		$new_columns['my_slug'] = __('Shortcode');
		$new_columns['date'] = _x('Date', 'column name');

		return $new_columns;
	}

}
new SINGLEPAGE;
?>

