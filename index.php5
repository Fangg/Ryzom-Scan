<?php
require_once('ryzom_api/ryzom_api.php');
include ('include/ryzom_translate.php');

if(isset($_POST['key']) && $_POST['key'] != '') header('Location: ?ckey='.ryzom_encrypt($_POST['key']).'&ckeyp='.$_POST['ckeyp'].'&language='.$_GET['language']);

ryzom_ckey_handle();

header('Content-Type:text/html; charset=UTF-8');

if(isset($_GET['ckey']) && $_GET['ckey'] != '') {
	$ckey = $_GET['ckey'];
	$key = ryzom_decrypt($ckey);
    setcookie("key_save", $key, time()+60*60*24*30);
	}

if(isset($_GET['ckeyp']) && $_GET['ckeyp'] != '') {
	$ckey = $_GET['ckey'];
	$key = ryzom_decrypt($ckey);
    setcookie("key_save_guild", $key, time()+60*60*24*30);
	}

echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
	<title>Ryzom Scan</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	'.ryzom_render_header().'
    <link type="text/css" href="ryzom_api/render/flexcrollstyles.css" rel="stylesheet" media="all" />
	<link type="text/css" href="ryzom_api/render/dtree.css" rel="stylesheet" media="all" />
	<link type="text/css" href="ryzom_api/render/scan.css" rel="stylesheet" media="all" />
	'.ryzom_render_header_www().'
    <script type="text/javascript" src="js/flexcroll.js"></script>
	<script type="text/javascript" src="js/dtree.js"></script>
	</head>
	<body>
';

if (isset($_GET['ckey']) && $_GET['ckey'] != '') {

	ryzom_log_start('rscan');

	$ckey = $_GET['ckey'];
	$key = ryzom_decrypt($ckey);
    
	$uid=0;$gid=0;$slot=0;$full=false;
	
	if(ryzom_character_valid_key($key, $uid, $slot, $full)) {
		$xml = ryzom_character_simplexml($key, 'full');
	} else if(ryzom_guild_valid_key($key, $gid)) {
		$xml = ryzom_guild_simplexml($key);
	} else {
		$xml = ryzom_error('Not valid character or guild key', 'simplexml');
	}
	
	if($xml->getName() == 'error') {
		$content = '<div class="error">'.$xml.'</div>';
		$content.= '<a class="ryzom-ui-button" href="?">Retour</a>';
		die(ryzom_render_www(ryzom_render_window('API error', $content)));
	}
	
    if (empty($_GET['language'])) {$_GET['language'] = 'fr';}
    include ('include/language.php');

	if((string)$xml->getName()=='character') {
	    if($_GET[p] == '1' OR $_GET[p] == '') {$title=$lang_equip;$content=scan_equipement($xml,$key);}
	    if($_GET[p] == '2') {$title=$lang_bag;$content=scan_bag($xml,$key);}
	    if($_GET[p] == '3') {$title=$lang_seller;$content=scan_sold($xml,$key);}
	    if($_GET[p] == '4') {$title=$lang_pack1;$content=scan_toub1($xml,$key);}
	    if($_GET[p] == '5') {$title=$lang_pack2;$content=scan_toub2($xml,$key);}
	    if($_GET[p] == '6') {$title=$lang_pack3;$content=scan_toub3($xml,$key);}
	    if($_GET[p] == '7') {$title=$lang_pack4;$content=scan_toub4($xml,$key);}
	    if($_GET[p] == '8') {$title=$lang_id;$content=scan_identity($xml,$key);}
	    if($_GET[p] == '9') {$title=$lang_action;$content=scan_skilltree($xml,$key);}
	    if($_GET[p] == '10') {$title=$lang_room;$content=scan_room($xml,$key);}
	    if($_GET[p] == '11') {$title=$lang_guild;$content=scan_guild($xml,$key);}
	    if($_GET[p] == '12') {$title=$lang_account;$content=scan_account($xml,$key);}
	    if($_GET[p] == '13') {$title='Release note';$content=scan_release($xml,$key);}
	    $content.= '<table cellspacing=0 cellspadding=0 background="ryzom_api/render/skin_blank.png"><tr><td><a class="ryzom-ui-button" href="?"><font color="red">'.$lang_key.'</font></a></td><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=1&language='.$_GET[language].'">'.$lang_equip.'</a></td><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=2&language='.$_GET[language].'">'.$lang_bag.'</a></td><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=3&language='.$_GET[language].'">'.$lang_seller.'</a></td></tr>
	        <tr><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=4&language='.$_GET[language].'">'.$lang_pack1.'</a></td><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=5&language='.$_GET[language].'">'.$lang_pack2.'</a></td><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=6&language='.$_GET[language].'">'.$lang_pack3.'</a></td><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=7&language='.$_GET[language].'">'.$lang_pack4.'</a></td></tr>
		    <tr><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=10&language='.$_GET[language].'">'.$lang_room.'</a></td><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=8&language='.$_GET[language].'">'.$lang_id.'</a></td><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=9&language='.$_GET[language].'">'.$lang_action.'</a></td><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&p=11&language='.$_GET[language].'">'.$lang_guild.'</a></td></tr></table>';
	} else {
	    if($_GET[pg] == '') {$title=$lang_guild;$content=render_guild($xml);}
	    if($_GET[pg] == '2') {$title=$lang_trunk;$content=render_guild_room($xml);}
	    if($_GET[pg] == '3') {$title=$lang_list;$content=scan_list();}
	}

	ryzom_log_end();
} else {
    if (empty($_GET['language'])) {$_GET['language'] = 'fr';}
	include ('include/language.php');
	// Display the form to enter the API Key
	$title = 'Ryzom Scan';
	$content = '<div id="full" class="flexcroll">';
	$content .= '<form action="" method="post">';
	$content .= $lang_phrase1;
	$content .= '<input type="text" name="key" value="'.$_COOKIE[key_save].'"><br/>';
	$content .= '<input type="submit" value="'.$lang_submit.'" />';
	$content .= '</form>';
	$content .= '<a href="http://www.ryzom.com/"><img style="margin-top: 120px;" border="0" src="https://secure.ryzom.com/images/ryzom_logo.png" alt=""/></a>';
	$content .= '</div>';
}

