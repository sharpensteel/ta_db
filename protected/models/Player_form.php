<?php


class Player_form {
	
	public $id;
	public $offense_level;
	public $substitution;
	public $offense_level_secondary;
	public $hits_avaliable;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// name, email, subject and body are required
			array('id, offense_level,substitution', 'required'),
			array('offense_level_secondary,hits_avaliable','safe'),
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
			'insert into player_update_history (ip,player_id,offense_level,substitution, offense_level_secondary, hits_avaliable) values (:ip,:player_id,:offense_level,:substitution, :offense_level_secondary, :hits_avaliable)',
			array("ip"=>$ip,'player_id'=>$this->id,'offense_level'=>$this->offense_level,'substitution'=>$this->substitution,'offense_level_secondary'=>$this->offense_level_secondary,'hits_avaliable'=>$this->hits_avaliable)
		);
		
		query_execute(
			'update player set offense_level=:offense_level, substitution=:substitution, interested_in_ff_run=1, offense_level_secondary=:offense_level_secondary, '.
			' hits_avaliable=:hits_avaliable, dt_last_updated_ol=current_timestamp where id=:player_id',
			array('player_id'=>$this->id,'offense_level'=>$this->offense_level,'substitution'=>$this->substitution,'offense_level_secondary'=>$this->offense_level_secondary,'hits_avaliable'=>$this->hits_avaliable)
		);
	}
}
