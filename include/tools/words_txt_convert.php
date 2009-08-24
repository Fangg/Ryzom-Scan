<?
	define('RYZOM_API_PATH', dirname(__FILE__).'/../');

	// load item words files
	$files=array(
		'creature' 	=> array('creature ID', 'name', 'p'), 
		'faction' 	=> array('faction', 'name', 'member'), 
		'item' 		=> array('item ID', 'name', 'p', 'description'), 
		'outpost' 	=> array('outpost ID', 'name', 'description'), 
		'place' 	=> array('placeId', 'name'), 
		'sbrick' 	=> array('sbrick ID', 'name', 'p', 'description', 'description2'), 
		'skill' 	=> array('skill ID', 'name', 'p', 'description'), 
		'sphrase' 	=> array('sphrase ID', 'name', 'p', 'description'), 
		'title' 	=> array('title_id', 'name', 'women_name'),
	);
	$lang=array('en', 'fr', 'de');
	
foreach($files as $file=>$fields){
	echo "Doing file ".$file."\n";

	$qa=array();
	foreach($lang as $l){
		echo "lang [".$l."] - ";
	
		
		$fname='words_txt/'.$file.'_words_'.$l.'.txt';
		if(!file_exists($fname)){
			echo " - file not found \n";
			continue;
		}
		$content=file_get_contents($fname);
		$content=utf16_to_utf8($content);
		$content=str_replace("\r", "", $content);
		
		$lines=explode("\n", $content);
		
		// header
		$hdr=explode("\t", array_shift($lines));
		
		$keys=array(); // id's already known
		foreach($lines as $line){
			$words=explode("\t", $line);
			if(trim($line)==''){
				continue;
			}
			
			$cols=array();
			foreach($hdr as $idx=>$fieldName){
				if(in_array($fieldName, $fields)){
					// make ID lowercased
					if(!isset($words[$idx])){
						print_r($words);
					}
					if($idx==1){ // make sure id is lowercase
						$cols[$fieldName]=strtolower($words[$idx]);
					}else{
						$cols[$fieldName]=$words[$idx];
					}
				}
			}
			// skip last empty row
			if(trim(join('', $cols))=='') continue;
			$id=$cols[$hdr[1]];
			unset($cols[$hdr[1]]); // id is not needed anymore
			if(isset($qa[$l][$id])){
				echo ' dup ['.$id.']';
				continue;
			}
			$qa[$l][$id]=$cols;
		}
		echo "\n";
	}//lang
	if(empty($qa)) continue;

	foreach($qa as $l=>$data){
		file_put_contents(RYZOM_API_PATH.'include/words_'.$file.'_'.$l.'.serial', serialize($data));
	}
/*
	$export = var_export($qa, true);
$php=<<<EOF
<?
  \$words_{$file} = $export;
  // also use return so it's possible to do \$arr=include('file');
  return \$words_{$file};
EOF;
	file_put_contents(RYZOM_API_PATH.'include/words_'.$file.'.serial', $php);
*/
	echo "\n";
}

// http://www.moddular.org/log/utf16-to-utf8
// string needs utf BOM at the start
function utf16_to_utf8($str) {
    $c0 = ord($str[0]);
    $c1 = ord($str[1]);

    if ($c0 == 0xFE && $c1 == 0xFF) {
        $be = true;
    } else if ($c0 == 0xFF && $c1 == 0xFE) {
        $be = false;
    } else {
        return $str;
    }

    $str = substr($str, 2);
    $len = strlen($str);
    $dec = '';
    for ($i = 0; $i < $len; $i += 2) {
        $c = ($be) ? ord($str[$i]) << 8 | ord($str[$i + 1]) : 
                ord($str[$i + 1]) << 8 | ord($str[$i]);
        if ($c >= 0x0001 && $c <= 0x007F) {
            $dec .= chr($c);
        } else if ($c > 0x07FF) {
            $dec .= chr(0xE0 | (($c >> 12) & 0x0F));
            $dec .= chr(0x80 | (($c >>  6) & 0x3F));
            $dec .= chr(0x80 | (($c >>  0) & 0x3F));
        } else {
            $dec .= chr(0xC0 | (($c >>  6) & 0x1F));
            $dec .= chr(0x80 | (($c >>  0) & 0x3F));
        }
    }
    return $dec;
}
