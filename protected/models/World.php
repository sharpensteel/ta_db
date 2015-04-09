<?php


class World {
	
	static public function get_hub_arr(){
		$hub_arr = array(
			'Alpha' => array('x'=>550,'y'=>527),
			'Beta' => array('x'=>567,'y'=>537),
			'Gamma' => array('x'=>570,'y'=>553),
			'Delta' => array('x'=>556,'y'=>568),
			'Epsilon' => array('x'=>541,'y'=>570),
			'Zeta' => array('x'=>528,'y'=>553),
			'Eta' => array('x'=>538,'y'=>540),
		);
		foreach($hub_arr as &$hub){
			$x = $hub['x'];
			$y = $hub['y'];
			
			$hub['position_arr'] = array(
				1 => ($x-2).":".($y-2), 2=> ($x).":".($y-2), 3=>($x+2).":".($y-2),
				4=>($x-2).":".($y),                    5=>($x+2).":".($y),
				6=>($x-2).":".($y+2), 7=>($x).":".($y+2), 8=>($x+2).":".($y+2),
			);
			$hub['position_arr'] = array_flip($hub['position_arr']);
			$x=$x;
		}
		unset($hub);
		return $hub_arr;
	}
	
}
