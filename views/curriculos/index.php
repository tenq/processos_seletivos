<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CurriculosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Curriculos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="curriculos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Curriculos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'edital',
            'cargo',
            'nome',
            'cpf',
            // 'datanascimento',
            // 'sexo',
            // 'email:email',
            // 'emailAlt:email',
            // 'telefone',
            // 'telefoneAlt',
            // 'data',
            // 'curriculos_endereco_id',
            // 'curriculos_documentacao_id',
            // 'curriculos_formacao_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