function render_guild($xml){
    include ('include/language.php');
	$guild_icon_small = ryzom_guild_icon_image($xml->icon, 's');
	$result = $xml->xpath('/guild/members/*');
	$members=array();
	$s_gk=array();// multi array sort keys
	$s_nk=array();
	$key=0;
	while(list(,$item)=each($result)) {
		$members[$key]=array(
			'joined' => intval($item->joined_date), // joined_date is in server tick (ingame date)
			'grade'  => (string)$item->grade,
			'name'   => (string)$item->name,
			);
		$s_gk[$key]=memberGrade($members[$key]['grade']);
		$s_nk[$key]=$members[$key]['name'];
		$key++;
	}
	// sort members by grade, then by name
	array_multisort($s_gk, SORT_ASC, $s_nk, SORT_ASC, $members);
	
	$content = '<div id="full" class="flexcroll">';
	$content.= '<div class="statfond">';
	$content.= '<div class="statfull"><span class="statr"><a href="?ckey='.$_GET[ckey].'&ckeyp='.$_GET[ckeyp].'&pg=3&language='.$_GET[language].'">'.$lang_list.'</a>&nbsp;</span></div>';
	$content.= '&nbsp;'.$guild_icon_small.'<font size="2"><span class="droit2"><div style="font-size: 16px;"><b>'.$xml->name.'</b></div><div>'.ryzom_time_txt(ryzom_time_array($xml->creation_date, '')).'/ '.count($members).'&nbsp;'.$lang_memb.'</div></span></font>';
	$content.= '<div class="statfull"></div>';
	$content.= '<div class="statfullm"><font color="yellow">&nbsp;'.$lang_mess.'&nbsp;:&nbsp;'.$xml->motd.'</font></div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_desc.'</div>';
	$content.= '<div class="statfull">&nbsp;'.$xml->description.'</div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_hq.'&gt;&nbsp;'.$xml->building.'</div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_gp.'&gt;&nbsp;0</div>';
	$content.= '<div class="statfull">&nbsp;DAPPERS&gt;&nbsp;'.$xml->money.'</div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_fame.'</b></div>';
	$content.= '<div class="statleftf"><span class="statr">'.floor(intval($xml->fames->fyros)/6000).'&nbsp;</span>&nbsp;Fyros</div><div class="statrightf"><span class="statr">'.floor(intval($xml->fames->matis)/6000).'&nbsp;</span>&nbsp;Matis</div>';
	$content.= '<div class="statleftf"><span class="statr">'.floor(intval($xml->fames->tryker)/6000).'&nbsp;</span>&nbsp;Tryker</div><div class="statrightf"><span class="statr">'.floor(intval($xml->fames->zorai)/6000).'&nbsp;</span>&nbsp;Zora&iuml;</div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_civ.'&nbsp;:&nbsp;<b>'.$xml->civ.'</b></div>';
	$content.= '<div class="statleftf"><span class="statr">'.floor(intval($xml->fames->kami)/6000).'&nbsp;</span>&nbsp;Kami</div><div class="statrightf"><span class="statr">'.floor(intval($xml->fames->karavan)/6000).'&nbsp;</span>&nbsp;Karavan</div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_cult.'&nbsp;:&nbsp;<b>'.$xml->cult.'</b></div>';
	$content.= '<div class="statfull"></div>';
	$content.= '<div class="statleft">&nbsp;'.$lang_memb.'</div><div class="statright">&nbsp;'.$lang_rank.'</div>';
	$content.= '<div id="cut2" class="flexcroll">';
	foreach($members as $member) {
	$content.= '<div class="statleftf">&nbsp;'.$member['name'].'</div><div class="statrightf2">&nbsp;'.$member['grade'].'</div>';
	}
	$content.= '</div>';
	$content.= '</div>';
	$content.= '</div>';
	$content.= '<table cellspacing=0 cellspadding=0 background="ryzom_api/render/skin_blank.png" width="100%"><tr>
	            <td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckey].'&ckeyp='.$_GET[ckeyp].'&pg=2&language='.$_GET[language].'">'.$lang_trunk.'</a></td>
	            <td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckeyp].'&p=1&language='.$_GET[language].'">'.$lang_back.'</a></td></tr>';
	$content.= '</tr></table>';
	return $content;
}

