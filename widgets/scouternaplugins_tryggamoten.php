<?php
/**
 * Widget som visar trygga möten
 */

 
class ScouternaPlugins_Scoutnet_TryggaMoten_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct('scouternaplugins_tryggamoten_widget',__('Visar hur m&aring;nga som g&aring;tt trygga m&ouml;ten','scouternaplugins'),
			array('classname' => 'scouternaplugins_tryggamoten_widget','description' => __('Visar hur m&aring;nga som g&aring;tt trygga m&omul;ten','scouternaplugins'))
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
		extract( $args );
		$listid = esc_attr($instance['listid']);
		
		echo $before_widget;
		if(!empty(ScouternaPlugins_ScoutnetGetCustomlist($listid)['data'])) {
			$gatttm = count(ScouternaPlugins_ScoutnetGetCustomlist($listid)['data']);
			$avantal = count(ScouternaPlugins_ScoutnetGetStaff())+count(ScouternaPlugins_ScoutnetGetLeaders())+count(ScouternaPlugins_ScoutnetGetAllAssleders());
			$procent = round($gatttm/$avantal*100);
			echo "<div style=\"text-align:center;\"><span style=\"font-size: 2em;\">$gatttm av $avantal </span><span style=\"font-size: 1.25em;\">($procent%)</span><br><span style=\"font-size: 2em;\"><br>ledare, assistenter och k&aring;rfunktion&auml;rer</span><br>har g&aring;tt trygga m&ouml;ten</span></div>";
		}
		else
			echo "Hej!<br>H&auml;r skulle en text legat p&aring; antalet som g&aring;tt Trygga M&ouml;ten.<br>Det saknas dock en inst&auml;llning f&ouml;r denna widget p&aring; widget sidan.<br>Har du r&auml;tt beh&ouml;righet s&aring; kan du &auml;ndra detta <a href=\"/wp-admin/widgets.php\">h&auml;r</a>.";
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
		$instance['listid'] = strip_tags($new_instance['listid']);
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
		$listid = esc_attr($instance['listid']);
?>
	<label for="<?=$this->get_field_id('listid')?>"><?php _e('Nummer p&aring; mejllistan'); ?></label>
	<input class="widefat" id="<?=$this->get_field_id('listid')?>" name="<?=$this->get_field_name('listid')?>" type="text" value="<?=$listid?>" /><?=scoutnet_colorbradgard(scoutnet_get_customlist($listid)['data'])?>
	<br><i>Du ska fylla i allt efter "?list_id=" i rutan ovan. Du hittar detta nummer p&aring; samma sida som du hittar API-nycklarna i Scoutnet.</i>
	<br>Syns det ett r&ouml;tt # efter f&auml;ltet efter du sparat s&aring; har scoutnet retunerat en tom lista.

<?php
	}
}

/* Register the widget */
add_action( 'widgets_init', function() {
	register_widget('ScouternaPlugins_Scoutnet_TryggaMoten_Widget');
});
