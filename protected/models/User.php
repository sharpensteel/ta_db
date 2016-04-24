<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property integer $alliance_id
 * @property string $role
 * @property integer $rank
 * @property integer $score
 * @property integer $base_count
 * @property string $login_lowercase
 * @property integer $is_admin
 */
class User extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('alliance_id, rank, score, base_count, is_admin', 'numerical', 'integerOnly'=>true),
			array('login, password, login_lowercase', 'length', 'max'=>50),
			array('role', 'length', 'max'=>30),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, login, password, alliance_id, role, rank, score, base_count, login_lowercase, is_admin', 'safe', 'on'=>'search'),
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
			'login' => 'Login',
			'password' => 'Password',
			'alliance_id' => 'Alliance',
			'role' => 'Role',
			'rank' => 'Rank',
			'score' => 'Score',
			'base_count' => 'Base Count',
			'login_lowercase' => 'Login Lowercase',
			'is_admin' => 'Is Admin',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('login',$this->login,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('alliance_id',$this->alliance_id);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('rank',$this->rank);
		$criteria->compare('score',$this->score);
		$criteria->compare('base_count',$this->base_count);
		$criteria->compare('login_lowercase',$this->login_lowercase,true);
		$criteria->compare('is_admin',$this->is_admin);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