function render_guild_room($xml){
        include ('include/language.php');
	    $result = $xml->xpath('/guild/room/*');
	    $content = '<div id="full" class="flexcroll">';
		while(list( , $item) = each($result))
		{
		$content.= ryzom_item_icon_image_from_simplexml($item,ryzom_translate($item, $_GET[language]));
		}
		$content.= '</div><table cellspacing=0 cellspadding=0 background="ryzom_api/render/skin_blank.png"><tr><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckeyp].'&p=1&language='.$_GET[language].'">'.$lang_back.'</a></td></tr></table>';
	return $content;
}

function scan_list() {
        require_once('ryzom_api/ryzom_api.php');
        include ('include/language.php');
		$guild_xml = ryzom_guilds_simplexml('ani');
	    $result = $guild_xml->xpath('/guilds/*');
		
	    $content = '<div id="full" class="flexcroll">';
		foreach($result as $guild)
		{
		if (!empty($guild->name)) {
	    $guild_icon_small = ryzom_guild_icon_image($guild->icon, 's');
	    $content.= '<div class="statfullc"></div>';
	    $content.= '&nbsp;'.$guild_icon_small.'<font size="2"><span class="droit2"><div style="font-size: 16px;"><b>'.$guild->name.'</b></div><div>'.ryzom_time_txt(ryzom_time_array($guild->creation_date, '')).'</div></span></font>';
	    $content.= '<div class="statfullc">&nbsp;'.$lang_race.' : '.$guild->race.'</div>';
	    $content.= '<div class="statfullcm">&nbsp;'.$guild->description.'</div>';
		}}
		$content.= '</div><table cellspacing=0 cellspadding=0 background="ryzom_api/render/skin_blank.png"><tr><td><a class="ryzom-ui-button" href="?ckey='.$_GET[ckeyp].'&p=1&language='.$_GET[language].'">'.$lang_back.'</a></td></tr></table>';
	return $content;
}

function memberGrade($grade) {
	switch(strtolower($grade)) {
	case 'leader': return 0;
	case 'highofficer': return 1;
	case 'officer': return 2;
	case 'member':
	default: return 3;
	}
}

