<?php
/*
Plugin Name: Featured Comments
Plugin URI: http://pippinsplugins.com/featured-comments
Description: Lets the admin add "featured" or "buried" css class to selected comments. Handy to highlight comments that add value to your post. Also includes a Featured Comments widget
Version: 1.2
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
Contributors: mordauk, Utkarsh

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Online: http://www.gnu.org/licenses/gpl.txt
*/


final class Featured_Comments {


	/** Singleton *************************************************************/

	/**
	 * @var Featured_Comments
	 */
	private static $instance;

	private static $actions;


	/**
	 * Main Featured_Comments Instance
	 *
	 * Insures that only one instance of Featured_Comments exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since v1.0
	 * @staticvar array $instance
	 * @see pw_featured_comments_load()
	 * @return The one true Featured_Comments
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Featured_Comments;
			self::$instance->includes();
			self::$instance->init();
			self::$instance->load_textdomain();
		}
		return self::$instance;
	}

	private function includes() {
		include_once( dirname( __FILE__ ) . '/widget.php' );
	}

	/** Filters & Actions **/
	private function init() {

		self::$actions = array(
			'feature'   => __( 'Feature',   'featured-comments' ),
			'unfeature' => __( 'Unfeature', 'featured-comments' ),
			'bury'      => __( 'Bury',      'featured-comments' ),
			'unbury'    => __( 'Unbury',    'featured-comments' )
		);

		/* Backend */
		add_action( 'edit_comment',             array( $this, 'save_meta_box_postdata' ) );
		add_action( 'admin_menu',               array( $this, 'add_meta_box'           ) );
		add_action( 'wp_ajax_feature_comments', array( $this, 'ajax'                   ) );
		add_filter( 'comment_text',             array( $this, 'comment_text'           ), 10, 3 );
		add_filter( 'comment_row_actions',      array( $this, 'comment_row_actions'    ) );

		add_action( 'wp_print_scripts',         array( $this, 'print_scripts'          ) );
		add_action( 'admin_print_scripts',      array( $this, 'print_scripts'          ) );
		add_action( 'wp_print_styles',          array( $this, 'print_styles'           ) );
		add_action( 'admin_print_styles',       array( $this, 'print_styles'           ) );

		/* Frontend */
		add_filter( 'comment_class',            array( $this, 'comment_class'          ) );

	}

