<?php

namespace app\models\etapasprocesso;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\etapasprocesso\EtapasProcesso;

/**
 * EtapasProcessoSearch represents the model behind the search form about `app\models\etapasprocesso\EtapasProcesso`.
 */
class EtapasProcessoSearch extends EtapasProcesso
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['etapa_id', 'processo_id'], 'integer'],
            [['etapa_cargo', 'etapa_data', 'etapa_atualizadopor', 'etapa_dataatualizacao', 'etapa_situacao'], 'safe'],
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
        $query = EtapasProcesso::find();

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
            'etapa_id' => $this->etapa_id,
            'processo_id' => $this->processo_id,
            'etapa_data' => $this->etapa_data,
            'etapa_dataatualizacao' => $this->etapa_dataatualizacao,
        ]);

        $query->andFilterWhere(['like', 'etapa_cargo', $this->etapa_cargo])
            ->andFilterWhere(['like', 'etapa_atualizadopor', $this->etapa_atualizadopor])
            ->andFilterWhere(['like', 'etapa_situacao', $this->etapa_situacao]);

        return $dataProvider;
    }
}