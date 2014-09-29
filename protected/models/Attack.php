<?php

/**
 * This is the model class for table "attack".
 *
 * The followings are the available columns in table 'attack':
 * @property string $id
 * @property integer $att_user_id
 * @property string $att_login
 * @property integer $att_base_id
 * @property string $att_base_name
 * @property integer $def_is_forgotten
 * @property string $def_login
 * @property integer $def_base_level
 * @property integer $def_base_id
 * @property string $def_base_name
 * @property string $def_alliance_name
 * @property integer $outcome_id
 * @property integer $outcome_text
 * @property string $dt
 * @property string $coords
 * @property string $attack_log_json
 * @property integer $attack_log_is_parsed
 * @property integer $report_id
 * @property string $report_json
 * @property integer $report_is_parsed
 */
class Attack extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'attack';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('att_user_id, att_base_id, def_is_forgotten, def_base_level, def_base_id, outcome_id, attack_log_is_parsed, report_id, report_is_parsed', 'numerical', 'integerOnly'=>true),
			array('id', 'length', 'max'=>20),
			array('att_login, att_base_name, def_login, def_base_name, def_alliance_name, outcome_text', 'length', 'max'=>50),
			array('coords', 'length', 'max'=>30),
			array('dt, attack_log_json, report_json', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, att_user_id, att_login, att_base_id, att_base_name, def_is_forgotten, def_login, def_base_level, def_base_id, def_base_name, def_alliance_name, outcome_id, outcome_text, dt, coords, attack_log_json, attack_log_is_parsed, report_id, report_json, report_is_parsed', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			//'att_user_id' => 'Att User',
			'att_login' => 'Attacker login',
			'att_base_id' => 'Attacker base',
			'att_base_name' => 'Attacker base',
			'def_is_forgotten' => 'Defender is Forgotten',
			'def_login' => 'Defender Login',
			'def_base_level' => 'Defender base level',
			'def_base_id' => 'Defender base',
			'def_base_name' => 'Defender base',
			'def_alliance_name' => 'Defender alliance',
			'outcome_id' => 'Outcome',
			'outcome_text' => 'Outcome text',
			'dt' => 'time',
			'coords' => 'Coords',
			'attack_log_json' => 'Attack log JSON',
			'attack_log_is_parsed' => 'Attack log is parsed',
			'report_id' => 'Report',
			'report_json' => 'Report JSON',
			'report_is_parsed' => 'Report is parsed',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('att_user_id',$this->att_user_id);
		$criteria->compare('att_login',$this->att_login,true);
		$criteria->compare('att_base_id',$this->att_base_id);
		$criteria->compare('att_base_name',$this->att_base_name,true);
		$criteria->compare('def_is_forgotten',$this->def_is_forgotten);
		$criteria->compare('def_login',$this->def_login,true);
		$criteria->compare('def_base_level',$this->def_base_level);
		$criteria->compare('def_base_id',$this->def_base_id);
		$criteria->compare('def_base_name',$this->def_base_name,true);
		$criteria->compare('def_alliance_name',$this->def_alliance_name,true);
		$criteria->compare('outcome_id',$this->outcome_id);
		$criteria->compare('outcome_text',$this->outcome_text);
		$criteria->compare('dt',$this->dt,true);
		$criteria->compare('coords',$this->coords,true);
		$criteria->compare('attack_log_json',$this->attack_log_json,true);
		$criteria->compare('attack_log_is_parsed',$this->attack_log_is_parsed);
		$criteria->compare('report_id',$this->report_id);
		$criteria->compare('report_json',$this->report_json,true);
		$criteria->compare('report_is_parsed',$this->report_is_parsed);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>25),
			'sort' => array(
				'defaultOrder' => array('id' => CSort::SORT_DESC),
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Attack the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
