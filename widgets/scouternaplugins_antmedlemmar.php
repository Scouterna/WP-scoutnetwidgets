<?php
/*
 * Widget som visar antalet medlemmar
 */

 
class ScouternaPlugins_Scoutnet_AntMedlemmar_Widget extends WP_Widget {
 
public function __construct() {
	parent::__construct('scouternaplugins_antmedlemmar_widget',__( 'Visar antalet medlemmar', 'scouternaplugins' ),
		array('classname'   => 'scouternaplugins_antmedlemmar_widget','description' => __( 'En widget som visar antalet medlemmar i k&aring;ren p&aring; lite olika s&auml;tt.', 'scouternaplugins' ))
	);
	load_plugin_textdomain( 'scouternaplugins', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
 
/**  
 * Front-end display of widget.
 *
 * @see WP_Widget::widget()
 *
 * @param array $args Widget arguments.
 * @param array $instance Saved values from database.
 */

public function widget( $args, $instance ) {
	wp_enqueue_style( 'ScouternaPlugins_Scoutnet_AntMedlemmar_Widget-style', plugins_url('css/bars.css', __FILE__) );
	$r_settings = esc_attr($instance['r_settings']);
	$title = esc_attr($instance['title']);
	extract( $args );

	echo $before_widget;

	if (!empty($instance['title']))
		echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];

	switch($r_settings) {
	case 3:
		echo "<p style='font-size:2em;text-align:center;'>". ScouternaPlugins_ScoutnetGetMemberscount() ." medlemmar</p><p style='font-size:1.75em;text-align:center;'>varav ". count(ScouternaPlugins_ScoutnetGetLeaders()) ." ledare,</p>";
		$decoded = ScouternaPlugins_ScoutnetGetGroup();
		$members = $decoded['Group']['stats']['active']['breakdown'];
		$konnamn = array("annat","killar","tjejer");
		$kontotantal = array(0,0,0);
		$medlemmar = array();

		foreach ($members as $key => $peralder) {
			$konantal = array(0,0,0);
			$alder = $key;
			foreach ($peralder as $kon => $antal) {
				$konantal[$kon]+=$antal;
				$kontotantal[$kon]+=$antal;
			}
			$kommatecken = 0;
			$medlemmar[$alder] = $konantal;
		}

		$under18 = 0;
		foreach ($medlemmar as $alder => $konsarray) {
			if ($alder > 18)
				break;
			$under18 += array_sum($konsarray);
		}
		echo "$under18 &auml;r 18 eller yngre<br >";
		echo "<div class=\"container\" style=\"width: ".$under18."px\">\n";
//		echo '<table style="border-collapse: collapse;margin:0;padding:0;">';
		echo "<table style=\"line-height:14px;\">";
		foreach ($medlemmar as $alder => $konsarray) {
			if ($alder > 18)
				break;
			$bredd = array_sum($konsarray);
			$bredd *= 3;
			$fodd = date('Y')-$alder;
			echo "<tr><td>$fodd</td><td style=\"padding: 0px 8px 0px 5px;\">".array_sum($konsarray)."</td><td><p class=\"alder$alder\" style=\"width: ".$bredd."px; float:left; margin:0;\">&nbsp;</p></td></tr>";
//		<div class=\"graf\" style=\"width: ".$under18."px;\"><p style=\"width: 34px; float:left;\">$fodd</p><p class=\"alder$alder\" style=\"width: ".$bredd."px; float:left;\">&nbsp;</p></div>\n";
		}
		echo "</table>";
		break;
	case 2:
		echo "<p style='font-size:2em;text-align:center;'>". ScouternaPlugins_ScoutnetGetMemberscount() ." medlemmar</p><p style='font-size:1.75em;text-align:center;'>varav ". count(ScouternaPlugins_ScoutnetGetLeaders()) ." ledare,</p><p style='font-size:1.55em;text-align:center;'>".count(ScouternaPlugins_ScoutnetGetStaff())." funktion&auml;rer</p><p style='font-size:1.25em;text-align:center;'>och ".count(ScouternaPlugins_ScoutnetGetUnitmembers("Stödjande Medlemmar", true))." st&ouml;djande.";
		break;
	case 1:
		echo "<p style='font-size:2em;text-align:center;'>". ScouternaPlugins_ScoutnetGetMemberscount() ." medlemmar</p><p style='font-size:1.75em;text-align:center;'>varav ". count(ScouternaPlugins_ScoutnetGetLeaders()) ." ledare.</p>";
		break;
	case 0:
	default:
		echo "<p style='font-size:2em;text-align:center;'>". ScouternaPlugins_ScoutnetGetMemberscount() ." medlemmar</p>";
		break;
	}
	echo $after_widget;
}
/**
 * Sanitize widget form values as they are saved.
 *
 * @see WP_Widget::update()
 *
 * @param array $new_instance Values just sent to be saved.
 * @param array $old_instance Previously saved values from database.
 *
 * @return array Updated safe values to be saved.
 */
public function update($new_instance,$old_instance) {
	$instance = $old_instance;
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['r_settings'] = strip_tags($new_instance['r_settings']);
	return $instance;
}
  
/**
  * Back-end widget form.
  *
  * @see WP_Widget::form()
  *
  * @param array $instance Previously saved values from database.
  */

public function form($instance) {
	if (isset($instance['title']))
		$title = $instance[ 'title' ];
	else
		$title = __( 'New title', 'text_domain' );
	$r_settings = esc_attr( $instance['r_settings']);
?>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titel:'); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?=$title?>" />
<input id="<?php echo esc_attr( $this->get_field_id( 'r_settings0' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="0" <?php checked( '0', $r_settings ); ?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings0' ) ); ?>"><?php _e( 'Simpel (standard)', 'scouternaplugins' ); ?></label>
<br /><input id="<?php echo esc_attr( $this->get_field_id( 'r_settings1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="1" <?php checked( '1', $r_settings ); ?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings1' ) ); ?>"><?php _e( 'Ut&ouml;kad (ledare)', 'scouternaplugins' ); ?></label>
<br /><input id="<?php echo esc_attr( $this->get_field_id( 'r_settings2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="2" <?php checked( '2', $r_settings ); ?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings2' ) ); ?>"><?php _e( 'Ut&ouml;kad (allt)', 'scouternaplugins' ); ?></label>
<br /><input id="<?php echo esc_attr( $this->get_field_id( 'r_settings3' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="3" <?php checked( '3', $r_settings ); ?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings3' ) ); ?>"><?php _e( 'Ut&ouml;kad (grafik)', 'scouternaplugins' ); ?></label>
<?php 
}
}
 
/* Register the widget */
add_action( 'widgets_init', function(){
 register_widget( 'ScouternaPlugins_Scoutnet_AntMedlemmar_Widget' );
});
