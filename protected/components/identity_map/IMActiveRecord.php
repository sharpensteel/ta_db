<?php

class IMActiveRecord extends CActiveRecord
{
	/** @var integer when record is updated sonewhere else, this revision number shows that record need to be refreshed */
	private $_is_deleted = false;
	private $_is_refreshing_now = false;
	public function is_deleted(){ return $this->_is_deleted; }
	/** only for calling by Identity_map */
	public function _set_is_deleted($is_deleted){ $this->_is_deleted = $is_deleted; }
	
	
	/**
	* @param array $attributes list of attribute values for the active records.
	* @return CActiveRecord the active record
	*/
	public function instantiate($attributes)
	{
		if($this->_is_refreshing_now){
			if((string)$this->get_primary_key_from_attr($attributes) === (string)$this->primaryKey){
				$model = parent::instantiate($attributes);
			}
		}
		else{		
			$model = Identity_map::on_record_instantiated($this, $attributes);
		}
		
		return $model;		
	}
	
	public function call_parent_instantiate($attributes){
		return parent::instantiate($attributes);
	}
	
			
	protected function afterSave()
	{
		parent::afterSave();
		Identity_map::on_record_saved($this);
	}
	
	protected function afterDelete()
	{
		parent::afterDelete();
		Identity_map::on_record_deleted($this);
	}

		
	/**
	 * used in Identity_map::on_record_instantiated()
	 */
	public function get_primary_key_from_attr($attributes)
	{
		$table=$this->getTableSchema();
		if(is_string($table->primaryKey)){
			return $attributes[$table->primaryKey];
		}
		elseif(is_array($table->primaryKey))
		{
			$values=array();
			foreach($table->primaryKey as $name)
				$values[$name]=$attributes[$name];
			return $values;
		}
		else
			return null;
	}
	
	public function refresh()
	{
		// this need for instantiate() / findByPk(), to return new record, not from intance map;
		$this->_is_refreshing_now = true;
		
		$result = parent::refresh();
		
		$this->_is_refreshing_now = false;
		
		return $result;
	}
	
	/**
	 * Finds a single record with the specified primary key.
	 * Return record from Identity_map; if not exists, called CActiveRecord::findByPk()
	 * @param mixed $pk primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
	 * @param mixed $condition query condition or criteria.
	 * @param array $params parameters to be bound to an SQL statement.
	 * @return CActiveRecord the record found. Null if none is found.
	 */
	public function findByPk($pk,$condition='',$params=array())
	{
		//$table=$this->getTableSchema();
		
		$this_is_refreshing = false;
		
		if($this->_is_refreshing_now ){
			if((string)$pk === (string)$this->primaryKey)
				$this_is_refreshing = true;  // when refresh() called, reload record from DB and not take it from IM
		}

		if(!$this_is_refreshing)
		{			
			if(!is_array($pk) && !$condition && !$params){
				$record = Identity_map::get_record( get_class($this), $pk);
				if($record) return $record;
			}
		}
		$record = parent::findByPk($pk,$condition,$params);
		return $record;
	}
	
	
	
	/**
	 * @param string $name the relation name (see {@link relations})
	 * @param boolean $refresh whether to reload the related objects from database. Defaults to false.
	 * If the current record is not a new record and it does not have the related objects loaded they
	 * will be retrieved from the database even if this is set to false.
	 * If the current record is a new record and this value is false, the related objects will not be
	 * retrieved from the database.
	 * @param mixed $params array or CDbCriteria object with additional parameters that customize the query conditions as specified in the relation declaration.
	 * If this is supplied the related record(s) will be retrieved from the database regardless of the value or {@link $refresh}.
	 * The related record(s) retrieved when this is supplied will only be returned by this method and will not be loaded into the current record's relation.
	 * The value of the relation prior to running this method will still be available for the current record if this is supplied.
	 * @return mixed the related object(s).
	 */	
	public function getRelated($name,$refresh=false,$params=array())
	{
		if(!$params && !$refresh){
			if(!$this->hasRelated($name)){
				
				$md=$this->getMetaData();
				if(isset($md->relations[$name])){
					$relation = $md->relations[$name];
					if($relation instanceof CBelongsToRelation && is_string($relation->foreignKey))
					{
						$record = Identity_map::get_record($relation->className, $this->{$relation->foreignKey});
						if($record){
							$this->{$name} = $record;
							return $record;
						}
					}
				}
				
			}
		}
		/*	const BELONGS_TO='CBelongsToRelation';
		const HAS_ONE='CHasOneRelation';
		const HAS_MANY='CHasManyRelation';
		const MANY_MANY='CManyManyRelation';
		const STAT='CStatRelation';*/
		
		return parent::getRelated($name,$refresh,$params);
		
	}
}