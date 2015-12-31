<?php
/**
 * Plugin Name: Alternative Featured Image for EDD
 * Description: Provide a second Featured Image for downloads for the Shop page
 */
 
 // Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

 if ( ! class_exists( 'EDD_Alternative_Image' ) ) {
 	class EDD_Alternative_Image {
 		
		private static $instance;
 		
		public static function instance() {
            if( ! self::$instance ) {
                self::$instance = new EDD_Alternative_Image();
				self::$instance->setup_hooks();
            }

            return self::$instance;
        }
		
		public function setup_hooks() {
			add_action( 'add_meta_boxes', array( $this, 'featured_image_metabox_callback' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'save_post', array( $this, 'eai_image_save_meta' ) );
			add_filter( 'edd_get_template_part', array( $this, 'filter_template_content_image' ), 10, 3 );
			add_filter( 'edd_template_paths', array( $this, 'register_template_path' ) );
		}
		
		public function filter_template_content_image( $templates, $slug, $name ) {
			if ( 'content-image' === $name ) {
				return array( 'shortcode-alternative-image.php' );
			}
			
			return $templates;
		}
		
		public function register_template_path( $file_paths ) {
			$file_paths[22] = dirname( __FILE__ ) . '/templates';
			
			return $file_paths;
		}
		
		public function enqueue_scripts( $hook ) {
			if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
				// TODO: Limit only to downloads, post.php doesn't set CPT
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'thickbox' );
				wp_enqueue_style( 'thickbox' );
				
				//for new media uploader
				wp_enqueue_media();
				wp_enqueue_script( 'media-upload' );

				wp_enqueue_script( 'eai-featured-image', plugins_url( 'assets/js/alternative-featured-image.js', __FILE__ ), array('jquery') );
			}
		}
		
		public function featured_image_metabox_callback( $post_type ) {
			add_meta_box(
				'eai_featured_image',
				__( 'Download Alternative Image', 'eai' ),
				array( $this, 'eai_featured_image' ),
				'download',
				'side',
				'low'
			);
		}
		
		public function eai_featured_image( $post_id, $metabox ) {
			$post_id = get_the_ID();
			$post_meta = get_post_custom( $post_id );
			$alternative_img = ( is_array( $post_meta ) && ! empty( $post_meta[ 'eai-featured-image' ][0] ) ) ? $post_meta[ 'eai-featured-image' ][0] : '';
			$alternative_img_id = ( is_array( $post_meta ) && ! empty( $post_meta[ 'eai-featured-image-id' ][0] ) ) ? $post_meta[ 'eai-featured-image-id' ][0] : '';
			$alternative_img_view = ! empty( $alternative_img ) ? '<img src="'. $alternative_img .'" style="width:100%">' : '';
			?>
			<p class="hide-if-no-js"></p>
			<input type="hidden" id="eai-image-id" name="eai-featured-image-id" value="<?php echo $alternative_img_id; ?>" />
			<input type="hidden" id="eai-image" name="eai-featured-image" value="<?php echo $alternative_img; ?>" />
	
			<?php
				if ( empty( $alternative_img ) ) {
					echo '<a title="Set an alternative image" href="#" id="set-post-sub-thumbnail" class="eai_thickbox">Set sub-featured image</a>';
				}
			?>
			
			<p></p>
			<div id="eai-image-view" style="border:0"><?php echo $alternative_img_view ?></div>
	
			<?php
			if ( ! empty( $alternative_img ) ) {
				?>
				<a title="remove sub-featured image" href="#" id="remove-post-sub-thumbnail" class="eai_thickbox">Remove sub-featured image</a>
				<?php
			}
		}

		public function eai_image_save_meta( $post_id ) {
			// Avoid autosaves
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
				
			// If the custom field is found, update the postmeta record
			// Also, filter the HTML just to be safe
			if ( isset( $_POST['eai-featured-image']  ) ) {
				update_post_meta( $post_id, 'eai-featured-image',  esc_html( $_POST['eai-featured-image'] ) );
			}
			if ( isset( $_POST['eai-featured-image-id']  ) ) {
				update_post_meta( $post_id, 'eai-featured-image-id',  esc_html( $_POST['eai-featured-image-id'] ) );
			}
		
		}
 	}

	EDD_Alternative_Image::instance();
}
