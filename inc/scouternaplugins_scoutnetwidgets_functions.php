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
 * ScouternaPlugins_ScoutnetGetGroup()
 *
 * Retrieves a list of gropinformation
 *
 * @return array of goupinformation
 */
function ScouternaPlugins_ScoutnetGetGroup() {
	// /api/organisation/group
	global $scoutnetapiurl;
        global $options;
        global $karid;
	$apinyckel = $options['ScouternaPlugins_Scoutnet_apinyckel_group'];
	$result = ScouternaPlugins_ScoutetCacheIt("group","https://$karid:$apinyckel@$scoutnetapiurl/api/organisation/group");
//	$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/organisation/group");
	if($result !== FALSE)
		return json_decode($result, true);
}
/**
 * ScouternaPlugins_ScoutnetGetMemberlist()
 *
 * Retrieves a list all the members
 *
 * @return array of all members
 */
function ScouternaPlugins_ScoutnetGetMemberlist() {
	// /api/group/memberlist
	global $scoutnetapiurl;
	global $options;
	global $karid;
	$apinyckel = $options['ScouternaPlugins_Scoutnet_apinyckel_members'];

	$result = ScouternaPlugins_ScoutetCacheIt("memberlist","https://$karid:$apinyckel@$scoutnetapiurl/api/group/memberlist");
//	$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/group/memberlist");
	
	if($result !== FALSE)
		return json_decode($result, true);
}
/**
 * ScouternaPlugins_ScoutnetGetCustomlist()
 *
 * Retrieves a custom created list
 *
 * @param string $listid id of the custom list.
 * @return array of all members
 */
function ScouternaPlugins_ScoutnetGetCustomlist($listid = false) {
	// /api/group/customlists 
	global $scoutnetapiurl;
        global $options;
        global $karid;
	$apinyckel = $options['ScouternaPlugins_Scoutnet_apinyckel_mail'];
	if ($listid == false) {
		$result = ScouternaPlugins_ScoutetCacheIt("customlists","https://$karid:$apinyckel@$scoutnetapiurl/api/group/customlists");
//		$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/group/customlists");
	}
	else {
		$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/group/customlists?list_id=$listid");
	}
	if($result !== FALSE)
		return json_decode($result, true);
}
/**
 * ScouternaPlugins_ScoutnetCheckRegistermemeber()
 *
 * A function to check if the API-key for register members is working
 *
 * @return boolean true if the site returns a 400 https status code
 */
