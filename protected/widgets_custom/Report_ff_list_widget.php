<?php



class Report_ff_list_widget extends CWidget{
	
	public function run()
	{
		$global_data_record_arr = query_arr("select * from global_data where id=1");
		$global_data_record = $global_data_record_arr[0];
		$number_of_medalists_for_ff = $global_data_record['number_of_medalists_for_ff'];
		$number_of_granted_by_cics = $global_data_record['number_of_granted_by_cics'];
		
		session_start_if_not();
		$is_admin = array_default($_SESSION,'is_admin',0);
		
		
		$sql_select_players = "SELECT p.id, p.name, p.alliance_id, p.rank, p.points, p.points_main_base, p.interested_in_ff_run, p.has_badge, p.has_sat_code,".
			" p.offense_level, unix_timestamp(p.dt_last_updated_ol) dt_last_updated_ol, p.substitution, p.alliance_original_str, p.hub_name, p.hub_position, p.fraction, a.name alliance_name,".
			" p.distance_to_ff_main, p.team, p.officer_comment, ".
			" p.`alliance_id_granted_by_cic`, p.`alliance_id_representative`, ".
			" if( COALESCE(aliance_granted_by_cic.abbreviation,'')<>'', aliance_granted_by_cic.abbreviation, aliance_granted_by_cic.name) aliance_granted_by_cic__name,".
			" if( COALESCE(alliance_representative.abbreviation,'')<>'', alliance_representative.abbreviation, alliance_representative.name) alliance_representative__name".
			" FROM player p".
			" LEFT JOIN alliance a ON p.`alliance_id`=a.`id`".
			" LEFT JOIN alliance aliance_granted_by_cic ON p.`alliance_id_granted_by_cic`=aliance_granted_by_cic.`id`".
			" LEFT JOIN alliance alliance_representative ON p.`alliance_id_representative`=alliance_representative.`id`".
			" WHERE a.`allways_full_update` OR p.`interested_in_ff_run` order by p.offense_level desc";
		
		
		$player_arr = query_arr($sql_select_players);
		
		$team_count_arr = query_arr("SELECT team, COUNT(*) count FROM player WHERE COALESCE(team,'')<>'' GROUP BY 1");
		
		?>
		<style>
			.warning{
				color:red;font-weght:bold;
			}
			
			
			.Report_ff_list_widget table{
				border-collapse: collapse;
				border-spacing: 0;
			}
	
			.Report_ff_list_widget td, .Report_ff_list_widget th {
				border: 1px solid #397127;
				font-size: 1.1em;
				padding: 3px 7px 2px;
			}
			.Report_ff_list_widget th {
				background-color: #769B49;
				color: #fff;
				font-size: 1.2em;
				padding-bottom: 4px;
				padding-top: 5px;
				text-align: left;
			}
			.Report_ff_list_widget tr.alt td {
				background-color: #769B49;
				color: #000;
			}
			.Report_ff_list_widget tr.has_badge{
				background:#F9F6E4;
			}
		
			.Report_ff_list_widget .team_input.not_saved,.Report_ff_list_widget .team_input.not_saved:focus{
				border-color: #0222FF;
			}
			
			.Report_ff_list_widget tr.fl_row_player td:nth-child(5) {
				font-size: 0.8em;
			}
			
			.Report_ff_list_widget .table_teams td:first-child{
				min-width: 100px;
			}
			
		</style>
		
		<?
		
		if($is_admin){
			?>
			<script>
				
				function ask_update_player_field($input){
					var player_id = $input.closest('.fl_row_player').attr('player_id');
					var field_name = $input.attr('field_name');
					var field_value = $input.val();
					//var field_value_htmlencoded = encodeURIComponent(field_value);
					$input.addClass('not_saved');
					
					$.ajax({
						url: '<?=baseUrl()?>site/Player_update_field',
						data: {player_id:player_id,field_name:field_name, field_value:field_value}
					}).done(function(data) {
						
						$input.data('saved_val', data);
						if($input.data('saved_val') === $input.val()){
							$input.removeClass('not_saved');
						}
						//console.log(data);
					});
				}
				
				
				$(function(){
					
					$('.Report_ff_list_widget .player_field_input').each(function() {
						var $elem = $(this);
						
						// Save current value of $element
						$elem.data('old_val', $elem.val());
						$elem.data('saved_val', $elem.val());

						// Look for changes in the value
						$elem.bind("propertychange change click keyup input paste", function(event){
						   // If value has changed...
						   if ($elem.data('old_val') != $elem.val()) {
							// Updated stored value
							$elem.data('old_val', $elem.val());

							// Do action
							
							ask_update_player_field($elem);
							
						  }
						});

					});
				});
			</script>
			<?
		}
		
		?>
		<div class="Report_ff_list_widget"> 
			<table class="">
				<thead>
					<tr>
						<th>Name</th>
						<th>OL</th>
						<th>OL Updated</th>
						<th style="min-width: 110px;">Group</th>
						<th style="font-size: 0.8em;">Group calculated</th>
						<th>Scores<br>total/main base</th>
						<th>Aliance origin</th>
						<th>Aliance current</th>
						<!--<th>Need badge?</th>-->
						<th>Have badge?</th>
						<th>Substitution</th>
						<th>Sat. code</th>
						<th>On hub</th>
						<th>Fraction</th>
						<th>Comment</th>
						<!--<th style="font-size: 1em;">Distance<br>main base to FF</th>-->
					</tr>
				</thead>


				<?

				$count_unbadged = 0;


				$position_unbadged = $number_of_granted_by_cics;
				$max_team = 50 - $number_of_medalists_for_ff;
				$max_unbadged = 50;


				function player_field_input_render($player, $is_admin, $field_name, $field_value = 'MAGIC_UNUSED_CONST_12312'){
					if($field_value === 'MAGIC_UNUSED_CONST_12312'){
						$field_value = $player[$field_name];
					}
					
					if($is_admin){
						?><input class="player_field_input" field_name="<?=$field_name?>" type="text" value="<?=$field_value?>"><?
					}
					else {
						echo $player[$field_name];	
					}
				}

				foreach($player_arr as $player){

					$status ='';

					$is_unbadged = !$player['has_badge'] && $player['interested_in_ff_run'];

					$is_help = 0;
					$is_team = 0;
					$is_swapin = 0;

					$is_granted = (int)$player["alliance_id_granted_by_cic"];

					if($is_granted){
						$is_team = 1;
						$status .= "TEAM granted by cic ".$player["aliance_granted_by_cic__name"]." ";
					}				


					if($is_unbadged){
						if(!$is_granted){
							$position_unbadged++;
							if($position_unbadged <= $max_team){
								$is_team = 1;
								$status .= "TEAM ".$position_unbadged." ";
							}
							else if($position_unbadged <= $max_unbadged){
								$is_swapin = 1;
								$status .= "SWAP-IN ".$position_unbadged." ";
							}
						}

					}

					if((int)$player["alliance_id_representative"]){
						$status .= "repres. officer ".$player["alliance_representative__name"]." ";
					}	

					$team = htmlentities($player['team']);

					$is_unbadged = $player['interested_in_ff_run'] && !$player['has_badge'];
					?>
					<tr class="fl_row_player <?=($player['has_badge'] ? 'has_badge' : '')?>" player_id="<?=($player['id'])?>" >
						<td><?=$player['name']?></td>
						<td><?=sprintf("%.2f",$player['offense_level'])?></td>
						<td><?=$player['dt_last_updated_ol'] ? date('Y-n-d',$player['dt_last_updated_ol']) : ''?></td>
						<td><? player_field_input_render($player, $is_admin, "team") ?></td>
						<td><?=$status?></td>
						<td><?=$this->format_points($player['points'])." /".$this->format_points($player['points_main_base']).""?></td>
						<td><? player_field_input_render($player, $is_admin, "alliance_original_str") ?></td>
						<td><?=$player['alliance_name']?></td>
						<!--<td><?=($player['interested_in_ff_run'] && !$player['has_badge'])?'YES':'NO' ?></td>-->
						<td><?=$player['has_badge']?'YES':'NO' ?></td>
						<td><? player_field_input_render($player, $is_admin, "substitution") ?></td>
						<td><?=$player['has_sat_code'] ? '' : '<span class="warning">NO</span>' ?></td>
						<td><?=($player['hub_name'].''!='')?$player['hub_name'].':'.$player['hub_position'] :''?></td>
						<td><?= ($player['fraction']==1)?'GDI':'NOD' ?></td>
						<td><? player_field_input_render($player, $is_admin, "officer_comment") ?></td>
						<!--<td><?=$player['distance_to_ff_main']?></td>-->
					</tr>
					<?


				}
				?>
			</table>

			<br><br>
			<b>Total players in groups:</b>
			<table class="table_teams" style="  margin-top: 10px;background:white;">
				<thead>
					<tr>
						<th>Group</th>
						<th>Players</th>
					</tr>
				</thead>
			<?	
			foreach($team_count_arr as $record){
				?>
				<tr>
					<td><?=$record['team']?></td>
					<td><?=$record['count']?></td>
				</tr>
				<?
			}
			?>
			</table>
			
		</div>
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