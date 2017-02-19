<?php
/**
 * All shared functions for this plugin
 *
 * @author Joel "PazZze" Martinsson <me@pazzze.se>
 * @link eservice.scout.se
 */

//static $scoutnetapiurl = "s1.test.custard.no";

$options = get_option( 'ScouternaPlugins_Scoutnet_settings' );
$karid = $options['ScouternaPlugins_Scoutnet_karid'];

/**
 * scoutnet_get_group()
 *
 * Retrieves a list of gropinformation
 *
 * @return array of goupinformation
 */
function scoutnet_get_group() {
	// /api/organisation/group
	global $scoutnetapiurl;
        global $options;
        global $karid;
	$apinyckel = $options['ScouternaPlugins_Scoutnet_apinyckel_group'];
	$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/organisation/group");
	if($result !== FALSE)
		return json_decode($result, true);
}
/**
 * scoutnet_get_memberlist()
 *
 * Retrieves a list all the members
 *
 * @return array of all members
 */
function scoutnet_get_memberlist() {
	// /api/group/memberlist
	global $scoutnetapiurl;
	global $options;
	global $karid;
	$apinyckel = $options['ScouternaPlugins_Scoutnet_apinyckel_members'];
	$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/group/memberlist");
//ho "<hr>";
//int_r($result);
	if($result !== FALSE)
		return json_decode($result, true);
}
/**
 * scoutnet_get_customlist()
 *
 * Retrieves a custom created list
 *
 * @param int $listid id of the custom list.
 * @return array of all members
 */
function scoutnet_get_customlist($listid = false) {
	// /api/group/customlists 
	global $scoutnetapiurl;
        global $options;
        global $karid;
	$apinyckel = $options['ScouternaPlugins_Scoutnet_apinyckel_mail'];
	if ($listid == false)
		$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/group/customlists");
	else
		$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/group/customlists?list_id=$listid");
	if($result !== FALSE)
		return json_decode($result, true);
}
/**
 * scoutnet_check_registermemeber()
 *
 * A function to check if the API-key for register members is working
 *
 * @return boolean true if the site returns a 400 https status code
 */
function scoutnet_check_registermemeber() {
	global $scoutnetapiurl;
        global $options;
        global $karid;
	$apinyckel = $options['ScouternaPlugins_Scoutnet_apinyckel_waitinglist'];
	$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/organisation/register/member");
	preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$http_response_header[0], $out );
	if($out[1] == 400)
		return true;
}
/**
 * scoutnet_get_groupname()
 *
 * Retrieves the name of the group.
 *
 * @return string name of the group
 */
function scoutnet_get_groupname() {
	$decoded = scoutnet_get_group();
	return $decoded['Group']['name'];
}
/**
 * scoutnet_get_memberscount()
 *
 * Retrieves the total amount of members.
 *
 * @return int all the members of the group
 */
function scoutnet_get_memberscount() {
	$decoded = scoutnet_get_group();
	return $decoded['Group']['membercount'];
}
/**
 * scoutnet_get_memberscount()
 *
 * 
 *
 * @return int persons waiting to become a member
 */
function scoutnet_get_waitingcount() {
	$decoded = scoutnet_get_group();
	return $decoded['Group']['waitingcount'];
}

/**
 * scoutnet_get_allleaders()
 *
 * 
 *
 * @return array with people that matches
 */
function scoutnet_get_allleaders() {
	$decoded = scoutnet_get_memberlist();
	$members = $decoded['data'];
	$avdelning = array();
	foreach ($members as $key => $value) {
		if (!empty($value['unit_role']['raw_value'])) {
			if (!is_numeric($value['unit_role']['raw_value'])) {
				preg_match_all('/\d+/', $value['unit_role']['raw_value'], $matches);
				if ($matches[0][0] == "1" || $matches[0][0] == "1" || $matches[0][0] == "3" || $matches[0][1] == "1" || $matches[0][1] == "1" || $matches[0][1] == "3")
					$avdelning[] = $value['member_no']['value'];
			}
			if ($value['unit_role']['raw_value'] == 1 || $value['unit_role']['raw_value'] == 2 ||
 $value['unit_role']['raw_value'] == 3)
 				$avdelning[] = $value['member_no']['value'];
		}
	}
	return $avdelning;
}




function scoutnet_get_troopmembers($wantedtroop,$exlude) {
	$decoded = scoutnet_get_memberlist();
	$members = $decoded['data'];
	$avdelning = array();
	foreach ($members as $key => $value) {
		if ($exlude) {
			if (scouternaplugins_fixthatstring($value['unit']['value']) === scouternaplugins_fixthatstring($wantedtroop) && !isset ($value['group_role']))
				$avdelning[] = $value['first_name']['value'];
		} else {
			if (scouternaplugins_fixthatstring($value['unit']['value']) === scouternaplugins_fixthatstring($wantedtroop))
				$avdelning[] = $value['first_name']['value'];
		}
	}
	return $avdelning;
}

function scoutnet_get_staff() {
	$decoded = scoutnet_get_memberlist();
	$members = $decoded['data'];
	$avdelning = array();
	foreach ($members as $key => $value) {
		if (empty($value['unit_role']['raw_value']) && !empty($value['group_role']['raw_value']))
			$avdelning[] = $value['member_no']['value'];
	}
	return $avdelning;
}


function scouternaplugins_fixthatstring($string) {
	return strtolower(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($string, ENT_COMPAT, 'UTF-8')));
}


function scoutnet_get_allass() {
	$decoded = scoutnet_get_memberlist();
	$members = $decoded['data'];
	$avdelning = array();
	foreach ($members as $key => $value) {
		if (!empty($value['unit_role']['raw_value'])) {
			if (!is_numeric($value['unit_role']['raw_value'])) {
				preg_match_all('/\d+/', $value['unit_role']['raw_value'], $matches);
				if ($matches[0][0] == "5" || $matches[0][1] == "5")
					$avdelning[] = $value['member_no']['value'];
				}
				if ($value['unit_role']['raw_value'] == 5)
					$avdelning[] = $value['member_no']['value'];
			}
	}
	return $avdelning;
}

function scoutnet_colorbradgard($thingtocheck, $mode="") {
	$color = "#FF0000";
	if (empty($mode))
		if(!empty($thingtocheck))
			$color = "#00FF00";
	if ($mode == "bool")
		if (scoutnet_check_registermemeber() == true)
			$color = "#00FF00";
	echo "<span style=\"color: $color\">#</span>";
}
?>
