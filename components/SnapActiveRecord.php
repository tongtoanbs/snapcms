<?php
/**
 * @author Francis Beresford
 */
class SnapActiveRecord extends CActiveRecord 
{
	const dateTimeFormat = 'yyyy-MM-dd hh:mm:ss';
	const dateFormat = 'yyyy-MM-dd';
	const timeFormat = 'hh:mm:ss';
	
	public $Search = array();
	
	/**
	 * Prepare logging fields such as: created, updated, created_user_id, updated_user_id
	 * done before performing validation 
	 */
	protected function beforeValidate() 
	{
                $user_id = isset(Yii::app()->user) ? Yii::app()->user->id : null;
		if( $this->isNewRecord ) 
		{
			// is a new record
			if( self::hasAttribute('created') )
				$this->created = date('Y-m-d H:i:s');
			if( self::hasAttribute('updated') )
				//$this->updated = new CDbExpression('NOW()');
				$this->updated = date('Y-m-d H:i:s');
			if( self::hasAttribute('created_by') )
				$this->created_by = $user_id;
			if( self::hasAttribute('updated_by') )
				$this->updated_by = $user_id;
		}
		else {
			// we are updating an existing one
			if( self::hasAttribute('updated') )
				$this->updated = date('Y-m-d H:i:s');
				//$this->updated = new CDbExpression('NOW()');
			if( self::hasAttribute('updated_by') )
				$this->updated_by = $user_id;			
		}

		foreach($this->attributes as $attribute=>$value) 
		{
			foreach($this->getValidators($attribute) as $validator)
			{
				$attrHour = $attribute.'_hour';
				$attrMinute = $attribute.'_minute';
				if(
					$validator instanceof CDateValidator &&
					isset($this->$attrHour) &&
					isset($this->$attrMinute))
				{
					if($validator->format == self::dateTimeFormat) {
						$datetimeParts = explode(' ',$this->$attribute);
						$this->$attribute = 
							$datetimeParts[0] . ' ' . 
							str_pad($this->$attrHour,2,"0",STR_PAD_LEFT) . ':' . 
							str_pad($this->$attrMinute,2,"0",STR_PAD_LEFT) . ':00';
					}
					if($validator->format == self::timeFormat) {
						$this->$attribute = 
							str_pad($this->$attrHour,2,"0",STR_PAD_LEFT) . ':' . 
							str_pad($this->$attrMinute,2,"0",STR_PAD_LEFT) . ':00';
					} 
				}
			}
		}

		return parent::beforeValidate();
	}
	
	public function afterFind()
	{
		foreach($this->attributes as $attribute=>$value) 
		{
			foreach($this->getValidators($attribute) as $validator)
			{
				if($validator instanceof CDateValidator)
				{
					$attrHour = $attribute.'_hour';
					$attrMinute = $attribute.'_minute';
					if(!$this->$attribute) {
						$timeParts = array(0,0);
					} else if($validator->format == self::dateTimeFormat) {
						$datetimeParts = explode(' ',$this->$attribute);
						$timeParts = explode(':',$datetimeParts[1]);
					} else if ($validator->format == self::timeFormat) {
						$timeParts = explode(':',$this->$attribute);
					}
					
					if($validator->format == self::dateTimeFormat || $validator->format == self::timeFormat)
					{
						$this->$attrHour = $timeParts[0];
						$this->$attrMinute = $timeParts[1];
					}
				}
			}
		}
		parent::afterFind();
	}
	
	public function beforeSave()
	{
		/*
		foreach($this->attributes as $attribute=>$value) 
		{
			foreach($this->getValidators($attribute) as $validator)
			{
				if($validator instanceof CDateValidator && $validator->allowEmpty)
				{
					$attrSet = $attribute.'_set';
					if(!$this->$attrSet) {
						$this->$attribute = null;
					}
				}
			}
		}
		 */
		return parent::beforeSave();
	}

	/**
	 * Check if this model belongs to the the current user 
	 */	
	public function getBelongs_to_user()
	{
		$user = Yii::app()->user;
		return $user && !$user->isGuest && $user->id == $this->user_id;
	}
	
	public function __get($name)
	{
		if(strpos($name,'.')!==false) {
			$parts = explode('.',$name);
			return isset($this->Search[$parts[0]][$parts[1]]) ? $this->Search[$parts[0]][$parts[1]] : '';
		} 
		else
			return parent::__get($name);
	}
	
	public function __set($name,$value)
	{
		if($name === 'attributes') 
		{
			foreach($value as $key=>$val)
			{
				if(strpos($key,'.')!==false) 
				{
					$parts = explode('.',$key);
					$relation = $this->getActiveRelation($parts[0]);
					$className = $relation->className;
					$model = new $className();

					if($model->hasAttribute($parts[1]) || $model->hasProperty($parts[1])) {
						$this->Search[$parts[0]][$parts[1]] = $val;
					}
				}
			}
		} 
		parent::__set($name,$value);
	}
}