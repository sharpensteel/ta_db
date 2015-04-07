<?php



class Report_ff_list_widget extends CWidget{
	
	public function run()
	{
		
		$sql_select_players = "SELECT p.id, p.name, p.alliance_id, p.rank, p.points, p.points_main_base, p.interested_in_ff_run, p.has_badge, p.has_sat_code,".
			" p.offense_level, unix_timestamp(p.dt_last_updated_ol) dt_last_updated_ol, p.substitution, p.alliance_original_str, p.hub_name, p.hub_position, p.fraction, a.name alliance_name".
			" FROM player p".
			" LEFT JOIN alliance a ON p.`alliance_id`=a.`id`".
			" WHERE a.`allways_full_update` OR p.`interested_in_ff_run` order by p.offense_level desc";
		
		
		$player_arr = query_arr($sql_select_players);
		
		?>
		<style>
			.warning{
				color:red;font-weght:bold;
			}
			
			
			.Report_ff_list_widget{
				border-collapse: collapse;
				border-spacing: 0;
			}
	
			.Report_ff_list_widget td, .Report_ff_list_widget th {
				border: 1px solid #397127;
				font-size: 1.2em;
				padding: 3px 7px 2px;
			}
			.Report_ff_list_widget th {
				background-color: #769B49;
				color: #fff;
				font-size: 1.4em;
				padding-bottom: 4px;
				padding-top: 5px;
				text-align: left;
			}
			.Report_ff_list_widget tr.alt td {
				background-color: #769B49;
				color: #000;
			}
		</style>
		<table class="Report_ff_list_widget">
			<thead>
				<tr>
					<th>Position</th>
					<th>Name</th>
					<th>OL</th>
					<th>OL Updated</th>
					<th>Scores (main base)</th>
					<th>Fraction</th>
					<th>Aliance origin</th>
					<th>Aliance current</th>
					<th>Need badge?</th>
					<th>Have badge?</th>
					<th>Substitution</th>
					<th>Sat. code</th>
					<th>On hub</th>
					<th>Team</th>
				</tr>
			</thead>

			<?
			
			$count_unbadged = 0;
			

			foreach($player_arr as $player){

				$is_unbadged = $player['interested_in_ff_run'] && !$player['has_badge'];
				if($is_unbadged) $count_unbadged++;
				?>
				<tr>
					<td><?=$is_unbadged ? $count_unbadged : '' ?></td>
					<td><?=$player['name']?></td>
					<td><?=$player['offense_level']?></td>
					<td><?=$player['dt_last_updated_ol'] ? date('Y-n-d',$player['dt_last_updated_ol']) : ''?></td>
					<td><?=$this->format_points($player['points'])." (".$this->format_points($player['points_main_base']).")"?></td>
					<td><?= ($player['fraction']==1)?'GDI':'NOD' ?></td>
					<td><?=$player['alliance_original_str']?></td>
					<td><?=$player['alliance_name']?></td>
					<td><?=($player['interested_in_ff_run'] && !$player['has_badge'])?'YES':'NO' ?></td>
					<td><?=$player['has_badge']?'YES':'NO' ?></td>
					<td><?=$player['substitution']?></td>
					<td><?=$player['has_sat_code'] ? '' : '<span class="warning">NO</span>' ?></td>
					<td><?=($player['hub_name'].''!='')?$player['hub_name'].':'.$player['hub_position'] :''?></td>
					<td></td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
	
	public function format_points($val){
		if($val>1000000)
			return ((int)($val/1000000))."M";
		if($val>1000)
			return ((int)($val/1000))."k";
		return $val;
	}
}