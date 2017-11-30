<?php
/**
 * All shared functions for this plugin
 *
 * @author Joel "PazZze" Martinsson <me@pazzze.se>
 * @link eservice.scout.se
 */

$options = get_option( 'ScouternaPlugins_ScoutnetWidgets_settings' );
$karid = $options['ScouternaPlugins_ScoutnetWidgets_karid'];

/**
 * ScouternaPlugins_ScoutnetWidgets_GetGroup()
 *
 * Retrieves a list of gropinformation
 *
 * @return array of goupinformation
 */
function ScouternaPlugins_ScoutnetWidgets_GetGroup() {
	// /api/organisation/group
	global $scoutnetapiurl;
        global $options;
        global $karid;
	$apinyckel = $options['ScouternaPlugins_ScoutnetWidgets_apinyckel_group'];
	$result = ScouternaPlugins_ScoutetCacheIt("group","https://$karid:$apinyckel@$scoutnetapiurl/api/organisation/group");
	if($result !== FALSE)
		return json_decode($result, true);
}
/**
 * ScouternaPlugins_ScoutnetWidgets_GetMemberlist()
 *
 * Retrieves a list all the members
 *
 * @return array of all members
 */
function ScouternaPlugins_ScoutnetWidgets_GetMemberlist() {
	// /api/group/memberlist
	global $scoutnetapiurl;
	global $options;
	global $karid;
	$apinyckel = $options['ScouternaPlugins_ScoutnetWidgets_apinyckel_members'];

	$result = ScouternaPlugins_ScoutetCacheIt("memberlist","https://$karid:$apinyckel@$scoutnetapiurl/api/group/memberlist");
	if($result !== FALSE)
		return json_decode($result, true);
}
/**
 * ScouternaPlugins_ScoutnetWidgets_GetCustomlist()
 *
 * Retrieves a custom created list
 *
 * @param string $listid id of the custom list.
 * @return array of all members in the list.
 */
function ScouternaPlugins_ScoutnetWidgets_GetCustomlist($listid = false) {
	// /api/group/customlists 
	global $scoutnetapiurl;
        global $options;
        global $karid;
	$apinyckel = $options['ScouternaPlugins_ScoutnetWidgets_apinyckel_mail'];
	if ($listid == false) {
		$result = ScouternaPlugins_ScoutetCacheIt("customlists","https://$karid:$apinyckel@$scoutnetapiurl/api/group/customlists");
	}
	else {
		$result = @file_get_contents("https://$karid:$apinyckel@$scoutnetapiurl/api/group/customlists?list_id=$listid");
	}
	if($result !== FALSE)
		return json_decode($result, true);
}
/**
 * ScouternaPlugins_ScoutnetWidgets_CheckRegistermemeber()
 *
 * A function to check if the API-key for register members is working
 *
 * @return boolean true if the site returns a 400 https status code
 */
