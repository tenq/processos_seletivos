<?php

namespace app\models\etapasprocesso;

use Yii;

use app\models\processoseletivo\ProcessoSeletivo;

/**
 * This is the model class for table "etapas_processo".
 *
 * @property integer $etapa_id
 * @property integer $processo_id
 * @property string $etapa_cargo
 * @property string $etapa_datarealizacao
 * @property string $etapa_local
 * @property string $etapa_cidade
 * @property string $etapa_estado
 * @property string $etapa_selecionadores
 * @property string $etapa_data
 * @property string $etapa_atualizadopor
 * @property string $etapa_dataatualizacao
 * @property string $etapa_situacao
 *
 * @property EtapasItens[] $etapasItens
 * @property Processo $processo
 */
class EtapasProcesso extends \yii\db\ActiveRecord
{
    public $processoSeletivo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'etapas_processo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['etapa_selecionadores','etapa_local', 'etapa_cidade', 'etapa_estado', 'etapa_situacao'], 'required', 'on' => 'update'],
            [['processo_id', 'etapa_perfil'], 'integer'],
            [['etapa_data', 'etapa_dataatualizacao', 'etapa_selecionadores'], 'safe'],
            [['etapa_cargo', 'etapa_observacao'], 'string', 'max' => 255],
            [['etapa_datarealizacao', 'etapa_local', 'etapa_cidade', 'etapa_estado', 'etapa_atualizadopor', 'etapa_situacao'], 'string', 'max' => 45],
            [['processo_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProcessoSeletivo::className(), 'targetAttribute' => ['processo_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['etapa_selecionadores','etapa_local', 'etapa_cidade', 'etapa_estado', 'etapa_situacao'];//Scenario Values Only Accepted
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'etapa_id' => 'Cód',
            'processo_id' => 'Processo Seletivo',
            'etapa_cargo' => 'Cargo',
            'etapa_datarealizacao' => 'Data da Realização',
            'etapa_local' => 'Local',
            'etapa_cidade' => 'Cidade',
            'etapa_estado' => 'Estado',
            'etapa_selecionadores' => 'Nome dos Selecionadores',
            'etapa_data' => 'Data da Criação',
            'etapa_atualizadopor' => 'Atualizado Por',
            'etapa_dataatualizacao' => 'Data Atualização',
            'etapa_situacao' => 'Situação',
            'etapa_observacao' => 'Observação',
            'etapa_perfil' => 'Perfil das Etapas',
        ];
    }

    //Localiza os cargos vinculado ao Processo Seletivo
    public static function getCandidatosAprovadosSubCat($cat_id) {

        $sql = 'SELECT
                    `curriculos`.`id` AS id,
                    `curriculos`.`nome` AS name
                FROM 
                    `curriculos`
                INNER JOIN 
                    `etapas_itens` ON  `etapas_itens`.`curriculos_id` = `curriculos`.`id`
                INNER JOIN 
                    `etapas_processo` ON `etapas_processo`.`etapa_id` = `etapas_itens`.`etapasprocesso_id`
                INNER JOIN 
                    `processo` ON `processo`.`id` = `etapas_processo`.`processo_id`
                WHERE
                    `etapas_itens`.`itens_classificacao` = "1º colocado(a)"
                AND
                    `processo`.`id` = '.$cat_id.'';

        $data = \app\models\curriculos\Curriculos::findBySql($sql)->asArray()->all();

        return $data;
   }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtapasItens()
    {
        return $this->hasMany(EtapasItens::className(), ['etapasprocesso_id' => 'etapa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesso()
    {
        return $this->hasOne(ProcessoSeletivo::className(), ['id' => 'processo_id']);
    }
}
