<?php

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\user\models\User;
use app\helpers\DateHelper;

/**
 * UsersSearch represents the model behind the search form about `app\models\Users`.
 * 
 * @author Stableflow
 * 
 */
class UsersSearch extends User {

    public $dateFrom;
    public $dateTo;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'status', 'role'], 'integer'],
            [['email', 'dateFrom', 'dateTo', 'username', 'referral_code'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = User::find();

        $query->andWhere('id != :id', [':id' => Yii::$app->user->id]);
        $query->andWhere('status != :status', [':status' => User::STATUS_TEMP]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'role' => $this->role,
        ]);


        if (!empty($this->dateFrom)) {
            $query->andWhere('create_date >= :dateFrom', [':dateFrom' => DateHelper::getGTMDatetime($this->dateFrom, Yii::$app->formatter->timeZone)]);
        }

        if (!empty($this->dateTo)) {
            $query->andWhere('create_date <= :dateTo', [':dateTo' => DateHelper::getGTMDatetime($this->dateTo, Yii::$app->formatter->timeZone)]);
        }

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'referral_code', $this->referral_code]);

        return $dataProvider;
    }
    

}
