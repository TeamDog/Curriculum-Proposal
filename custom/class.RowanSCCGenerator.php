<?php

class RowanSCCGenerator {
	public static function createSCC($id,$college, $dms) {

        $scc = "";
        
        $month = date('n');
        $year = date('y');
        
        if($month >= 6) {
            $scc = $year . "-" . ++$year . "-";
        }
        else{
            $scc = --$year . "-" . ++$year . "-";
        }
		
		
        
        switch($college) {
				case "College of Business":
					$scc = $scc. "1." . $id;
					break;
                case "College of Communication and Creative Arts":
					$scc = $scc. "2.". $id;
					break;
                case "College of Education":
					$scc = $scc. "3.". $id;
					break;
                case "College of Engineering":
					$scc = $scc. "4.". $id;
					break;
                case "College of Fine and Performing Arts":
					$scc = $scc. "5.". $id;
					break;
                case "College of HSS Humanities":
					$scc = $scc. "6.". $id;
					break;
                case "College of HSS Social Science":
					$scc = $scc. "7.". $id;
					break;
                case "College of Science and Mathematics":
					$scc = $scc. "8.". $id;
					break;
                case "Cooper Medical School of Rowan University":
					$scc = $scc. "9.". $id;
					break;
                case "College of Earth and Environment":
					$scc = $scc. "10.". $id;
					break;
                case "School of Health Professions":
					$scc = $scc. "11.". $id;
					break;
                case "Campbell Library":
					$scc = $scc. "12.". $id;
					break;
				default:
                    #Unable to be match
					$scc = $scc. "13.". $id;
					break;
        }
        
        return $scc;
    }
}

?>