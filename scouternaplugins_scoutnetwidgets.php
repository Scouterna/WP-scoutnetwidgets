<?php
/*
Plugin Name: Scoutnet koppling
Plugin URI: http://eservice.scout.se/
Description: Plugin f&ouml;r att ansluta Wordpress till Scoutnets API och presentera data via widgets.
Version: 0.5
Author: Joel "PazZze" Martinsson
Author URI: https://code.pazzze.se
Text Domain: scouternaplugins
License: GNU AGPLv3
*/

define( 'SCOUTERNAPLUGINS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once( SCOUTERNAPLUGINS__PLUGIN_DIR . '/inc/scouternaplugins_scoutnetwidgets_functions.php' );
foreach (glob(SCOUTERNAPLUGINS__PLUGIN_DIR . "widgets/*.php") as $filename) {
    include $filename;
}


//temp variabel f�r att �ndra server =)
static $scoutnetapiurl = "www.scoutnet.se";

add_action( 'admin_menu', 'ScouternaPlugins_Scoutnet_add_admin_menu' );
add_action( 'admin_init', 'ScouternaPlugins_Scoutnet_settings_init' );


function ScouternaPlugins_Scoutnet_add_admin_menu() { 
	add_menu_page( 'Scoutnet koppling', 'Scoutnet koppling', 'manage_options', 'scouternaplugins_scoutnetwidgets', 'ScouternaPlugins_Scoutnet_options_page', plugins_url("scouternaplugins_scoutnetwidgets/img/logga.png"), 99 );
}


function ScouternaPlugins_Scoutnet_settings_init() { 

	register_setting( 'pluginPage', 'ScouternaPlugins_Scoutnet_settings' );

	add_settings_section(
		'ScouternaPlugins_Scoutnet_pluginPage_section', 
		__( 'Koppla Wordpress till Scoutnet', 'scouternaplugins' ), 
		'ScouternaPlugins_Scoutnet_settings_section_callback', 
		'pluginPage'
	);
	
	add_settings_field( 
		'ScouternaPlugins_Scoutnet_karid', 
		__( 'K&aring;r ID', 'scouternaplugins' ), 
		'ScouternaPlugins_Scoutnet_karid_render', 
		'pluginPage', 
		'ScouternaPlugins_Scoutnet_pluginPage_section' 
	);

	add_settings_field( 
		'ScouternaPlugins_Scoutnet_apinyckel_group', 
		__( 'API-nyckel <br /><i>View group information</i>', 'scouternaplugins' ), 
		'ScouternaPlugins_Scoutnet_apinyckel_group_render', 
		'pluginPage', 
		'ScouternaPlugins_Scoutnet_pluginPage_section' 
	);

	add_settings_field( 
		'ScouternaPlugins_Scoutnet_apinyckel_waitinglist', 
		__( 'API-nyckel <br /><i>Register a group member on a waitinglist</i>', 'scouternaplugins' ), 
		'ScouternaPlugins_Scoutnet_apinyckel_waitinglist_render', 
		'pluginPage', 
		'ScouternaPlugins_Scoutnet_pluginPage_section' 
	);

	add_settings_field( 
		'ScouternaPlugins_Scoutnet_apinyckel_members', 
		__( 'API-nyckel <br /><i>Get a detailed list of all members </i>', 'scouternaplugins' ), 
		'ScouternaPlugins_Scoutnet_apinyckel_members_render', 
		'pluginPage', 
		'ScouternaPlugins_Scoutnet_pluginPage_section' 
	);

	add_settings_field( 
		'ScouternaPlugins_Scoutnet_apinyckel_mail', 
		__( 'API-nyckel <br /><i>Get a list of members, based on mailing lists</i>', 'scouternaplugins' ), 
		'ScouternaPlugins_Scoutnet_apinyckel_mail_render', 
		'pluginPage', 
		'ScouternaPlugins_Scoutnet_pluginPage_section' 
	);


}
$options = get_option('ScouternaPlugins_Scoutnet_settings');

function ScouternaPlugins_Scoutnet_karid_render() { 
	global $options;
	?>
	<input type='number' size='3' name='ScouternaPlugins_Scoutnet_settings[ScouternaPlugins_Scoutnet_karid]' value='<?=$options['ScouternaPlugins_Scoutnet_karid']?>'>
<?php
	if (!empty(scoutnet_get_groupname()))
		echo "<br />Du har anslutit Wordpress till ".scoutnet_get_groupname();
}
function ScouternaPlugins_Scoutnet_apinyckel_group_render() { 
	global $options;
	?>
	<input type='text' size='47' name='ScouternaPlugins_Scoutnet_settings[ScouternaPlugins_Scoutnet_apinyckel_group]' value='<?=$options['ScouternaPlugins_Scoutnet_apinyckel_group']?>'>
<?php
	scoutnet_colorbradgard(scoutnet_get_group());
}
function ScouternaPlugins_Scoutnet_apinyckel_waitinglist_render() { 
	global $options;
	?>
	<input type='text' size='47' name='ScouternaPlugins_Scoutnet_settings[ScouternaPlugins_Scoutnet_apinyckel_waitinglist]' value='<?=$options['ScouternaPlugins_Scoutnet_apinyckel_waitinglist']?>'>
<?php
	scoutnet_colorbradgard(scoutnet_check_registermemeber(),"bool");
}
function ScouternaPlugins_Scoutnet_apinyckel_members_render() { 
	global $options;
	?>
	<input type='text' size='47' name='ScouternaPlugins_Scoutnet_settings[ScouternaPlugins_Scoutnet_apinyckel_members]' value='<?=$options['ScouternaPlugins_Scoutnet_apinyckel_members']?>'>
<?php
	scoutnet_colorbradgard(scoutnet_get_memberlist());
}
function ScouternaPlugins_Scoutnet_apinyckel_mail_render() { 
	global $options;
	?>
	<input type='text' size='47' name='ScouternaPlugins_Scoutnet_settings[ScouternaPlugins_Scoutnet_apinyckel_mail]' value='<?=$options['ScouternaPlugins_Scoutnet_apinyckel_mail']?>'>
<?php
	scoutnet_colorbradgard(scoutnet_get_customlist());
}
function ScouternaPlugins_Scoutnet_settings_section_callback() { 
	echo __( 'H&auml;r kan du koppla Wordpress till Scoutnet.<br/>Du beh&ouml;ver ha r&auml;tt beh&ouml;righet i Scoutnet f&ouml;r att se sidan d&auml;r uppgifterna st&aring;r. Alternativt f&aring; uppgifterna fr&aring;n en som har.<br/><br/>Du hittar uppgifterna i Scoutnet under "Din k&aring;r" > Webbkoppling.<br/>&Auml;r inte API-systemet p&aring;slaget m&aring;ste du g&ouml;ra detta f&ouml;rst genom knappen h&ouml;gst upp till h&ouml;ger.<br/>K&aring;r ID hittar du genom att expandera ett av f&auml;lten.<br/><br/>Du beh&ouml;ver skriva in flertalet API-nycklar i rutorna nedan. Se till att du skriver r&auml;tt nyckel i r&auml;tt ruta! Se &auml;ven till att det inte &auml;r n&aring;gra blanktecken!<br /><br />Br&auml;dg&aring;rdstecknet blir gr&ouml;nt n&auml;r anslutningen fungerar, uppdateras efter du sparat.', 'scouternaplugins' );
}


function ScouternaPlugins_Scoutnet_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}

?>