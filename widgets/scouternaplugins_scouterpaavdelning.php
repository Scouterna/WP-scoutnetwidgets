<?php
/*
 * Widget som visar scouter på en viss avdelning
 */

 
class ScouternaPlugins_Scoutnet_ScouterpaAvdelning_Widget extends WP_Widget {
 
public function __construct() {
	parent::__construct('scouternaplugins_scouterpaavdelning_widget',__( 'Visar scouter p&aring; en avdelning', 'scouternaplugins' ),
		array('classname' => 'scouternaplugins_scouterpaavdelning_widget','description' => __( 'En widget som visar antalet scouter p&aring; en avdelning.', 'scouternaplugins'))
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

public function widget($args,$instance) {
	extract( $args );
	echo $before_widget;
	$show_names = esc_attr($instance['show_names']);
	$show_patrols = esc_attr($instance['show_patrols']);
	$show_option = esc_attr($instance['show_option']);
	$url = get_page_uri();
	
	if (strpos($url, '/') !== false)
		$avdelningsnamn = substr($url, strrpos($url, '/') + 1);
	else
		$avdelningsnamn = $url;
	
	$decoded = ScouternaPlugins_ScoutnetGetMemberlist();
	$members = $decoded['data'];
	$avdelning = array();

	foreach ($members as $key => $medlem) {
		if ($avdelningsnamn == ScouternaPlugins_ScoutnetFixThatString($medlem['unit']['value'])) {
			if (empty($medlem['unit_role']['value'])) {
				if ($show_patrols == "yes") {
					$avdelning[] = ["name" => $medlem['first_name']['value']." ".mb_substr($medlem['last_name']['value'],0,1),"patrol"=>$medlem['patrol']['value']];
				}
				else
				$avdelning[] = $medlem['first_name']['value']." ".mb_substr($medlem['last_name']['value'],0,1);
			}
		}
	}
	if (empty($avdelning))
		echo "Hej!<br>Denna widgets &auml;r just nu p&aring; en sida som inte heter samma som en av avdelningarna i Scoutnet. Denna sidas namn &auml;r <i>$avdelningsnamn</i>.<br>Denna widget vet inte vilka sidor som &auml;r en avdelning eller inte, s&aring; man beh&ouml;ver ha en till plugin som kan best&auml;mma vilka sidor denna widget ska visas p&aring;.";
	else {
		if (is_user_logged_in() && $show_names== "yes") {
			echo "Dessa ".count($avdelning)." scouter finns p&aring; avdelningen:<br>";
			if ($show_option != 1)
				sort($avdelning);
			if ($show_option == 2 && $show_names == "yes" && $show_patrols == "yes") {
				foreach ($avdelning as $key => $row)
					$patrol[$key]  = $row['patrol'];
				array_multisort($patrol, SORT_ASC, $avdelning);
				$lastpatrol = $avdelning[0]['patrol'];
				echo "<table>\r";
				foreach ($avdelning as $value) {
					if ($lastpatrol != $value['patrol'])
						echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
					echo "<tr><td>".$value['name']."</td><td style=\"padding-left: 10px;\">".$value['patrol']."</td></tr>\r";
					$lastpatrol = $value['patrol'];
				}
				echo "</table>\r";
			} else {
				if ($show_patrols == "yes") {
					echo "<table>\r";
					foreach ($avdelning as $value)
						echo "<tr><td>".$value['name']."</td><td style=\"padding-left: 10px;\">".$value['patrol']."</td></tr>\r";
					echo "</table>\r";
				}
				else
					foreach ($avdelning as $value)
						echo $value."<br>\r";
			}
		}
		else
			echo "Det finns ".count($avdelning)." scouter p&aring; avdelningen";
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
public function update($new_instance,$old_instance ) {
	$instance = $old_instance;
	$instance['show_names'] = strip_tags($new_instance['show_names']);
	$instance['show_patrols'] = strip_tags($new_instance['show_patrols']);
	$instance['show_option'] = strip_tags($new_instance['show_option']);
	return $instance;
}
  
/**
  * Back-end widget form.
  *
  * @see WP_Widget::form()
  *
  * @param array $instance Previously saved values from database.
  */
public function form( $instance ) {
	$show_names = esc_attr($instance['show_names']);
	$show_patrols = esc_attr($instance['show_patrols']);
	$show_option = esc_attr($instance['show_option']);
?>

<u>Visa namnen p&aring; scouterna p&aring; avdelningen f&ouml;r de som &auml;r inloggade:</u><br>
<input id="<?=esc_attr($this->get_field_id('show_names_yes'))?>" name="<?=esc_attr( $this->get_field_name('show_names'))?>" type="radio" value="yes" <?=checked('yes', $show_names)?>>
<label for="<?=esc_attr($this->get_field_id('show_names_yes'))?>"><?=_e('Ja','scouternaplugins')?></label>
<input id="<?=esc_attr($this->get_field_id('show_names_no'))?>" name="<?=esc_attr($this->get_field_name('show_names'))?>" type="radio" value="no" <?=checked('no', $show_names)?>>
<label for="<?=esc_attr($this->get_field_id('show_names_no'))?>"><?=_e('Nej','scouternaplugins')?></label>
<br>
<u>Visa patruller (endast om ovan &auml;r satt till "ja"):</u><br>
<input id="<?=esc_attr($this->get_field_id('show_patrols_yes'))?>" name="<?=esc_attr( $this->get_field_name('show_patrols'))?>" type="radio" value="yes" <?=checked('yes', $show_patrols)?>>
<label for="<?=esc_attr($this->get_field_id('show_patrols_yes'))?>"><?=_e('Ja','scouternaplugins')?></label>
<input id="<?=esc_attr($this->get_field_id('show_patrols_no'))?>" name="<?=esc_attr($this->get_field_name('show_patrols'))?>" type="radio" value="no" <?=checked('no', $show_patrols)?>>
<label for="<?=esc_attr($this->get_field_id('show_patrols_no'))?>"><?=_e('Nej','scouternaplugins')?></label>
<br>
<u>Sortera namnen efter:</u><br>
<input id="<?=esc_attr($this->get_field_id('show_option0'))?>" name="<?=esc_attr( $this->get_field_name('show_option'))?>" type="radio" value="0" <?=checked('0', $show_option)?>>
<label for="<?=esc_attr($this->get_field_id('show_option0'))?>"><?=_e('F&ouml;rnamn (standard)','scouternaplugins')?></label>
<br />
<input id="<?=esc_attr($this->get_field_id('show_option1'))?>" name="<?=esc_attr($this->get_field_name('show_option'))?>" type="radio" value="1" <?=checked('1', $show_option)?>>
<label for="<?=esc_attr( $this->get_field_id('show_option1'))?>"><?=_e('Efternamn', 'scouternaplugins')?></label>
<br /><input id="<?=esc_attr($this->get_field_id('show_option3'))?>" name="<?=esc_attr( $this->get_field_name('show_option'))?>" type="radio" value="2" <?=checked('2', $show_option);?>>
<label for="<?=esc_attr( $this->get_field_id('show_option3')); ?>"><?=_e('Gruppera i patruller (endast om b&aring;da valen &auml;r satta till "Ja")', 'scouternaplugins')?></label>
<?php 
}
}


/* Register the widget */
add_action( 'widgets_init', function(){
 register_widget( 'ScouternaPlugins_Scoutnet_ScouterpaAvdelning_Widget' );
});