function ScouternaPlugins_ScoutnetWidgets_CheckRegistermemeber() {
	global $scoutnetapiurl;
        global $options;
        global $karid;
	$apinyckel = $options['ScouternaPlugins_ScoutnetWidgets_apinyckel_waitinglist'];
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
 * @param string $group groups all cache, default "ScouternaPlugins".
 * @param string $expire time in secounds until expire, default "1800s/30min".
 * @return array of data
 */
function ScouternaPlugins_ScoutetCacheIt($name, $data, $group = "ScouternaPlugins", $expire = 1800) {
	$result = wp_cache_get($name,$group);
	if (false === $result) {
		$result = @file_get_contents($data);
		wp_cache_set($name, $result, $group, $expire);
	}
	return $result;
}

/**
 * ScouternaPlugins_ScoutnetWidgets_GetGroupname()
 *
 * Retrieves the name of the group.
 *
 * @return string name of the group
 */
function ScouternaPlugins_ScoutnetWidgets_GetGroupname() {
	$decoded = ScouternaPlugins_ScoutnetWidgets_GetGroup();
	return $decoded['Group']['name'];
}
/**
 * ScouternaPlugins_ScoutnetWidgets_GetMemberscount()
 *
 * Retrieves the total amount of members.
 *
 * @return int all the members of the troop
 */
function ScouternaPlugins_ScoutnetWidgets_GetMemberscount() {
	$decoded = ScouternaPlugins_ScoutnetWidgets_GetGroup();
	return $decoded['Group']['membercount'];
}
/**
 * ScouternaPlugins_ScoutnetWidgets_GetWaitingcount()
 *
 * Retrieves the total amount on the waiting list.
 *
 * @return int persons waiting to become a member
 */
function ScouternaPlugins_ScoutnetWidgets_GetWaitingcount() {
	$decoded = ScouternaPlugins_ScoutnetWidgets_GetGroup();
	return $decoded['Group']['waitingcount'];
}

/**
 * ScouternaPlugins_ScoutnetWidgets_GetLeaders()
 *
 * Retrieves members that have a unit role matching 1-3
 *
 * @return array with membernumber
 */
function ScouternaPlugins_ScoutnetWidgets_GetLeaders() {
	$decoded = ScouternaPlugins_ScoutnetWidgets_GetMemberlist();
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
 * ScouternaPlugins_ScoutnetWidgets_GetUnitmembers()
 *
 * Sorts out all members that got an unitrole matching 1-3
 *
 * @param string $searchedunit name of unit.
 * @param boolean $exlude if true, counts members without group role
 * @return array with first name
 */
function ScouternaPlugins_ScoutnetWidgets_GetUnitmembers($searchedunit,$exlude=false) {
	$decoded = ScouternaPlugins_ScoutnetWidgets_GetMemberlist();
	$members = $decoded['data'];
	$returnarray = array();
	foreach ($members as $key => $value) {
		if ($exlude) {
			if (ScouternaPlugins_ScoutnetWidgets_FixThatString($value['unit']['value']) === ScouternaPlugins_ScoutnetWidgets_FixThatString($searchedunit) && !isset ($value['group_role']))
				$returnarray[] = $value['first_name']['value'];
		} else {
			if (ScouternaPlugins_ScoutnetWidgets_FixThatString($value['unit']['value']) === ScouternaPlugins_ScoutnetWidgets_FixThatString($searchedunit))
				$returnarray[] = $value['first_name']['value'];
		}
	}
	return $returnarray;
}

/**
 * ScouternaPlugins_ScoutnetWidgets_GetStaff()
 *
 * Sorts out all members that go an unit role and no group role
 *
 * @return array with member number
 */
function ScouternaPlugins_ScoutnetWidgets_GetStaff() {
	$decoded = ScouternaPlugins_ScoutnetWidgets_GetMemberlist();
	$members = $decoded['data'];
	$returnarray = array();
	foreach ($members as $key => $value) {
		if (empty($value['unit_role']['raw_value']) && !empty($value['group_role']['raw_value']))
			$returnarray[] = $value['member_no']['value'];
	}
	return $returnarray;
}

/**
 * ScouternaPlugins_ScoutnetWidgets_FixThatString()
 *
 * Cleans the string from specialchars and makes it lowercase
 *
 * @param string $thestring String to fix
 * @return string fixed string
 */
function ScouternaPlugins_ScoutnetWidgets_FixThatString($thestring) {
	return strtolower(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($thestring, ENT_COMPAT, 'UTF-8')));
}

/**
 * ScouternaPlugins_ScoutnetWidgets_GetAllAssleders()
 *
 * Sorts out all members that got an unitrole matching 5
 *
 * @return array with member number
 */
function ScouternaPlugins_ScoutnetWidgets_GetAllAssleders() {
	$decoded = ScouternaPlugins_ScoutnetWidgets_GetMemberlist();
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
 * ScouternaPlugins_ScoutnetWidgets_ColorThatBradgard()
 *
 * Echos a string with a colored # depending on if the $functiontocheck is not empty or if it's true
 *
 * @param function $functiontocheck 
 * @param boolean $mode String to color
 */
function ScouternaPlugins_ScoutnetWidgets_ColorThatBradgard($functiontocheck, $mode="") {
	$color = "#FF0000";
	if (empty($mode))
		if(!empty($functiontocheck))
			$color = "#00FF00";
	if ($mode == "bool")
		if (ScouternaPlugins_ScoutnetWidgets_CheckRegistermemeber() == true)
			$color = "#00FF00";
	echo "<span style=\"color: $color\">#</span>";
}

/**
 * ScouternaPlugins_ScoutnetWidgets_GetBirthday()
 *
 * Sorts out all members who have a birthday today
 *
 * @param number $option the option for the list, default 0.
 * @return array with members
 */
function ScouternaPlugins_ScoutnetWidgets_GetBirthday($option=0) {
	$decoded = ScouternaPlugins_ScoutnetWidgets_GetMemberlist();
	$members = $decoded['data'];
	$returnarray = array();
	$today = date("m-d");
	foreach ($members as $key => $medlem) {
		$memberbirthdayarray = explode('-', $medlem['date_of_birth']['value']);
		$memberbirthday = $memberbirthdayarray[1]."-".$memberbirthdayarray[2];
		if ($memberbirthday == $today) {
			switch($option){
			case 2:
				$returnarray[] = $medlem['first_name']['value']." ".mb_substr($medlem['last_name']['value'],0,1);
				break;
			case 4:
				$returnarray[] = $medlem['first_name']['value']." ".$medlem['unit']['value'];
				break;
			case 5:
				$returnarray[] = $medlem['first_name']['value']." ".mb_substr($medlem['last_name']['value'],0,1)." ".$medlem['unit']['value'];
				break;
			default:
			case 0:
			case 1:
				$returnarray[] = $medlem['first_name']['value'];
				break;
			}
		}
	}
	return $returnarray;
}
?>
