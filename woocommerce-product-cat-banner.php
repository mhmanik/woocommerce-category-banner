<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WC_Admin_Taxonomies' ) ) {
	/**
	 * WP_Admin_Taxonomies_Banner_Img class.
	 */
	class WP_Admin_Taxonomies_Banner_Img extends WC_Admin_Taxonomies {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add form
			add_filter( 'product_cat_add_form_fields', array( $this, 'es_add_category_fields' ), 10 );
			add_filter( 'product_cat_edit_form_fields', array( $this, 'es_edit_category_fields' ), 10, 2 );
			add_filter( 'created_term', array( $this, 'es_save_category_fields' ), 10, 3 );
			add_filter( 'edit_term', array( $this, 'es_save_category_fields' ), 10, 3 );

			// Add columns
			add_filter( 'manage_edit-product_cat_columns', array( $this, 'es_product_cat_columns' ), 10, 1 );
			add_filter( 'manage_product_cat_custom_column', array( $this, 'es_product_cat_column' ), 10, 3 );

			//Styling
			add_action( 'admin_head', array( $this, 'es_product_cat_css' ) );

		}

		/**
		 * Category extra thumbnail fields.
		 *
		 * @access public
		 * @return void
		 */
		public function es_add_category_fields() {
			?>
			<div class="form-field">
				<label><?php _e( 'Banner', 'woocommerce' ); ?></label>
				<div id="product_cat_banner" style="float:left;margin-right:10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
				<div style="line-height:60px;">
					<input type="hidden" id="product_cat_banner_id" name="product_cat_banner_id" />
					<button type="button" class="banner_upload_image_button button"><?php _e( 'Upload/Add image', 'woocommerce' ); ?></button>
					<button type="button" class="banner_remove_image_button button"><?php _e( 'Remove image', 'woocommerce' ); ?></button>
				</div>


			<script type="text/javascript">

				// Only show the "remove image" button when needed
				if ( ! jQuery('#product_cat_banner_id').val() ) {
					jQuery('.banner_remove_image_button').hide();
				}

				// Uploading files
				var file_frame;

				jQuery( document ).on( 'click', '.banner_upload_image_button', function( event ) {

					event.preventDefault();

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( 'Choose an image', 'woocommerce' ); ?>',
						button: {
							text: '<?php _e( 'Use image', 'woocommerce' ); ?>',
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();

						jQuery('#product_cat_banner_id').val( attachment.id );
						jQuery('#product_cat_banner img').attr('src', attachment.url );
						jQuery('.banner_remove_image_button').show();

						file_frame = undefined;

					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery( document ).on( 'click', '.banner_remove_image_button', function( event ) {
					jQuery('#product_cat_banner img').attr('src', '<?php echo esc_url( wc_placeholder_img_src() ); ?>');
					jQuery('#product_cat_banner_id').val('');
					jQuery('.banner_remove_image_button').hide();
					return false;
				});

			</script>
				<div class="clear"></div>
			</div>
			<?php
		}


		/**
		 * Edit extra category thumbnail field.
		 *
		 * @access public
		 * @param mixed $term Term (category) being edited
		 */
		public function es_edit_category_fields( $term, $taxonomy ) {

			$banner_id = absint( get_woocommerce_term_meta( $term->term_id, 'banner_id', true ) );

			if ( $banner_id ) {
				$image = wp_get_attachment_thumb_url( $banner_id );
			} else {
				$image = wc_placeholder_img_src();
			}
			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php _e( 'Banner', 'woocommerce' ); ?></label></th>
				<td>
					<div id="product_cat_banner" style="float:left;margin-right:10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
					<div style="line-height:60px;">
						<input type="hidden" id="product_cat_banner_id" name="product_cat_banner_id" value="<?php echo esc_attr( $banner_id ); ?>" />
						<button type="submit" class="banner_upload_image_button button"><?php _e( 'Upload/Add image', 'woocommerce' ); ?></button>
						<button type="submit" class="banner_remove_image_button button"><?php _e( 'Remove image', 'woocommerce' ); ?></button>
					</div>
					<script type="text/javascript">

						// Uploading files
						var file_frame;

						jQuery( document ).on( 'click', '.banner_upload_image_button', function( event ) {

							event.preventDefault();
							// Create the media frame.
							file_frame = wp.media.frames.downloadable_file = wp.media({
								title: '<?php _e( 'Choose an image', 'woocommerce' ); ?>',
								button: {
									text: '<?php _e( 'Use image', 'woocommerce' ); ?>',
								},
								multiple: false
							});

							// When an image is selected, run a callback.
							file_frame.on( 'select', function() {
								attachment = file_frame.state().get('selection').first().toJSON();

								jQuery('#product_cat_banner_id').val( attachment.id );
								jQuery('#product_cat_banner img').attr('src', attachment.url );
								jQuery('.banner_remove_image_button').show();

								file_frame = undefined;
							});

							// Finally, open the modal.
							file_frame.open();
						});

						jQuery( document ).on( 'click', '.banner_remove_image_button', function( event ) {
							jQuery('#product_cat_banner img').attr('src', '<?php echo esc_url( wc_placeholder_img_src() ); ?>');
							jQuery('#product_cat_banner_id').val('');
							jQuery('.banner_remove_image_button').hide();
							return false;
						});

					</script>
					<div class="clear"></div>
				</td>
			</tr>
			<?php
		}

		/**
		 * save_category_fields function.
		 *
		 * @access public
		 * @param mixed $term_id Term ID being saved
		 * @param mixed $tt_id
		 * @param mixed $taxonomy Taxonomy of the term being saved
		 * @return void
		 */
		public function es_save_category_fields( $term_id, $tt_id, $taxonomy ) {
			if ( filter_input( INPUT_POST , 'product_cat_banner_id', FILTER_SANITIZE_NUMBER_INT ) ) {

				update_woocommerce_term_meta( $term_id, 'banner_id', absint( filter_input( INPUT_POST , 'product_cat_banner_id', FILTER_SANITIZE_NUMBER_INT ) ) );
			}
		}

		/**
		 * Thumbnail column added to category admin.
		 *
		 * @access public
		 * @param mixed $columns
		 * @return array
		 */
		public function es_product_cat_columns( $columns ) {
			$new_columns          = array();
			$new_columns['cb']    = $columns['cb'];
			$new_columns['thumb']    = $columns['thumb'];
			$new_columns['banner'] = __( 'Banner', 'woocommerce' );

			unset( $columns['cb'] );
			unset( $columns['thumb'] );

			return array_merge( $new_columns, $columns );
		}

		/**
		 * Thumbnail column value added to category admin.
		 *
		 * @access public
		 * @param mixed $columns
		 * @param mixed $column
		 * @param mixed $id
		 * @return array
		 */
		public function es_product_cat_column( $columns, $column, $id ) {

			if ( 'banner' == $column ) {

				$image 			= '';
				$banner_id 	= get_woocommerce_term_meta( $id, 'banner_id', true );

				if ( $banner_id ) {
					$image = wp_get_attachment_thumb_url( $banner_id );
				}else {
					$image = wc_placeholder_img_src();
				}

				$image = str_replace( ' ', '%20', $image );

				$columns .= '<img src="' . esc_url( $image ) . '" alt="' . __( 'Thumbnail', 'woocommerce' ) . '" class="wp-post-image" height="48" width="48" />';

			}

			return $columns;
		}

		/**
		 * CSS.
		 *
		 * @access public
		 */
		public function es_product_cat_css() {
			echo '
				<style>
					table.wp-list-table .column-banner {
						width: 52px;
						text-align: center;
						white-space: nowrap;
					}

					table.wp-list-table td.column-banner img {
						margin: 0;
						vertical-align: middle;
						width: auto;
						height: auto;
						max-width: 40px;
						max-height: 40px;
					}
				</style>';
		}
	}
	new WP_Admin_Taxonomies_Banner_Img();
}