function scan_equipement($xml, $key) {
    include ('include/language.php');
	$title = ryzom_title_txt($xml->titleid, $_GET[language], $xml->gender);
	$inv_xml = ryzom_character_simplexml($key, 'items');

	$result = $xml->xpath('/character/hands/*');
	while(list( , $item) = each($result))
	{
    ${$item[part].'_icon'} = ryzom_item_icon_image_from_simplexml($item,ryzom_translate($item, $_GET[language]));
	}
	
	$result = $xml->xpath('/character/equipments/*');
	while(list( , $item) = each($result))
	{
    ${$item[part].'_out'} = '&'.$item[part].'='.$item.'/'.$item[c];
    ${$item[part].'_icon'} = ryzom_item_icon_image_from_simplexml($item,ryzom_translate($item, $_GET[language]));
	}
	
	if($xml->guild->name != '') {
		$guild_name = $xml->guild->name;
		$guild_icon = ryzom_guild_icon_image($xml->guild->icon, 'b');
		$guild_icon_small = ryzom_guild_icon_image($xml->guild->icon, 's');
	} else {
		$guild_name = $lang_noguild;
	}
	
	if($xml->cult == 'karavan') {
	    $cult = 'kara.png';
	} elseif($xml->cult == 'kami')  {
	    $cult = 'kami.png';
	} else {
	    $cult = 'empty.png';
	}
	
	if($xml->latest_logout == '0') {
	    $live = 'on';
	} else {
	    $live = 'off';
	}
	
	$content = '<div class="decor">
	                <div class="colg">
					    <div class="haut"></div>
						<div class="ic"></div>
						<div class="ic">'.$left_icon.'</div>
						<div class="ic">'.$necklace_icon.'</div>
						<div class="ic">'.$ear_l_icon.'</div>
						<div class="ic">'.$wrist_l_icon.'</div>
						<div class="ic">'.$finger_l_icon.'</div>
						<div class="ic">'.$ankle_l_icon.'</div>
					</div>
                    <div class="perso" style="background-image: url(http://ballisticmystix.net/api/dressingroom.php?race='.$xml->race.'&gender='.$xml->gender.'&hair='.$xml->body->hair_type.'/'.$xml->body->hair_color.'&tattoo='.$xml->body->tattoo.'&eyes='.$xml->body->eyes_color.$feet_out.$hands_out.$legs_out.$arms_out.$chest_out.$head_out.');">
					    <div class="haut2">'.$guild_icon_small.'<font size="2"><span class="droit2"><div style="font-size: 16px;"><b>'.$xml->name.'</b></div><div>'.$title.'</div><div>'.$guild_name.'&nbsp;&nbsp;<img class="cult" src="ryzom_api/render/'.$cult.'"></div></span></font></div>
						<div class="ic2"></div>
						<div class="ic2"></div>
						<div class="ic2"></div>
						<div class="ic2"></div>
						<div class="ic2"><span class="gauche">'.$head_icon.'</span><span class="droit">'.$chest_icon.'</span></div>
						<div class="ic2"><span class="gauche">'.$arms_icon.'</span><span class="droit">'.$legs_icon.'</span></div>
						<div class="ic2"><span class="gauche">'.$hands_icon.'</span><span class="droit">'.$feet_icon.'</span></div>
					</div>
	                <div class="cold">
					    <div class="haut"><img vspace="7" border="0" src="ryzom_api/render/'.$live.'.png" /><img hspace="2" vspace="10" border="0" src="http://www.ryzom.com/data/'.$_GET[language].'_v6.jpg" /></div>
						<div class="ic"></div>
						<div class="ic">'.$right_icon.'</div>
						<div class="ic">'.$head_dress_icon.'</div>
						<div class="ic">'.$ear_r_icon.'</div>
						<div class="ic">'.$wrist_r_icon.'</div>
						<div class="ic">'.$finger_r_icon.'</div>
						<div class="ic">'.$ankle_r_icon.'</div>
					</div>
				</div>';
	return $content;
}

