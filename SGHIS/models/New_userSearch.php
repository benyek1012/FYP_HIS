<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\New_user;

/**
 * NewuserSearch represents the model behind the search form of `app\models\Newuser`.
 */
class New_UserSearch extends New_user
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_uid', 'username', 'user_password', 'role_cashier', 'role_clerk', 'role_admin', 'role_guest_print', 'Case_Note','Registration','Charge_Sheet','Sticker_Label', 'authKey'], 'safe'],
            [['retire'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = New_user::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'retire' => $this->retire,
        ]);

        $query->andFilterWhere(['like', 'user_uid', $this->user_uid])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'user_password', $this->user_password])
            ->andFilterWhere(['like', 'role_cashier', $this->role_cashier])
            ->andFilterWhere(['like', 'role_clerk', $this->role_clerk])
            ->andFilterWhere(['like', 'role_admin', $this->role_admin])
            ->andFilterWhere(['like', 'Case_Note', $this->Case_Note])
            ->andFilterWhere(['like', 'Registration', $this->Registration])
            ->andFilterWhere(['like', 'Charge_Sheet', $this->Charge_Sheet])
            ->andFilterWhere(['like', 'Sticker_Label', $this->Sticker_Label])
            ->andFilterWhere(['like', 'authKey', $this->authKey]);

        return $dataProvider;
    }
}
