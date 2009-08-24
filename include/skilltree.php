<?
class Skilltree {
    
	function getSkillTree(SimpleXMLElement $xml){
        $skills = array();
        // to be safe, sort the skills
        foreach($xml->skills->children() as $node){
			$name = (string) $node->getName();
			$level = (int) $node;
            $skills[$name]=$level;
        }
        ksort($skills);

        // build the tree
        $arr=array(); // temporary
        foreach($skills as $name => $level){
            if($level<=20) $max=20;
            elseif($level<=50) $max=50;
            elseif($level<=100)$max=100;
            elseif($level<=150)$max=150;
            elseif($level<=200)$max=200;
            else $max=250;
            $arr[$name]=array('level' => $level, 'max_level'=>$max, 'childs' => array(), 'is_root'=>true);
            // now find where it belongs to in skilltree
            for($i=strlen($name)-1;$i>0;$i--){
                $sub=substr($name, 0, $i);
                if(isset($arr[$sub])){
                    unset($arr[$name]['is_root']);
                    $arr[$sub]['childs'][$name]=&$arr[$name];
                    break;
                }
            }
		}
        // get the root nodes and 'sort' them in the right order,
        // sub-skills should be ordered correctly
        $tree = array(
            'sf' => $arr['sf'],
            'sm' => $arr['sm'],
            'sc' => $arr['sc'],
            'sh' => $arr['sh'],
        );
        return $tree;
	}
}//Skilltree

    $tree = SkillTree::getSkilltree($xml);
	$result = array();
	
$content.='<table cellspacing=0 cellspadding=0><tr>
           <td><a class="ryzom-ui-button" href="javascript: d.openAll();">'.$lang_openall.'</a></td>
		   <td><a class="ryzom-ui-button" href="javascript: d.closeAll();">'.$lang_closeall.'</a></td>
		   </tr></table>';	
$content.= '<div id="full" class="flexcroll"><div class="dtree"><div id="skillTree">';

$content.= '<script type="text/javascript">
		<!--

		d = new dTree("d");
        d.config.closeSameLevel=true;
        d.config.useIcons=false;
		d.add(0,-1,"");
		';

foreach(display_tree($tree) as $out) {
$content.= $out;
}

$content.= 'document.write(d);
		//-->
	    </script>';

$content.= '</div></div></div>';

function display_tree($childs){
   global $result,$id,$mem,$mem2,$mem3,$mem4,$mem5;
   foreach($childs as $key => $node){
       $txt=ryzom_translate($key.'.skill', $_GET[language]);
	   $id++;
       if($node['level'] <= 20){$n = 0;$mem = $id;}
	   elseif($node['level'] <= 50){$n = $mem;$mem2 = $id;}
	   elseif($node['level'] <= 100){$n = $mem2;$mem3 = $id;}
	   elseif($node['level'] <= 150){$n = $mem3;$mem4 = $id;}
	   elseif($node['level'] <= 200){$n = $mem4;$mem5 = $id;}
	   else{$n = $mem5;}
       // check if skill is finished, or is not
       if(empty($node['childs']) && $node['level']!=250){
           $class=" unfinished";
       }else{
           $class=' finished';
       }
       $result[] = 'd.add('.$id.','.$n.',"<span class=\"nodes'.$class.'\">'.
            '<span class=\"skill-level\">'.$node['level'].'/'.$node['max_level'].'</span>'.$txt.
            '</span>","");';
       if(!empty($node['childs'])){
           display_tree($node['childs']);
       }
   }
   return $result;
}
?>