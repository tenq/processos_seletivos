<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\editable\Editable;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;

use app\models\etapasprocesso\EtapasItens;

/* @var $this yii\web\View */
/* @var $searchModel app\models\etapasprocesso\EtapasProcessoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cadastro de Reserva';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="etapas-cadastro-de-reserva-index">
<?php
//Pega as mensagens
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
echo '<div class="alert alert-'.$key.'">'.$message.'</div>';
}
?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<?php

$gridColumns = [

            [
                'attribute' => 'numeroEdital',
                'value' => 'curriculos.edital'
            ],
            
            [
                'attribute' => 'numeroInscricao',
                'value' => 'curriculos.numeroInscricao'
            ],
            [
                'attribute' => 'nome',
                'value' => 'curriculos.nome'
            ],
            'itens_classificacao',
            'itens_localcontratacao',
            'cargo',

            ['class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'contentOptions' => ['style' => 'width: 7%;'],
                        'buttons' => [

                        //VISUALIZAR/IMPRIMIR
                        'view' => function ($url, $model) {
                            $url = 'index.php?r=curriculos/curriculos-admin/imprimir&id=' . $model->curriculos->id;
                            return Html::a('<span class="glyphicon glyphicon-print"></span> ', $url, [
                                        'target'=>'_blank', 
                                        'data-pjax'=>"0",
                                        'class'=>'btn btn-info btn-xs',
                                        'title' => Yii::t('app', 'Imprimir'),
                       
                            ]);
                        },
                ],
           ],
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
                ['content'=>'Detalhes do Cadastro de Reserva', 'options'=>['colspan'=>6, 'class'=>'text-center warning']], 
                ['content'=>'Área de Ações', 'options'=>['colspan'=>1, 'class'=>'text-center warning']], 
            ],
        ]
    ],
        'hover' => true,
        'panel' => [
        'type'=>GridView::TYPE_PRIMARY,
        'heading'=> '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> Listagem - Cadastro de Reserva</h3>',
    ],
]);
    ?>
    <?php Pjax::end(); ?>

</div>