	function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters( 'featured_comments_languages_directory', $lang_dir );


		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'featured-comments' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'edd', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/featured-comments/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/featured-comments folder
			load_textdomain( 'featured-comments', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/featured-comments/languages/ folder
			load_textdomain( 'featured-comments', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'featured-comments', false, $lang_dir );
		}

	}

	// Scripts
	function print_scripts() {
		if ( current_user_can( 'moderate_comments' ) ) {
			wp_enqueue_script( 'featured_comments', plugin_dir_url( __FILE__ ) . 'feature-comments.js', array( 'jquery' ) );
			wp_localize_script( 'featured_comments', 'featured_comments', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	// Styles
	function print_styles() {
		if ( current_user_can( 'moderate_comments' ) ) {
?>
			<style>
				.feature-comments.unfeature, .feature-comments.unbury {display:none;}
				.feature-comments { cursor:pointer;}
				.featured.feature-comments.feature { display:none;}
				.featured.feature-comments.unfeature { display:inline;}
				.buried.feature-comments.bury { display:none;}
	            .buried.feature-comments.unbury { display:inline;}
				#the-comment-list tr.featured { background-color: #dfd; }
				#the-comment-list tr.buried { opacity: 0.5; }
			</style>
<?php
		}
	}


	function ajax() {

		if ( !isset( $_POST['do'] ) ) die;

		$action = $_POST['do'];

		$actions = array_keys( self::$actions );

		if ( in_array( $action, $actions ) ) {

			$comment_id = absint( $_POST['comment_id'] );

			if ( ! $comment = get_comment( $comment_id ) || ! current_user_can( 'edit_post', $comment->comment_post_ID ) )
				die;

			switch ( $action ) {

				case 'feature':
					add_comment_meta( $comment_id, 'featured', '1' );
					break;

				case 'unfeature':
					delete_comment_meta( $comment_id, 'featured' );
					break;

				case 'bury':
                    add_comment_meta( $comment_id, 'buried', '1');
                break;

                case 'unbury':
                    delete_comment_meta( $comment_id, 'buried', '0');
                break;

			}
		}
		die;
	}

	function comment_text( $comment_text ) {
		if( is_admin() || ! current_user_can( 'moderate_comments' ) ) return $comment_text;

		global $comment;

		$comment_id = $comment->comment_ID;
		$data_id    = ' data-comment_id=' . $comment_id;

		$current_status = implode( ' ', self::comment_class() );
		$o = '<br/>';
		foreach( self::$actions as $action => $label )
		    $o .= "<a class='feature-comments {$current_status} {$action}' data-do='{$action}' {$data_id} title='{$label}'>{$label}</a> ";

		return $comment_text . $o;
    }

	function comment_row_actions( $actions ) {

		global $comment, $post, $approve_nonce;

		$comment_id = $comment->comment_ID;

		$data_id = ' data-comment_id=' . $comment->comment_ID;

		$current_status = implode( ' ', self::comment_class() );

		$o = '';
		$o .= "<a data-do='unfeature' {$data_id} class='feature-comments unfeature {$current_status} dim:the-comment-list:comment-{$comment->comment_ID}:unfeatured:e7e7d3:e7e7d3:new=unfeatured vim-u' title='" . esc_attr__( 'Unfeature this comment', 'featured-comments' ) . "'>" . __( 'Unfeature', 'featured-comments' ) . '</a>';
		$o .= "<a data-do='feature' {$data_id} class='feature-comments feature {$current_status} dim:the-comment-list:comment-{$comment->comment_ID}:unfeatured:e7e7d3:e7e7d3:new=featured vim-a' title='" . esc_attr__( 'Feature this comment', 'featured-comments' ) . "'>" . __( 'Feature', 'featured-comments' ) . '</a>';
		$o .= ' | ';
		$o .= "<a data-do='unbury' {$data_id} class='feature-comments unbury {$current_status} dim:the-comment-list:comment-{$comment->comment_ID}:unburied:e7e7d3:e7e7d3:new=unburied vim-u' title='" . esc_attr__( 'Unbury this comment', 'featured-comments' ) . "'>" . __( 'Unbury', 'featured-comments' ) . '</a>';
		$o .= "<a data-do='bury' {$data_id}  class='feature-comments bury {$current_status} dim:the-comment-list:comment-{$comment->comment_ID}:unburied:e7e7d3:e7e7d3:new=buried vim-a' title='" . esc_attr__( 'Bury this comment', 'featured-comments' ) . "'>" . __( 'Bury', 'featured-comments' ) . '</a>';
		$o = "<span class='$current_status'>$o</span>";

		$actions['feature_comments'] = $o;

		return $actions;
	}

	function add_meta_box() {
		add_meta_box( 'comment_meta_box', __( 'Featured Comments', 'featured-comments' ), array( $this, 'comment_meta_box' ), 'comment', 'normal' );
	}

	function save_meta_box_postdata( $comment_id ) {

		if ( ! wp_verify_nonce( $_POST['featured_comments_nonce'], plugin_basename( __FILE__ ) ) )
			return;

		if ( !current_user_can( 'moderate_comments', $comment_id ) )
			comment_footer_die( __( 'You are not allowed to edit comments on this post.', 'featured-comments' ) );

		update_comment_meta( $comment_id, 'featured', isset( $_POST['featured'] ) ? '1' : '0' );
	}

	function comment_meta_box() {

		global $comment;
		$comment_id = $comment->comment_ID;
		echo '<p>';
		echo wp_nonce_field( plugin_basename( __FILE__ ), 'featured_comments_nonce' );
		echo '<input id = "featured" type="checkbox" name="featured" value="true"' . checked( true, self::is_comment_featured( $comment_id ), false ) . '/>';
		echo ' <label for="featured">' . __( "Featured", 'featured-comments' ) . '</label>';
		echo '</p>';
	}

	function comment_class( $classes = array() ) {
		global $comment;

		$comment_id = $comment->comment_ID;

		if ( self::is_comment_featured( $comment_id ) )
			$classes[] = 'featured';

		if( self::is_comment_buried( $comment_id ) )
			$classes [] = 'buried';

		return $classes;
	}

	private function is_comment_featured( $comment_id ) {
		if ( '1' == get_comment_meta( $comment_id, 'featured', true ) )
			return 1;
		return 0;
	}


	private static function is_comment_buried( $comment_id ) {
	    if( '1' == get_comment_meta( $comment_id, 'buried', true ) )
	        return 1;
	    return 0;
	}

}


function wp_featured_comments_load() {
	return Featured_Comments::instance();
}

// load Easy Featured Comments
wp_featured_comments_load();