function scan_bag($xml, $key) {
	$inv_xml = ryzom_character_simplexml($key, 'items');
	if(isset($inv_xml->inventories->bag)) {
	    $content = '<div id="full" class="flexcroll">';
		$result = $inv_xml->xpath('/items/inventories/bag/*');
		while(list( , $item) = each($result))
		{
			$content .= ryzom_item_icon_image_from_simplexml($item, ryzom_translate($item, $_GET[language]).' - '.$lang_price.$item[price]);
		}
		$content.= '</div>';
	}
	return $content;
}

function scan_sold($xml, $key) {
    include ('include/language.php');
	$inv_xml = ryzom_character_simplexml($key, 'items');
	if(isset($inv_xml->item_in_store)) {
	    $content = '<div id="full" class="flexcroll">';
		$result = $inv_xml->xpath('/items/item_in_store/*');
		while(list( , $item) = each($result))
		{
			$content .= ryzom_item_icon_image_from_simplexml($item, ryzom_translate($item, $_GET[language]).' - '.$lang_price.$item[price]);
		}
		$content.= '</div>';
	}
	return $content;
}

function scan_toub1($xml, $key) {
	$inv_xml = ryzom_character_simplexml($key, 'items');
	if(isset($inv_xml->inventories->pet_animal1)) {
	    $content = '<div id="full" class="flexcroll">';
		$result = $inv_xml->xpath('/items/inventories/pet_animal1/*');
		while(list( , $item) = each($result))
		{
		$content.= ryzom_item_icon_image_from_simplexml($item,ryzom_translate($item, $_GET[language]));
		}
		$content.= '</div>';
	}
	return $content;
}

function scan_toub2($xml, $key) {
	$inv_xml = ryzom_character_simplexml($key, 'items');
	if(isset($inv_xml->inventories->pet_animal2)) {
	    $content = '<div id="full" class="flexcroll">';
		$result = $inv_xml->xpath('/items/inventories/pet_animal2/*');
		while(list( , $item) = each($result))
		{
		$content.= ryzom_item_icon_image_from_simplexml($item,ryzom_translate($item, $_GET[language]));
		}
		$content.= '</div>';
	}
	return $content;
}

function scan_toub3($xml, $key) {
	$inv_xml = ryzom_character_simplexml($key, 'items');
	if(isset($inv_xml->inventories->pet_animal3)) {
	    $content = '<div id="full" class="flexcroll">';
		$result = $inv_xml->xpath('/items/inventories/pet_animal3/*');
		while(list( , $item) = each($result))
		{
		$content.= ryzom_item_icon_image_from_simplexml($item,ryzom_translate($item, $_GET[language]));
		}
		$content.= '</div>';
	}
	return $content;
}

function scan_toub4($xml, $key) {
	$inv_xml = ryzom_character_simplexml($key, 'items');
	if(isset($inv_xml->inventories->pet_animal4)) {
	    $content = '<div id="full" class="flexcroll">';
		$result = $inv_xml->xpath('/items/inventories/pet_animal4/*');
		while(list( , $item) = each($result))
		{
		$content.= ryzom_item_icon_image_from_simplexml($item,ryzom_translate($item, $_GET[language]));
		}
		$content.= '</div>';
	}
	return $content;
}

