<?php
// The widget class
class Ac_Reco_Widget extends WP_Widget {


	// Main constructor
	public function __construct() {
		parent::__construct(
			'ac_reco_widget',
			__( 'Recotrust', 'woocommerce-reco-plugin' ),
			[
				'customize_selective_refresh' => true,
				'description'                 => __( 'This will display Recotrust recommendations on your woocommerce site.', 'woocommerce-reco-plugin' ),
				'help'                        => 'http://www.recotrust.se/',
			]
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {

		// Set widget defaults
		$badges   = [];
		$defaults = [
			'width'    => '',
			'height'   => '',
			'cssclass' => '',
			'bgcolor'  => '',
			'select'   => '',
		];
		for ( $i = date( 'Y' );$i >= ( date( 'Y' ) - 5 );$i-- ) {
			$defaults['badges'][ $i ] = $i;
		}

		// Parse current settings with defaults
		$current = wp_parse_args( (array) $instance, $defaults );
		extract( $current );
		?>
		<?php // Style options ?>
		 <p>
			<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Widget Style', 'woocommerce-reco-plugin' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'select' ); ?>" id="<?php echo $this->get_field_id( 'select' ); ?>" class="widefat wc-enhanced-select">
			<?php
			// options array
			$options = [
				''                 => __( 'Select', 'woocommerce-reco-plugin' ),
				'MINI'             => __( 'Mini', 'woocommerce-reco-plugin' ),
				'SMALL'            => __( 'Small', 'woocommerce-reco-plugin' ),
				'TALL'             => __( 'High with Recos', 'woocommerce-reco-plugin' ),
				'HORIZONTAL_QUOTE' => __( 'Horizontal with Recos', 'woocommerce-reco-plugin' ),
				'HORIZONTAL'       => __( 'Horizontal', 'woocommerce-reco-plugin' ),
			];

			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" ' . selected( $select, $key, false ) . '>' . $name . '</option>';

			}
			?>
			</select>
		 </p>

		<?php // Width ?>
	 <p>
	  <label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php _e( 'Width', 'woocommerce-reco-plugin' ); ?></label>
	  <input required class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" />
	 </p>


		<?php // Height ?>
	<p>
	 <label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php _e( 'Height', 'woocommerce-reco-plugin' ); ?></label>
	 <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>" />
	</p>


		<?php // CSS Class ?>
	 <p>
	  <label for="<?php echo esc_attr( $this->get_field_id( 'cssclass' ) ); ?>"><?php _e( 'Custom CSS Class (optional)', 'woocommerce-reco-plugin' ); ?></label>
	  <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cssclass' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cssclass' ) ); ?>" type="text" value="<?php echo esc_attr( $cssclass ); ?>" />
	 </p>

		<?php // BG Color ?>
	 <p>
	  <label for="<?php echo esc_attr( $this->get_field_id( 'bgcolor' ) ); ?>"><?php _e( 'Custom Background Color (optional)', 'woocommerce-reco-plugin' ); ?></label>
	  <input class="widefat wp-picker-container" id="<?php echo esc_attr( $this->get_field_id( 'bgcolor' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bgcolor' ) ); ?>" type="text" value="<?php echo esc_attr( $bgcolor ); ?>" />
	</p>
			<?php
			// Badges.
			for ( $i = date( 'Y' );$i >= ( date( 'Y' ) - 5 );$i-- ) {
				?>
		  <!--<p>
		   <input id="<?php echo esc_attr( $this->get_field_id( 'badges[' . $i . ']' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'badges[' . $i . ']' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $current['badges'][ $i ] ); ?> />
		   <label for="<?php echo esc_attr( $this->get_field_id( 'badges[' . $i . ']' ) ); ?>"><?php _e( 'Badge for ' . $i, 'woocommerce-reco-plugin' ); ?></label>
		  </p>-->
			<?php } ?>


	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['width']    = isset( $new_instance['width'] ) ? wp_strip_all_tags( $new_instance['width'] ) : '100%';
		$instance['height']   = isset( $new_instance['height'] ) ? wp_strip_all_tags( $new_instance['height'] ) : '240px';
		$instance['cssclass'] = isset( $new_instance['cssclass'] ) ? wp_strip_all_tags( $new_instance['cssclass'] ) : '';
		$instance['bgcolor']  = isset( $new_instance['bgcolor'] ) ? wp_strip_all_tags( $new_instance['bgcolor'] ) : 'ffffff';
		$instance['select']   = isset( $new_instance['select'] ) ? wp_strip_all_tags( $new_instance['select'] ) : 'HORIZONTAL_QUOTE';
		$instance['badges']   = isset( $new_instance['badges'] ) ? $new_instance['badges'] : '';
		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {
		$instance['args'] = $args;
		Ac_Reco_Output::output( $instance );
	}

}

?>
