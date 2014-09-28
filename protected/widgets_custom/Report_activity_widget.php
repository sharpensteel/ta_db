<?php


class Report_activity_widget extends CWidget{
	public $interval_days = 30;	
	public $alliance_id; //101: vortex ares
	
	const DEFENDER_LEVEL_FACTOR = 1.2;
	const KILL_MULTIPLIER = 6;
	const ATTACK_MULTIPLIER = 1;
	
	public function run(){
		
		$attack_arr = query_arr(
			'SELECT att_login, def_base_level, SUM(IF(outcome_id IN (42,38),1,0)) AS kills,  COUNT(*) AS attacks FROM attack '.
			' WHERE outcome_id IN (43,34,42, 40,24,38)'.
			' AND dt > CURRENT_DATE - INTERVAL :interval_days DAY'.
			' GROUP BY 1, 2',
			array('interval_days' => $this->interval_days));
		
		
		$player_arr = make_array_indexed_by_records_field(query_arr('select id, name from player where alliance_id=:alliance_id order by name',array('alliance_id'=>$this->alliance_id)),'name');
		
		
		
		foreach($player_arr as &$player){
			$player['attack_arr'] = array();
			$player['score'] = 0;
		}
		unset($player);
		
		foreach($attack_arr as $attack){
			if(!isset($player_arr[$attack['att_login']])) continue;
			$player = &$player_arr[$attack['att_login']];
			$player['attack_arr'][(int)$attack['def_base_level']] = $attack;
			$lvl_modifier = pow(1.2, $attack['def_base_level']);
			$player['score'] += $lvl_modifier * ( (int)$attack['kills']*6 + (int)$attack['attacks'] );
			unset($player);
		}
		
		$player_arr_by_score = array_values($player_arr);
		usort($player_arr_by_score, function($a, $b){ return -($a['score'] - $b['score']); });
	
		$alliance_name = query_scalar('select name from alliance where id=:id', array('id'=>$this->alliance_id));
		if($alliance_name === false) $alliance_name = '???';
		
		?>
		Alliance: <?=$alliance_name?><br>
		<span style="color:gray;font-style:italic;"><? self::print_score_calc_description() ?></span><br><br>
		<?
		foreach($player_arr_by_score as $player){
			?><b><?=$player['name']?> </b><br> activity score = <?=round($player['score']/1000000,3)?> M
			<div style="padding-left:20px;">
				<?
				krsort($player['attack_arr']);
				
				foreach($player['attack_arr'] as $def_base_level => $attack){
					?><?=$def_base_level?>: <?=$attack['kills']?>  kills / <?=$attack['attacks']?> attacks<br><?
				}
				?>
			</div>
			<br>
			<?
		}
	}
	
	function print_score_calc_description(){
		echo "Scores increasing each base kill/attack. kill/attack score ratio: ".self::KILL_MULTIPLIER.":".self::ATTACK_MULTIPLIER.". Scores increased with each level by *".self::DEFENDER_LEVEL_FACTOR."<br> *** NOTE: this score does not account importance of targets!";
	}
}
