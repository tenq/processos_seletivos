<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "resultados".
 *
 * @property integer $id
 * @property string $resultado
 * @property integer $processo_id
 *
 * @property Processo $processo
 */
class Resultados extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resultados';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['processo_id'], 'required'],
            [['processo_id'], 'integer'],
            [['resultado'], 'string', 'max' => 145]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resultado' => 'Resultado',
            'processo_id' => 'Processo ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesso()
    {
        return $this->hasOne(Processo::className(), ['id' => 'processo_id']);
    }
}
