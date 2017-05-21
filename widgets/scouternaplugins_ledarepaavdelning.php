<?php
/*
 * Widget som visar ledare på en viss avdelning
 */

 
class ScouternaPlugins_Scoutnet_LedarepaAvdelning_Widget extends WP_Widget {
 
    public function __construct() {
     
        parent::__construct(
            'scouternaplugins_ledarepaavdelning_widget',
            __( 'Visar ledare p&aring; en avdelning', 'scouternaplugins' ),
            array(
                'classname'   => 'scouternaplugins_ledarepaavdelning_widget',
                'description' => __( 'En widget som visar ledare p&aring; en avdelning.', 'scouternaplugins' )
                )
        );
       
        load_plugin_textdomain( 'scouternaplugins', false, basename( dirname( __FILE__ ) ) . '/languages' );
       
    }
 
    /**  
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {    
         
        extract( $args );
        $r_settings = esc_attr($instance['r_settings']);

        echo $before_widget;

$url = get_page_uri();
    if (strpos($url, '/') !== false)
        $avdelningsnamn = substr($url, strrpos($url, '/') + 1);
    else
	$avdelningsnamn = $url;
	
$decoded = ScouternaPlugins_ScoutnetGetMemberlist();
$members = $decoded['data'];

$medlemmar = array();
$avdelning = array();


foreach ($members as $key => $value) {
	$medlemmar[$key] = $value;

    foreach ($value as $key2 => $medlem) {
	    if ($key2 == "unit") {
        if ($avdelningsnamn == ScouternaPlugins_ScoutnetFixThatString($medlem['value'])) {
		if (!empty($value['unit_role']['value'])) {
			switch($r_settings) {
				case 2:
					$ledare[] = $value['first_name']['value']." ".$value['last_name']['value']."<br><i>".$value['unit_role']['value']."</i><br>tel: ".substr($value['contact_mobile_phone']['value'],0,5)."XXXXXX<br>E-post: <a href=\"".$value['email']['value']."\">".$value['email']['value']."</a>";
					break;
				case 1:
					$ledare[] = $value['first_name']['value']." ".$value['last_name']['value']."<br><i>".$value['unit_role']['value']."</i><br>tel: ".substr($value['contact_mobile_phone']['value'],0,5)."XXXXXX";
					break;
				case 0:
				default:
					$ledare[] = $value['first_name']['value']." ".$value['last_name']['value']."<br><i>".$value['unit_role']['value']."</i>";
					break;
			}
		}
			
	}
        }
    }
}

echo "Ledare p&aring; avdelningen:<br>";
if (empty($ledare))
	echo "Hej!<br>Denna widgets &auml;r just nu p&aring; en sida som inte heter samma som en av avdelningarna i Scoutnet. Denna sidas namn &auml;r <i>$avdelningsnamn</i>.<br>Denna widget vet inte vilka sidor som &auml;r en avdelning eller inte, s&aring; man beh&ouml;ver ha en till plugin som kan best&auml;mma vilka sidor denna widget ska visas p&aring;.";
else
	foreach ($ledare as $item)
		echo "\n<br>$item";
echo "<br><br><i>Scoutnet listar bara de ledare som &auml;r har denna avdelning som huvudavdelning. Exempelivs s&aring; har assistenter utmanarlaget som huvudavdelning, s&aring; de listas inte.</i>";

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
    public function update( $new_instance, $old_instance ) {        

        $instance = $old_instance;
        
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
    public function form( $instance ) {    
	    $r_settings = esc_attr( $instance['r_settings']);


	    $options = array('simple','full');
//	    foreach ( $options as $option ) {
?>


<input id="<?php echo esc_attr( $this->get_field_id( 'r_settings0' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="0" <?php checked( '0', $r_settings ); ?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings0' ) ); ?>"><?php _e( 'Simpel (standard)', 'scouternaplugins' ); ?></label>
<br><input id="<?php echo esc_attr( $this->get_field_id( 'r_settings1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="1" <?php checked( '1', $r_settings ); ?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings1' ) ); ?>"><?php _e( 'Ut&ouml;kad (telnr)', 'scouternaplugins' ); ?></label>
<br><input id="<?php echo esc_attr( $this->get_field_id( 'r_settings3' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'r_settings' ) ); ?>" type="radio" value="2" <?php checked( '2', $r_settings ); ?> />
<label for="<?php echo esc_attr( $this->get_field_id( 'r_settings3' ) ); ?>"><?php _e( 'Ut&ouml;kad (telnr+mejl)', 'scouternaplugins' ); ?></label>
    <?php 
    }
}

/* Register the widget */
add_action( 'widgets_init', function(){
     register_widget( 'ScouternaPlugins_Scoutnet_LedarepaAvdelning_Widget' );
});