function scan_identity($xml, $key) {
    include ('include/language.php');
	$title = ryzom_title_txt($xml->titleid, $_GET[language], $xml->gender);
	
	if($xml->guild->name != '') {
		$guild_name = $xml->guild->name;
	} else {
		$guild_name = $lang_noguild;
	}
	
	$content.= '<div class="statfond">';
	$content.= '<div class="statfull"><span class="statr"><a href="?ckey='.$_GET[ckey].'&p=12&language='.$_GET[language].'">'.$lang_account.'</a>&nbsp;</span>&nbsp;'.$lang_race.'&gt;&nbsp;<b>'.$xml->race.'</b></div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_title.'&gt;&nbsp;<b>'.$title.'</b></div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_member.'&gt;&nbsp;<b>'.$guild_name.'</b></div>';
	$content.= '<div class="statfull">&nbsp;DAPPERS&gt;&nbsp;<b>'.$xml->money.'</b></div>';
	$content.= '<div class="statleft">&nbsp;'.$lang_score.'</div><div class="statright">&nbsp;'.$lang_caract.'</div>';
	$content.= '<div class="statleftf">&nbsp;'.$lang_value.'</div><div class="statrightf"></div>';
	$content.= '<div class="statleftf"><span class="statr">'.$xml->phys_scores->hitpoints.'/'.$xml->phys_scores->hitpoints[max].'&nbsp;</span>&nbsp;'.$lang_HP.'</div><div class="statrightf"><span class="statr">'.$xml->phys_characs->constitution.'&nbsp;</span>&nbsp;'.$lang_const.'</div>';
	$content.= '<div class="statleftf"><span class="statr">'.$xml->phys_scores->sap.'/'.$xml->phys_scores->sap[max].'&nbsp;</span>&nbsp;'.$lang_sap.'</div><div class="statrightf"><span class="statr">'.$xml->phys_characs->intelligence.'&nbsp;</span>&nbsp;'.$lang_intel.'</div>';
	$content.= '<div class="statleftf"><span class="statr">'.$xml->phys_scores->stamina.'/'.$xml->phys_scores->stamina[max].'&nbsp;</span>&nbsp;'.$lang_stam.'</div><div class="statrightf"><span class="statr">'.$xml->phys_characs->strength.'&nbsp;</span>&nbsp;'.$lang_strenght.'</div>';
	$content.= '<div class="statleftf"><span class="statr">'.$xml->phys_scores->focus.'/'.$xml->phys_scores->focus[max].'&nbsp;</span>&nbsp;'.$lang_focus.'</div><div class="statrightf"><span class="statr">'.$xml->phys_characs->dexterity.'&nbsp;</span>&nbsp;'.$lang_dext.'</div>';
	$content.= '<div class="statfull"></div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_point.'</b></div>';
	$content.= '<div class="statleftf"><span class="statr">'.$xml->faction_points->kami.'&nbsp;</span>&nbsp;Kami</div><div class="statrightf"><span class="statr">'.$xml->faction_points->karavan.'&nbsp;</span>&nbsp;Karavan</div>';
	$content.= '<div class="statleftf"><span class="statr">'.$xml->faction_points->fyros.'&nbsp;</span>&nbsp;Fyros</div><div class="statrightf"><span class="statr">'.$xml->faction_points->matis.'&nbsp;</span>&nbsp;Matis</div>';
	$content.= '<div class="statleftf"><span class="statr">'.$xml->faction_points->zorai.'&nbsp;</span>&nbsp;Zora&iuml;</div><div class="statrightf"><span class="statr">'.$xml->faction_points->tryker.'&nbsp;</span>&nbsp;Tryker</div>';
	$content.= '<div class="statfull"></div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_fame.'</b></div>';
	$content.= '<div class="statleftf"><span class="statr">'.floor(intval($xml->fames->fyros)/6000).'&nbsp;</span>&nbsp;Fyros</div><div class="statrightf"><span class="statr">'.floor(intval($xml->fames->matis)/6000).'&nbsp;</span>&nbsp;Matis</div>';
	$content.= '<div class="statleftf"><span class="statr">'.floor(intval($xml->fames->tryker)/6000).'&nbsp;</span>&nbsp;Tryker</div><div class="statrightf"><span class="statr">'.floor(intval($xml->fames->zorai)/6000).'&nbsp;</span>&nbsp;Zora&iuml;</div>';
	$content.= '<div class="statleft"><span class="statr"><b>'.$xml->civ.'</b>&nbsp;</span>&nbsp;'.$lang_civ.'</div><div class="statright"></div>';
	$content.= '<div class="statleftf"><span class="statr">'.floor(intval($xml->fames->kami)/6000).'&nbsp;</span>&nbsp;Kami</div><div class="statrightf"><span class="statr">'.floor(intval($xml->fames->karavan)/6000).'&nbsp;</span>&nbsp;Karavan</div>';
	$content.= '<div class="statleft"><span class="statr"><b>'.$xml->cult.'</b>&nbsp;</span>&nbsp;'.$lang_cult.'</div><div class="statright"></div>';
	$content.= '<div class="statfull">&nbsp;'.$lang_tribe.'</div>';
	$content.= '<div id="cut" class="flexcroll">';
    $fames = array();
    foreach($xml->fames->children() as $node){
		$name = (string) $node->getName();
		$level = floor(intval($node)/6000);
	if ($name != 'karavan' AND $name != 'kami' AND $name != 'fyros' AND $name != 'matis' AND $name != 'zorai' AND $name != 'tryker') {
    $txt=ryzom_translate($name.'.faction', $_GET[language]);
	$content.= '<div class="statleftf">&nbsp;'.$txt.'</div><div class="statrightf2">'.$level.'</div>';
	}
    }
	$content.= '</div>';
	$content.= '</div>';
	return $content;
}

