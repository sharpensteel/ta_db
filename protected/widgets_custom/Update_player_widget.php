<?


class Update_player_widget extends CWidget{
	
	public function run()
	{
		
		//player_update_history
		$player_arr = query_arr('select p.id, p.name from player p left join alliance a on a.id=p.alliance_id where a.interested order by name');
		
		$default_id = (int)array_default($_COOKIE,'up_id',array_default($_REQUEST,'up_id',0));
		$default_ol = array_default($_COOKIE,'up_ol','');
		
		
		?>
		<form action="<?=baseUrl()?>site/Player_update" style="padding:10px;">
			<div>
				
				<div style="margin-bottom:10px">
					<div style="display:inline-block;width:140px">Your name: </div>
					<select name="Player_form[id]" style="width:200px">
						<option value="">select...</option>
						<?
						foreach($player_arr as $player){
							?><option value="<?=$player['id']?>"  <?=((int)$player['id']===$default_id ?'selected':'') ?> ><?=$player['name']?> </option>
							<?
						}
						?>
					</select>
				</div>
				
				<div style="margin-bottom:10px">
					<div style="display:inline-block;width:140px">Your offense level:</div>
					<input name="Player_form[offense_level]" type="text" style="width:50px" value="<?=$default_ol?>"><br>
				</div>
				<div style="margin-bottom:10px">
					<div style="display:inline-block;width:140px">Substitution:</div>
					<input name="Player_form[substitution]" type="text" style="width:200px;" value=""><br>
				</div>
				
				<div style="text-align:center;margin-top:20px;">
					<input type="submit" value="Update" style="width:120px;">
				</div>
			</div>
			
		
		</form>
		<?
	}

}