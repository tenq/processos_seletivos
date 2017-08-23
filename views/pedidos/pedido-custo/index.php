<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\pedidos\PedidoCustoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedido de Custos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-custo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Novo Pedido de Custo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'custo_id',
            'custo_assunto',
            'custo_recursos',
            'custo_valortotal',
            'custo_data',
            // 'custo_aprovadorggp',
            // 'custo_situacaoggp',
            // 'custo_dataaprovacaoggp',
            // 'custo_aprovadordad',
            // 'custo_situacaodad',
            // 'custo_dataaprovacaodad',
            'custo_responsavel',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
