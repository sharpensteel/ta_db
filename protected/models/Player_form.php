<?php


class Player_form {
	
	public $id;
	public $offense_level;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// name, email, subject and body are required
			array('id, offense_level', 'required'),
			// verifyCode needs to be entered correctly
			//array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>"Player's id",
		);
	}
	
	public function update(){
		
		$ip = array_default($_SERVER,'REMOTE_ADDR');
		query_execute(
			'insert into player_update_history (ip,player_id,offense_level) values (:ip,:player_id,:offense_level)',
			array("ip"=>$ip,'player_id'=>$this->id,'offense_level'=>$this->offense_level)
		);
		
		query_execute(
			'update player set offense_level=:offense_level, interested_in_ff_run=:interested_in_ff_run, dt_last_updated_ol=current_timestamp where id=:player_id',
			array('player_id'=>$this->id,'offense_level'=>$this->offense_level)
		);
	}
}
