<?php

/**
 * This is the model class for table "tbl_user".
 *
 * The followings are the available columns in table 'tbl_user':
 * @property integer $id
 * @property string $name
 * @property string $login
 * @property string $password
 * @property boolean $is_admin Description
 * @property string $email
 *
 * The followings are the available model relations:
 * @property Project[] $projects
 */
class User extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, login, password', 'required'),
			array('name, login, password,email', 'length', 'max'=>255),
            array('is_admin', 'boolean'),
			array('login,email', 'unique'),
			array('email', 'email'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, login, password', 'safe', 'on'=>'search'),
		);
	}
        
        /**
         * 
         * @return string
    */
    public function isAdminStr() {
            return $this->is_admin == 1 ? Yii::t('main', 'Yes') : Yii::t('main', 'No');
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'projects' => array(self::HAS_MANY, 'Project', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('main', 'ID'),
			'name' => Yii::t('main','User name'),
			'login' => Yii::t('main','User login'),
			'password' => Yii::t('main','User password'),
            'is_admin'=>Yii::t('main','User is admin'),
			'email'=>Yii::t('main','User email'),
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('login',$this->login,true);
		$criteria->compare('password',$this->password,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	*
	*/
	public function getViewName() {
		return empty($this->name) ? $this->login : $this->name;
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
	
	protected function beforeSave() {
		if($this->isNewRecord) {
			$this->password = self::passwordHash($this->password);
		}
		return parent::beforeSave();
	}
        
        /**
         * 
         * @param type $string
         * @return type
         */
        public static function passwordHash($string) {
            return md5($string);
        }
}
