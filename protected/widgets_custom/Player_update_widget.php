<?


class Player_update_widget extends CWidget{
	
	public function run()
	{
		
		//player_update_history
		$player_arr = query_arr('select p.id, p.name, p.offense_level, p.offense_level_secondary, p.substitution, p.hits_avaliable from player p left join alliance a on a.id=p.alliance_id where a.interested order by name');
		
		$default_id = (int)array_default($_COOKIE,'up_id',array_default($_REQUEST,'up_id',0));
		
		
		?>
		<script>
			
			
			var Player_update_widget = {
				$elem: undefined,
				
				player_arr: undefined,
				player_arr_indexed: undefined,
				
				
				create: function(params){
					var _ = $.extend({}, Player_update_widget, params);
					
					_.$elem = $(".Player_update_widget");
					
					_.player_arr = JSON.parse("<?=  addslashes(json_encode($player_arr))?>"),
						
					_.player_arr_indexed = {};
					for(var i=0; i<_.player_arr.length; i++){
						var player = _.player_arr[i];
						_.player_arr_indexed[ player['id'] ] = player;
					}
					
					var $select = _.$elem.find(".select_player_id");
				

					var options_str = "";
					for(var i=0; i<_.player_arr.length; i++){					
						var player = _.player_arr[i];
						_.player_arr_indexed[ player['id'] ] = player;

						var is_selected = (parseInt(player['id']) === parseInt("<?=$default_id?>"));
						var selected_str = is_selected ? 'selected' : '';
						options_str += "<option value='"+player['id']+"'  "+selected_str+" >"+player['name']+"</option>";
					}

				
					options_str = "<option value=''>select...</option>" + options_str;
					$select.html(options_str);
					
					$select.change(function(event){
						_.player_select_apply(_);
					});
					
					_.player_select_apply(_);
					
					
					return _;
				},
				
				
				player_select_apply: function(_){
					var player = _.player_arr_indexed[_.$elem.find('.select_player_id').val()];
					
					_.$elem.find('.input_offense_level').val( player ? player['offense_level'] : '');
					_.$elem.find('.input_substitution').val( player ? player['substitution'] : '');
					_.$elem.find('.input_offense_level_secondary').val( player ? player['offense_level_secondary'] : '');
					_.$elem.find('.input_hits_avaliable').val( player ? player['hits_avaliable'] : '');
				}
			}
			
			
			$(function(){
				
				var widget = Player_update_widget.create();
				
			});
		</script>
		<form class="Player_update_widget" action="<?=baseUrl()?>site/Player_update" style="padding:10px;">
			<div>
				
				<div style="margin-bottom:10px">
					<div style="display:inline-block;width:190px">Your name: </div>
					<select  class="select_player_id" name="Player_form[id]" style="width:204px">
						<option value="">loading...</option>
					</select>
				</div>
				
				<div style="margin-bottom:10px">
					<div style="display:inline-block;width:190px">Main offense level:</div>
					<input class="input_offense_level" name="Player_form[offense_level]" type="text" style="width:200px" value=""><br>
				</div>
				<div style="margin-bottom:10px">
					<div style="display:inline-block;width:190px">Secondary offense level:</div>
					<input class="input_offense_level_secondary" name="Player_form[offense_level_secondary]" type="text" style="width:200px" value=""><br>
				</div>
				<div style="margin-bottom:10px">
					<div style="display:inline-block;width:190px">Substitution:</div>
					<input class="input_substitution" name="Player_form[substitution]" type="text" style="width:200px;" value=""><br>
				</div>
				<div style="margin-bottom:10px">
					<div style="display:inline-block;width:190px">Hits avaliable (main base):</div>
					<select  class="input_hits_avaliable" name="Player_form[hits_avaliable]" style="width:204px">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3 or more">3 or more</option>
					</select>
				</div>
				
				<div style="text-align:center;margin-top:20px;">
					<input type="submit" value="Update" style="width:120px;">
				</div>
			</div>
			
		
		</form>
		<?
	}

}