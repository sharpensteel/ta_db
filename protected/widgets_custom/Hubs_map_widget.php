<?php


class Hubs_map_widget extends CWidget{
	public function run()
	{
		$hub_arr = World::get_hub_arr();
		
		foreach($hub_arr as &$hub){
			$hub['position_arr'] = array(1=>null, 2=>null, 3=>null, 4=>null, 5=>null, 6=>null,7=>null, 8=>null);
		}
		unset($hub);
		
		$player_arr = query_arr('select id, name, team, hub_name, hub_position, hub_name_planned, hub_position_planned from player where coalesce(hub_name,"")<>"" or coalesce(hub_name_planned,"")<>"" ');
		
		
		
		foreach($player_arr as $player){
			if($player['hub_name'].'' !=''){
				$hub = &$hub_arr[$player['hub_name']];
				$hub['position_arr'][$player['hub_position']] = $player;
				unset($hub);
			}
		}
		
		
		?>
		<style>
			.Hubs_map_widget .hub{
				background: #fff;
				border: 1px solid black;
				padding:10px 10px 10px 10px;
				font-size: 1.1em; 
				display: inline-block;
				min-width: 200px;
				margin-right: 30px;
				margin-bottom: 30px;
			}

			.Hubs_map_widget .hub_title{
				text-align: center;
				height: 30px; line-height: 30px; font-size: 1.2em; font-weight: bold;
				margin-bottom: 7px;
			}
			
			.Hubs_map_widget .hub_place{
				min-height: 28px;
			}
		</style>
		<div class="Hubs_map_widget">
		<?
		foreach($hub_arr as $hub_name => $hub){
			?>
			<div class="hub">
				<div class="hub_title"><?=$hub_name?></div>
				<?
				for($pos = 1; $pos<=8; $pos++){
					$player = array_default($hub['position_arr'],$pos,null);
					
					$player_text = "";
					if($player){
						$player_text = $player['name'];
						if($player['team'].'' != '') $player_text .= ' ('.$player['team'].')';
					}
					?><div class="hub_place"><?=$pos?>. <?=$player_text ?> </div>
					<?
				}
				?>
			</div>
			<?
		}
		?>
		</div>
		<?
	}
}
