<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\editable\Editable;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\processoseletivo\CargosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cargos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cargos-index">

<?php

//Pega as mensagens
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
echo '<div class="alert alert-'.$key.'">'.$message.'</div>';
}

?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Cargo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<?php

$gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],

            'descricao',
            'area',
            'ch_semana',
            [
                'format' => 'Currency',
                'attribute' => 'salario_valorhora',
            ],
            [
                'format' => 'Currency',
                'attribute' => 'salario',
            ],
            [
                'format' => 'Currency',
                'attribute' => 'salario_1sexto',
            ],
            [
                'format' => 'Currency',
                'attribute' => 'salario_produtividade',
            ],
            [
                'format' => 'Currency',
                'attribute' => 'salario_6horasfixas',
            ],
            [
                'format' => 'Currency',
                'attribute' => 'salario_1sextofixas',
            ],
            [
                'format' => 'Currency',
                'attribute' => 'salario_bruto',
            ],
            [
                'format' => 'Currency',
                'attribute' => 'encargos',
            ],
            [
                'format' => 'Currency',
                'attribute' => 'valor_total',
            ],
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'status', 
                'vAlign'=>'middle'
            ], 
                        
            ['class' => 'yii\grid\ActionColumn','template' => '{update}'],
    ]; 
?>

<?php Pjax::begin(); ?>

    <?php 

    echo GridView::widget([
    'dataProvider'=>$dataProvider,
    'filterModel'=>$searchModel,
    'columns'=>$gridColumns,
    'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
    'headerRowOptions'=>['class'=>'kartik-sheet-style'],
    'filterRowOptions'=>['class'=>'kartik-sheet-style'],
    'pjax'=>false, // pjax is set to always true for this demo
    'beforeHeader'=>[
        [
            'columns'=>[
                ['content'=>'Detalhes de Cargos Cadastrados', 'options'=>['colspan'=>14, 'class'=>'text-center warning']], 
                ['content'=>'Área de Ações', 'options'=>['colspan'=>1, 'class'=>'text-center warning']], 
            ],
        ]
    ],
        'hover' => true,
        'panel' => [
        'type'=>GridView::TYPE_PRIMARY,
        'heading'=> '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> Listagem de Cargos</h3>',
        'persistResize'=>false,
    ],
]);
    ?>
    <?php Pjax::end(); ?>

</div>
