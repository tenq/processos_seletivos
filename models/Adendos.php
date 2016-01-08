<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "adendos".
 *
 * @property integer $id
 * @property string $adendos
 * @property integer $processo_id
 *
 * @property Processo $processo
 */
class Adendos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adendos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['adendos', 'processo_id'], 'required'],
            [['processo_id'], 'integer'],
            [['adendos'], 'string', 'max' => 145]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'adendos' => 'Adendos',
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
