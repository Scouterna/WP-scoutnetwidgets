<?php
/*
 * Widget som visar antalet medlemmar
 */

 
class ScouternaPlugins_Scoutnet_Fodelsedag_Widget extends WP_Widget {
 
public function __construct() {
	parent::__construct('scouternaplugins_fodelsedag_widget',__( 'Visar medlemmar som fyller &aring;r', 'scouternaplugins' ),
		array('classname'   => 'scouternaplugins_fodelsedag_widget','description' => __( 'En widget som listar medlemmar i k&aring;ren som fyller &aring;r idag.', 'scouternaplugins' ))
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
	if (!empty(ScouternaPlugins_ScoutnetGetBirthday(0))) {

	wp_enqueue_style( 'ScouternaPlugins_Scoutnet_Fodelsedag_Widget-style', plugins_url('css/birthday.css', __FILE__) );
	$r_settings = esc_attr($instance['r_settings']);
	if (empty($r_settings))
		$r_settings=0;

	extract( $args );
	echo $before_widget;

	$party=true;
	if (empty(ScouternaPlugins_ScoutnetGetBirthday($r_settings)))
		$party=false;
	else
		$birthdaylist = ScouternaPlugins_ScoutnetGetBirthday($r_settings);

	if ($party) {
		if ($r_settings == 0)
			echo "Idag fyller ".count($birthdaylist)." scouter i k&aring;ren &aring;r.";
		else {
			if (count($birthdaylist) > 1) {
				$first=true;
				foreach ($birthdaylist as $value) {
					if ($first) {
						echo $value;
						$first=false;
					}
					else
						echo "<br>".$value;
				}
			}
			else
				echo $value;
		}
	} else {
		echo "Inga som fyller &aring;r idag.";
	}
	echo $after_widget;
	}
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
	$instance['customtext'] = ( ! empty( $new_instance['customtext'] ) ) ? strip_tags( $new_instance['customtext'] ) : '';
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
	if (isset($instance['customtext']))
		$customtext = $instance[ 'customtext' ];
	else
		$customtext = __( 'Idag gratulerar vi:', 'scouternaplugins' );
	$r_settings = esc_attr( $instance['r_settings']);
?>
<label for="<?php echo $this->get_field_id('customtext'); ?>"><?php _e('Vafri text:'); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id('customtext'); ?>" name="<?php echo $this->get_field_name('customtext'); ?>" type="text" value="<?=$customtext?>" />

<u>Visa avdelningen p&aring; de som fyller &aring;r:</u><br>
<input id="<?=esc_attr($this->get_field_id('show_names_yes'))?>" name="<?=esc_attr( $this->get_field_name('show_names'))?>" type="radio" value="3" <?=checked('3', $show_names)?>>
<label for="<?=esc_attr($this->get_field_id('show_names_yes'))?>"><?=_e('Ja','scouternaplugins')?></label>
<input id="<?=esc_attr($this->get_field_id('show_names_no'))?>" name="<?=esc_attr($this->get_field_name('show_names'))?>" type="radio" value="0" <?=checked('0', $show_names)?><?=(empty($show_names) ? "checked" : "");?>>
<label for="<?=esc_attr($this->get_field_id('show_names_no'))?>"><?=_e('Nej (standard)','scouternaplugins')?></label>
<br>
<u>Visa antal/namn:</u><br>
<input id="<?php echo esc_attr( $this->get_field_id( 'r_settings0' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="0" <?php checked( '0', $r_settings ) ?> <?=(empty($r_settings) ? "checked" : "");?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings0' ) ); ?>"><?php _e( 'Visar bara antal (standard)', 'scouternaplugins' ); ?></label>
<br /><input id="<?php echo esc_attr( $this->get_field_id( 'r_settings1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="1" <?php checked( '1', $r_settings ); ?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings1' ) ); ?>"><?php _e( 'Visar f&ouml;rnamn', 'scouternaplugins' ); ?></label>
<br /><input id="<?php echo esc_attr( $this->get_field_id( 'r_settings2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="2" <?php checked( '2', $r_settings ); ?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings2' ) ); ?>"><?php _e( 'Visar f&ouml;rnamn + f&ouml;rsta bokstaven i efternamn', 'scouternaplugins' ); ?></label>
<?php 
}
}
 
/* Register the widget */
add_action( 'widgets_init', function(){
 register_widget( 'ScouternaPlugins_Scoutnet_Fodelsedag_Widget' );
});
