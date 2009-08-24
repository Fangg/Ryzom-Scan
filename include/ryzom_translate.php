<? 
define('RYZOM_API_PATH', dirname(__FILE__).'/');
// translation array
if(!isset($GLOBALS['__ryzom_words'])){
	$GLOBALS['__ryzom_words']=array();
}
/**
 * Get $sheetid translation from .suffix language file.
 * Include language file on first run and cache it.
 *
 * @param string sheetid
 * @param string lang
 * @param int gender 0=male, 1=female
 *
 * @return string translated text, error message if language file or sheet id is not found
 */
function ryzom_translate($sheetid, $lang, $gender=0){
	
	// break up sheetid
	$_id = strtolower($sheetid);
	$_ext=strtolower(substr(strrchr($sheetid, '.'), 1));
	if($_ext===false || $_ext==''){
		$_ext='title'; // 'title' should be only one without 'dot' in sheetid
	}else{
		$_id=substr($_id, 0, strlen($_id)-strlen($_ext)-1);
	}

	// remap
	if($_ext=='sitem') $_ext='item';

	// 'Neutral' is not included in faction translation, so do it here
	if($_ext=='faction' && $_id=='neutral'){
		if($lang=='fr') {
			return 'Neutre';
		}else{
			return 'Neutral';
		}
	}

	// include translation file if needed
	if(!isset($GLOBALS['__ryzom_words'][$_ext][$lang])){
		// use serialize/unserialize saves lot of memory
		$file = RYZOM_API_PATH.'include/'.$_ext.'_'.$lang.'.serial';
		if(file_exists($file)){
			$ret=@unserialize(file_get_contents($file));
			if($ret!==false){
				$GLOBALS['__ryzom_words'][$_ext][$lang] = $ret; 
			}
		}
	}


	// check if translation is there
	if(!isset($GLOBALS['__ryzom_words'][$_ext][$lang][$_id])){
		return 'NotFound:('.$_ext.')'.$lang.'.'.$sheetid;
	}

	// return translation - each may have different array 'key' for translation
	$word=$GLOBALS['__ryzom_words'][$_ext][$lang][$_id];
	switch($_ext){
		case 'place':
			return $word['name'];
		case 'uxt':
			return $word; // plain text and not array
		case 'faction':
			return $word['name'];
		case 'item':
			return $word['name'];
		case 'skill':
			return $word['name'];
		case 'title': // titles ?
			if((int) $gender==0){
				return $word['name'];
			}else{
				return $word['women_name'];
			}
	}
	// should never reach here, but incase it does...
	return 'Unknown:'.$_ext.'.'.$_id;
}
