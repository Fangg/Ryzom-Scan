<?
	define('RYZOM_API_PATH', dirname(__FILE__).'/../');

	$lang=array('en','fr','de');

	$CR='-myEoL-';
	$data=array();
	foreach($lang as $l){
		$data[$l]=array();

		// load .uxt file
		$uxtFile='uxt_txt/'.$l.'.uxt';
		$uxtContent=file_get_contents($uxtFile);
		// skip UTF-8 BOM marker
		$uxtContent=substr($uxtContent, 3);
		// replace line-breaks with my own string
		$uxtContent=str_replace("\n", $CR, $uxtContent);
		// strip single and multi line comments out
		$uxtContent = preg_replace(array('/\/\/.*'.$CR.'/U', '/\/\*(.*)\*\//Um'), array(""), $uxtContent);
		// put line-breaks back
		$uxtContent = str_replace($CR, "\n", $uxtContent);
		$uxtLength=strlen($uxtContent);

		// join multi line values together (all lines between [ and ] symbols
		// FIXME: test if it breaks when those are found in text
		$matches = preg_match_all('/^(.*)\t\[([^]]+)\]/Um', $uxtContent, $uxtLines);
		if($matches>0){
			foreach($uxtLines[1] as $idx=>$key){
				$uxtLines[2][$idx] = str_replace("\r", "", $uxtLines[2][$idx]);
				$s=preg_replace(array("/\n\s+/Um"), array(""), $uxtLines[2][$idx]);
				$data[$l][strtolower($key)]=str_replace("\\n", "\n", $s);
			}
		}else{
			die('FATAL: no matches found');
		}

	}

	foreach($data as $l=>$uxt){
		file_put_contents(RYZOM_API_PATH.'include/uxt_'.$l.'.serial', serialize($uxt));
	}
	
