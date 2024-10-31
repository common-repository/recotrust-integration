<?php
// The output class
class Ac_Reco_Output {

	// Main constructor
	public function __construct( $output ) {
		$this->output( $output );
	}
	// Display the widget
	public static function output( $instance ) {

		// Check the widget options
		$partner  = get_option( 'woocommerce_reco_plugin_reco_id' );
		$width    = isset( $instance['width'] ) ? $instance['width'] : '100%';
		$height   = isset( $instance['height'] ) ? $instance['height'] : '240px';
		$cssclass = isset( $instance['cssclass'] ) ? $instance['cssclass'] : '';
		$bgcolor  = isset( $instance['bgcolor'] ) ? $instance['bgcolor'] : 'ffffff';
		$select   = isset( $instance['select'] ) ? $instance['select'] : 'HORIZONTAL_QUOTE';
		$badges   = isset( $instance['badges'] ) ? $instance['badges'] : '';
		if ( empty( $partner ) ) {
			return;
		}
		extract( $instance['args'] );
		// WordPress core before_widget hook (always include )
		echo $before_widget;

		// Display the widget
		echo '<div class="widget-text wp_widget_plugin_box">';

			echo '<iframe id="reco_se" src="https://widget.reco.se/v2/widget/' . $partner . '?mode=' . $select . '&bg=' . $bgcolor . '" width="' . $width . '" height="' . $height . '" scrolling="no" style="border:0;"></iframe>';

		// print_r($args);  print_r($instance);
		if ( $instance['badges'] ) {
			foreach ( $instance['badges'] as $badge => $is_ok ) {
				echo '<div id="reco--badge-' . $badge . '"></div><script src="https://widget.reco.se/badge/' . $badge . '/' . $partner . '.js"></script>';
			}
		}

		echo '</div>';

		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}

}
