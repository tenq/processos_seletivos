<?php

namespace app\models\contratacao;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\contratacao\Contratacao;

/**
 * ContratacaoEncerradaSearch represents the model behind the search form about `app\models\Contratacao`.
 */
class ContratacaoEncerradaSearch extends Contratacao
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cod_colaborador', 'cod_unidade_solic', 'quant_pessoa', 'substituicao', 'periodo', 'tempo_periodo', 'aumento_quadro', 'deficiencia', 'fundamental_comp', 'fundamental_inc', 'medio_comp', 'medio_inc', 'tecnico_comp', 'tecnico_inc', 'superior_comp', 'superior_inc', 'pos_comp', 'pos_inc', 'windows', 'word', 'excel', 'internet', 'experiencia', 'jornada_horas', 'recrutamento_id', 'selec_curriculo', 'selec_dinamica', 'selec_prova', 'selec_entrevista'], 'integer'],
            [['data_solicitacao', 'hora_solicitacao', 'colaborador', 'cargo', 'unidade', 'motivo', 'obs_aumento', 'nome_substituicao', 'obs_deficiencia', 'data_ingresso_prevista', 'tecnico_area', 'superior_area', 'pos_area', 'dominio_atividade', 'experiencia_tempo', 'experiencia_atividade', 'jornada_obs', 'principais_atividades', 'selec_teste', 'situacao_id', 'cargo_id'], 'safe'],
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
        $query = Contratacao::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort = ['defaultOrder' => ['id'=>SORT_DESC]];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith(['situacao']);


        $query->andFilterWhere([
            'id' => $this->id,
            'data_solicitacao' => $this->data_solicitacao,
            'hora_solicitacao' => $this->hora_solicitacao,
            'cod_colaborador' => $this->cod_colaborador,
            'cod_unidade_solic' => $this->cod_unidade_solic,
            'quant_pessoa' => $this->quant_pessoa,
            'substituicao' => $this->substituicao,
            'periodo' => $this->periodo,
            'tempo_periodo' => $this->tempo_periodo,
            'aumento_quadro' => $this->aumento_quadro,
            'deficiencia' => $this->deficiencia,
            'fundamental_comp' => $this->fundamental_comp,
            'fundamental_inc' => $this->fundamental_inc,
            'medio_comp' => $this->medio_comp,
            'medio_inc' => $this->medio_inc,
            'tecnico_comp' => $this->tecnico_comp,
            'tecnico_inc' => $this->tecnico_inc,
            'superior_comp' => $this->superior_comp,
            'superior_inc' => $this->superior_inc,
            'pos_comp' => $this->pos_comp,
            'pos_inc' => $this->pos_inc,
            'windows' => $this->windows,
            'word' => $this->word,
            'excel' => $this->excel,
            'internet' => $this->internet,
            'experiencia' => $this->experiencia,
            'jornada_horas' => $this->jornada_horas,
            'recrutamento_id' => $this->recrutamento_id,
            'selec_curriculo' => $this->selec_curriculo,
            'selec_dinamica' => $this->selec_dinamica,
            'selec_prova' => $this->selec_prova,
            'selec_entrevista' => $this->selec_entrevista,
            'situacao_id' => [5, 6], // 5 - Finalizado / 6 - Cancelado
        ]);

        $query->joinWith('cargo0');

        $query->andFilterWhere(['like', 'situacao_contratacao.descricao', $this->situacao_id])
            ->andFilterWhere(['like', 'colaborador', $this->colaborador])
            ->andFilterWhere(['like', 'cargo', $this->cargo])
            ->andFilterWhere(['like', 'unidade', $this->unidade])
            ->andFilterWhere(['like', 'motivo', $this->motivo])
            ->andFilterWhere(['like', 'obs_aumento', $this->obs_aumento])
            ->andFilterWhere(['like', 'nome_substituicao', $this->nome_substituicao])
            ->andFilterWhere(['like', 'obs_deficiencia', $this->obs_deficiencia])
            ->andFilterWhere(['like', 'data_ingresso_prevista', $this->data_ingresso_prevista])
            ->andFilterWhere(['like', 'tecnico_area', $this->tecnico_area])
            ->andFilterWhere(['like', 'superior_area', $this->superior_area])
            ->andFilterWhere(['like', 'pos_area', $this->pos_area])
            ->andFilterWhere(['like', 'dominio_atividade', $this->dominio_atividade])
            ->andFilterWhere(['like', 'experiencia_tempo', $this->experiencia_tempo])
            ->andFilterWhere(['like', 'experiencia_atividade', $this->experiencia_atividade])
            ->andFilterWhere(['like', 'jornada_obs', $this->jornada_obs])
            ->andFilterWhere(['like', 'principais_atividades', $this->principais_atividades])
            ->andFilterWhere(['like', 'selec_teste', $this->selec_teste])
            ->andFilterWhere(['like', 'cargos.descricao', $this->cargo_id]);

        return $dataProvider;
    }
}
