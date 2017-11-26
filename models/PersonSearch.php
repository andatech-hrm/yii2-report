<?php

namespace andahrm\report\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use andahrm\person\models\Person;

/**
 * PersonSearch represents the model behind the search form about `andahrm\person\models\Person`.
 */
class PersonSearch extends \andahrm\person\models\PersonSearch
{
    public $fullname;
    public $position_type_id;
    public $person_type_id;
    public $religion_id;
    public $year;
    public $section_id;
    
    public $start_age;
    public $end_age;
    #edu
    public $level_id;
    
    
    const NO_DEGREE = '-1';
    const NO_GENDER = '-1';
    const NO_BIRTHDAY = '-1';
    const NO_SELECT_POSITION = '-1';
    const NO_SELECT_POSITION_TYPE = '-2';
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title_id', 'created_at', 'created_by', 'updated_at', 'updated_by','person_type_id','year',
            'religion_id','section_id','start_age','end_age','position_type_id','level_id'], 'integer'],
            [['citizen_id', 'firstname_th', 'lastname_th', 'firstname_en', 'lastname_en', 'gender', 'tel', 'phone', 'birthday', 'fullname', 'full_address_contact'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Person::find();
        //$query->joinWith(['addressContact.tambol', 'addressContact.amphur', 'addressContact.province']);
       

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes['full_address_contact'] = [
            'asc' => ['local_province.name' => SORT_ASC],
            'desc' => ['local_province.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        //echo $this->position_type_id;
        
        if(isset($this->position_type_id) && 
        $this->position_type_id==self::NO_SELECT_POSITION 
        || $this->section_id==self::NO_SELECT_POSITION 
        || $this->person_type_id==self::NO_SELECT_POSITION 
        ){
            //echo "3";
            $query->andWhere('position_id IS NULL');
        }elseif(isset($this->position_type_id) && $this->position_type_id==self::NO_SELECT_POSITION_TYPE){
            //echo "2";
            $query->joinWith(['position'],true,"INNER JOIN");
            $query->andWhere('position.position_type_id IS NULL');
        }elseif(isset($this->person_type_id) || isset($this->section_id) || isset($this->position_type_id)){
            //echo "1";
            $query->joinWith(['position'],true,"INNER JOIN");
            $query->andFilterWhere(['position.person_type_id'=>$this->person_type_id]);
            $query->andFilterWhere(['position.section_id'=>$this->section_id]);
            $query->andFilterWhere(['position.position_type_id'=>$this->position_type_id]);
            
        }
        
        
        
        
        if(isset($this->start_age) && isset($this->end_age) && $this->start_age==0 && $this->end_age == 0){
             $query->andWhere('birthday IS NULL');
        }elseif($this->start_age || $this->end_age){
             $query->andFilterWhere([">=","timestampdiff(YEAR,birthday,NOW())",$this->start_age]);
             $query->andFilterWhere(["<=","timestampdiff(YEAR,birthday,NOW())",$this->end_age]);
        }
        
        if($this->religion_id){
             $query->joinWith(['detail']);
             $query->andFilterWhere(['person_detail.religion_id'=>$this->religion_id]);
        }
        
        
        if($this->level_id == self::NO_DEGREE){
             $query->joinWith(['detail']);
             $query->andWhere('person_detail.person_education_id IS NULL');
        }elseif($this->level_id){
             $query->joinWith(['detail.education']);
             $query->andFilterWhere(['person_education.level_id'=>$this->level_id]);
        }
        
        if($this->year){
             $dateBetween = \andahrm\structure\models\FiscalYear::getDateBetween($this->year);
             $query->joinWith(['positionSalary']);
             $query->andFilterWhere(['<=', 'DATE(person_position_salary.adjust_date)', $dateBetween->date_end])
                ->andFilterWhere(['>=', 'DATE(person_position_salary.adjust_date)', $dateBetween->date_start]);
        }
        
        if($this->gender == self::NO_GENDER){
             $query->andWhere('gender IS NULL');
        }else{
            $query->andFilterWhere(['like', 'gender', $this->gender]);
        }
        

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'title_id' => $this->title_id,
            'birthday' => $this->birthday,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'citizen_id', $this->citizen_id])
            ->andFilterWhere(['like', 'firstname_th', $this->firstname_th])
            ->andFilterWhere(['like', 'lastname_th', $this->lastname_th])
            ->andFilterWhere(['like', 'firstname_en', $this->firstname_en])
            ->andFilterWhere(['like', 'lastname_en', $this->lastname_en])
            
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'phone', $this->phone]);
        
        $query->andFilterWhere(['like', 'firstname_th', $this->fullname])
            ->orFilterWhere(['like', 'lastname_th', $this->fullname])
            ->orFilterWhere(['like', 'firstname_en', $this->fullname])
            ->orFilterWhere(['like', 'lastname_en', $this->fullname]);
            
        $query->andFilterWhere(['like', 'person_address.number', $this->full_address_contact])
            ->orFilterWhere(['like', 'person_address.sub_road', $this->full_address_contact])
            ->orFilterWhere(['like', 'person_address.road', $this->full_address_contact])
            ->orFilterWhere(['like', 'person_address.postcode', $this->full_address_contact])
            ->orFilterWhere(['like', 'person_address.phone', $this->full_address_contact])
            ->orFilterWhere(['like', 'person_address.fax', $this->full_address_contact])
            ->orFilterWhere(['like', 'local_tambol.name', $this->full_address_contact])
            ->orFilterWhere(['like', 'local_amphur.name', $this->full_address_contact])
            ->orFilterWhere(['like', 'local_province.name', $this->full_address_contact]);

        return $dataProvider;
    }
}
