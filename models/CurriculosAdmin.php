<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yiibr\brvalidator\CpfValidator;
use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CeiValidator;

/**
 * This is the model class for table "curriculos".
 *
 * @property integer $id
 * @property string $edital
 * @property integer $cargo
 * @property string $nome
 * @property string $cpf
 * @property string $datanascimento
 * @property string $sexo
 * @property string $email
 * @property string $emailAlt
 * @property string $telefone
 * @property string $telefoneAlt
 * @property string $data
 *
 * @property CurriculosCurriculosComplementos[] $curriculosCurriculosComplementos
 * @property CurriculosCurriculosEmpregos[] $curriculosCurriculosEmpregos
 * @property CurriculosDocumentacao[] $curriculosDocumentacaos
 * @property CurriculosEndereco[] $curriculosEnderecos
 * @property CurriculosFormacao[] $curriculosFormacaos
 */
class CurriculosAdmin extends \yii\db\ActiveRecord
{
    public $idadeModel;
    public $termoAceite;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'curriculos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['edital', 'numeroInscricao','cargo', 'nome', 'cpf', 'datanascimento', 'sexo', 'email', 'telefone', 'data', 'termoAceite'], 'required'],
            // ['cpf', 'unique', 'targetAttribute' => ['edital', 'cpf', 'cargo'],'message' => '"{value} Já utilizado para o edital e cargo selecionado"'],
            // ['cpf', CpfValidator::className()],
            [['idade'], 'integer'],
            [['datanascimento', 'data' , 'idadeModel', 'classificado'], 'safe'],
            [['edital', 'numeroInscricao', 'identidade', 'orgao_exped'], 'string', 'max' => 45],
            [['nome', 'cargo', 'email', 'emailAlt'], 'string', 'max' => 100],
            [['email', 'emailAlt'], 'email'],
            [['cpf', 'sexo', 'telefone', 'telefoneAlt'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'edital' => 'Edital',
            'numeroInscricao' => 'Inscrição',
            'cargo' => 'Cargo',
            'nome' => 'Nome',
            'cpf' => 'CPF',
            'identidade' => 'RG',
            'orgao_exped' => 'Orgão Expedidor',
            'datanascimento' => 'Data de Nascimento',
            'idade' => 'Idade',
            'sexo' => 'Sexo',
            'email' => 'Email',
            'emailAlt' => 'Email Alternativo',
            'telefone' => 'Telefone',
            'telefoneAlt' => 'Telefone Alternativo',
            'data' => 'Data/Hora da Inscrição',
            'classificado' => 'Situação',
            'termoAceite' => 'Termo de aceite',
        ];
    }

    public function getCargosProcesso() //Relation between Cargos & Processo table
    {
        return $this->hasMany(CargosProcesso::className(), ['processo_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurriculosCurriculosComplementos()
    {
        return $this->hasMany(CurriculosCurriculosComplementos::className(), ['curriculos_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurriculosCurriculosEmpregos()
    {
        return $this->hasMany(CurriculosCurriculosEmpregos::className(), ['curriculos_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurriculosDocumentacaos()
    {
        return $this->hasMany(CurriculosDocumentacao::className(), ['curriculos_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurriculosEnderecos()
    {
        return $this->hasMany(CurriculosEndereco::className(), ['curriculos_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurriculosFormacaos()
    {
        return $this->hasMany(CurriculosFormacao::className(), ['curriculos_id' => 'id']);
    }
}