function scan_skilltree($xml, $key) {
    include ('include/language.php');
    include ('include/skilltree.php');
	return $content;
}

function scan_room($xml, $key) {
	$inv_xml = ryzom_character_simplexml($key, 'items');
	if(isset($inv_xml->room)) {
	    $content = '<div id="full" class="flexcroll">';
		$result = $inv_xml->xpath('/items/room/*');
		while(list( , $item) = each($result))
		{
		$content.= ryzom_item_icon_image_from_simplexml($item,ryzom_translate($item, $_GET[language]));
		}
		$content.= '</div>';
	}
	return $content;
}

function scan_guild($xml, $key) {
        include ('include/language.php');
	    $content = '<div id="full" class="flexcroll">';
		$content .= '<form action="" method="post">';
		$content .= $lang_phrase2;
		$content .= '<input type="text" name="key" value="'.$_COOKIE[key_save_guild].'"><br/>';
		$content .= '<input type="hidden" name="ckeyp" value="'.$_GET[ckey].'">';
		$content .= '<input type="submit" value="'.$lang_submit.'" />';
		$content .= '</form>';
	    $content .= '<a href="http://www.ryzom.com/"><img style="margin-top: 120px;" border="0" src="https://secure.ryzom.com/images/ryzom_logo.png" alt=""/></a>';
		$content .= '</div>';
	return $content;
}

function strTime($s) {

  include ('include/language.php');

  $d = intval($s/86400);
  $s -= $d*86400;

  $h = intval($s/3600);
  $s -= $h*3600;

  $m = intval($s/60);
  $s -= $m*60;

  if ($d) $str = $d.' '.$lang_day.', ';
  if (isset($h)) {$str .= $h.':';} else {$str .= '0 :';}
  if (isset($m)) {$str .= $m.':';} else {$str .= '0 :';}
  if (isset($s)) {$str .= $s;} else {$str .= '0';}

  return $str;
}

function scan_account($xml, $key) {
        include ('include/language.php');
		$latest = (int)$xml->latest_login;
	    $content = '<div id="full" class="flexcroll">';
	    $content .= '<div class="statfond">';
		$content .= '<div class="statfull"><span class="statr"><a href="?ckey='.$_GET[ckey].'&p=13&language='.$_GET[language].'">Release note</a>&nbsp;</span>&nbsp;SHARD&gt; <span class="maj"><b>'.$xml->shard.'</b></span></div>';
		$content .= '<div class="statfull">&nbsp;ID&gt; '.$xml->uid.'</div>';
		$content .= '<div class="statfull">&nbsp;'.$lang_time.'&gt; <b>'.strTime($xml->played_time).'</b></div>';
		$content .= '<div class="statfull">&nbsp;'.$lang_seen.'&gt; '.date("D, d M Y H:i:s", (int)$xml->latest_login).'</div>';                       
		$content .= '<div class="statfull"></div>';                       
		$content .= '<div class="statfullc"><span class="statr">'.$lang_length.'&nbsp;</span>&nbsp;'.$lang_date.'</div>';
		$content .= '</div>';
	    $content .= '<div id="cut3" class="flexcroll">';
        foreach ($xml->latest_connection->log as $log) {
		$content .= '<div class="statfullc"><span class="statr">'.strTime($log[duration]).'&nbsp;</span>&nbsp;'.date("D, d M Y H:i:s", (int)$log[in]).'</div>';                       
		}
		$content .= '</div></div>';
	return $content;
}

function scan_release($xml, $key) {
	    $content = '<div id="full" class="flexcroll">';
		$content .= file_get_contents('http://atys.ryzom.com/releasenotes/index.php?version=642&lang=fr&r=1&ca=ryzom_live');
		$content .= '</div>';
	return $content;
}

echo ryzom_render_www(ryzom_render_window($title, $content));
echo '</body></html>';
?>ssh-keygen -C “username@email.com” -t rsa