function ScouternaPlugins_ScoutnetCheckRegistermemeber() {
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
 * ScouternaPlugins_ScoutetCacheIt()
 *
 * Adds chaching to the lists
 *
 * @param string $name name of cache
 * @param string $data url of data to cache
 * @param string $group groups all cache
 * @param string $expire time in secounds until expire.
 * @return array of data
 */
function ScouternaPlugins_ScoutetCacheIt($name, $data, $group = "ScouternaPlugins", $expire = 1800) {
	$result = wp_cache_get($name,$group);
	if ( false === $result ) {
		$result = @file_get_contents($data);
		wp_cache_set($name, $result, $group, $expire);
	}
	return $result;
}

/**
 * ScouternaPlugins_ScoutnetGetGroupname()
 *
 * Retrieves the name of the group.
 *
 * @return string name of the group
 */
function ScouternaPlugins_ScoutnetGetGroupname() {
	$decoded = ScouternaPlugins_ScoutnetGetGroup();
	return $decoded['Group']['name'];
}
/**
 * ScouternaPlugins_ScoutnetGetMemberscount()
 *
 * Retrieves the total amount of members.
 *
 * @return int all the members of the group
 */
function ScouternaPlugins_ScoutnetGetMemberscount() {
	$decoded = ScouternaPlugins_ScoutnetGetGroup();
	return $decoded['Group']['membercount'];
}
/**
 * ScouternaPlugins_ScoutnetGetWaitingcount()
 *
 * Retrieves the total amount on the waiting list.
 *
 * @return int persons waiting to become a member
 */
function ScouternaPlugins_ScoutnetGetWaitingcount() {
	$decoded = ScouternaPlugins_ScoutnetGetGroup();
	return $decoded['Group']['waitingcount'];
}

/**
 * ScouternaPlugins_ScoutnetGetLeaders()
 *
 * Retrieves members that have a unitrole matching 1-3
 *
 * @return array with membernumber
 */
function ScouternaPlugins_ScoutnetGetLeaders() {
	$decoded = ScouternaPlugins_ScoutnetGetMemberlist();
	$members = $decoded['data'];
	$returnarray = array();
	foreach ($members as $key => $value) {
		if (!empty($value['unit_role']['raw_value'])) {
			if (!is_numeric($value['unit_role']['raw_value'])) {
				preg_match_all('/\d+/', $value['unit_role']['raw_value'], $matches);
				if ($matches[0][0] == "1" || $matches[0][0] == "1" || $matches[0][0] == "3" || $matches[0][1] == "1" || $matches[0][1] == "1" || $matches[0][1] == "3")
					$returnarray[] = $value['member_no']['value'];
			}
			if ($value['unit_role']['raw_value'] == 1 || $value['unit_role']['raw_value'] == 2 || $value['unit_role']['raw_value'] == 3)
 				$returnarray[] = $value['member_no']['value'];
		}
	}
	return $returnarray;
}

/**
 * ScouternaPlugins_ScoutnetGetUnitmembers()
 *
 * Sorts out all members that got an unitrole matching 1-3
 *
 * @param string $searchedunit name of unit.
 * @param boolean $exlude if true, counts members without group role
 * @return array with first name
 */
function ScouternaPlugins_ScoutnetGetUnitmembers($searchedunit,$exlude=false) {
	$decoded = ScouternaPlugins_ScoutnetGetMemberlist();
	$members = $decoded['data'];
	$returnarray = array();
	foreach ($members as $key => $value) {
		if ($exlude) {
			if (ScouternaPlugins_ScoutnetFixThatString($value['unit']['value']) === ScouternaPlugins_ScoutnetFixThatString($searchedunit) && !isset ($value['group_role']))
				$returnarray[] = $value['first_name']['value'];
		} else {
			if (ScouternaPlugins_ScoutnetFixThatString($value['unit']['value']) === ScouternaPlugins_ScoutnetFixThatString($searchedunit))
				$returnarray[] = $value['first_name']['value'];
		}
	}
	return $returnarray;
}

/**
 * ScouternaPlugins_ScoutnetGetStaff()
 *
 * Sorts out all members that go an unit role and no group role
 *
 * @return array with member number
 */
function ScouternaPlugins_ScoutnetGetStaff() {
	$decoded = ScouternaPlugins_ScoutnetGetMemberlist();
	$members = $decoded['data'];
	$returnarray = array();
	foreach ($members as $key => $value) {
		if (empty($value['unit_role']['raw_value']) && !empty($value['group_role']['raw_value']))
			$returnarray[] = $value['member_no']['value'];
	}
	return $returnarray;
}

/**
 * ScouternaPlugins_ScoutnetFixThatString()
 *
 * Cleans the string from specialchars and makes it lowercase
 *
 * @param string $thestring String to fix
 * @return string fixed string
 */
function ScouternaPlugins_ScoutnetFixThatString($thestring) {
	return strtolower(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($thestring, ENT_COMPAT, 'UTF-8')));
}

/**
 * ScouternaPlugins_ScoutnetGetAllAssleders()
 *
 * Sorts out all members that got an unitrole matching 5
 *
 * @return array with member number
 */
function ScouternaPlugins_ScoutnetGetAllAssleders() {
	$decoded = ScouternaPlugins_ScoutnetGetMemberlist();
	$members = $decoded['data'];
	$returnarray = array();
	foreach ($members as $key => $value) {
		if (!empty($value['unit_role']['raw_value'])) {
			if (!is_numeric($value['unit_role']['raw_value'])) {
				preg_match_all('/\d+/', $value['unit_role']['raw_value'], $matches);
				if ($matches[0][0] == "5" || $matches[0][1] == "5")
					$returnarray[] = $value['member_no']['value'];
				}
				if ($value['unit_role']['raw_value'] == 5)
					$returnarray[] = $value['member_no']['value'];
			}
	}
	return $returnarray;
}

/**
 * ScouternaPlugins_ScoutnetColorThatBradgard()
 *
 * Echos a string with a colored # depending on if the $functiontocheck is not empty or if it's true
 *
 * @param function $functiontocheck 
 * @param boolean $mode String to fix
 */
function ScouternaPlugins_ScoutnetColorThatBradgard($functiontocheck, $mode="") {
	$color = "#FF0000";
	if (empty($mode))
		if(!empty($thingtocheck))
			$color = "#00FF00";
	if ($mode == "bool")
		if (ScouternaPlugins_ScoutnetCheckRegistermemeber() == true)
			$color = "#00FF00";
	echo "<span style=\"color: $color\">#</span>";
}
?>
