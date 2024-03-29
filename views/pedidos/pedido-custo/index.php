<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\editable\Editable;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\widgets\DatePicker;

use app\models\pedidos\pedidocusto\PedidocustoSituacao;
use app\models\processoseletivo\Situacao;

/* @var $this yii\web\View */
/* @var $searchModel app\models\pedidos\pedidocusto\PedidoCustoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedido de Custos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-custo-index">

<?php
//Pega as mensagens
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
echo '<div class="alert alert-'.$key.'">'.$message.'</div>';
}
?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Novo Pedido de Custo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<?php

$gridColumns = [
            
            [
                'class'=>'kartik\grid\ExpandRowColumn',
                'width'=>'1%',
                'format' => 'raw',
                'value'=>function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail'=>function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('view-expand', ['model'=>$model, 'modelsItens' => $model->pedidocustoItens]);
                },
                'headerOptions'=>['class'=>'kartik-sheet-style'], 
                'expandOneOnly'=>true
            ],

            [
                'attribute' => 'custo_id',
                'width'=>'2%',
            ],

            [
                'attribute' => 'custo_recursos',
                'width'=>'5%',
            ],

            [
                'attribute' => 'custo_assunto',
                'width'=>'5%',
            ],

            [
               'attribute' => 'custo_valortotal',
               'width'=>'5%',
               'contentOptions' => ['class' => 'col-lg-1'],
               'format' => ['decimal',2],
            ],

            [
                'attribute'=>'custo_situacaoggp', 
                'width'=>'5%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return $model->custoSituacaoggp->situacao_descricao;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(PedidocustoSituacao::find()->orderBy('situacao_descricao')->asArray()->all(), 'situacao_descricao', 'situacao_descricao'), 
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                    'filterInputOptions'=>['placeholder'=>'Selecione a Situação'],
            ],

            [
                'attribute'=>'custo_situacaodad', 
                'width'=>'5%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return $model->custoSituacaodad->situacao_descricao;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(PedidocustoSituacao::find()->orderBy('situacao_descricao')->asArray()->all(), 'situacao_descricao', 'situacao_descricao'), 
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                    'filterInputOptions'=>['placeholder'=>'Selecione a Situação'],
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'custo_situacao',
                'width'=>'5%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return $model->custoSituacao->descricao;
                },
                'readonly'=>function($model, $key, $index, $widget) {
                    return (!$model->custo_situacao); // do not allow editing of inactive records
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(Situacao::find()->orderBy('descricao')->asArray()->all(), 'descricao', 'descricao'), 
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>'Selecione a Situação'],
                //CAIXA DE ALTERAÇÕES DA SITUAÇÃO
                'editableOptions' => [
                    'header' => 'Situação',
                    'data'=>[
                                2 => 'Em Processo',
                                3 => 'Encerrado',
                            ],
                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                ],          
            ],

            [
                'attribute' => 'custo_responsavel',
                'width'=>'7%',
            ],

            [
                'attribute' => 'custo_homologador',
                'width'=>'7%',
            ],

            [
                'attribute' => 'custo_datahomologacao',
                'format' => ['date', 'php:d/m/Y'],
                'width' => '7%',
                'hAlign' => 'center',
                'filter'=> DatePicker::widget([
                'model' => $searchModel, 
                'attribute' => 'custo_datahomologacao',
                'pluginOptions' => [
                     'autoclose'=>true,
                     'format' => 'yyyy-mm-dd',
                    ]
                ])
            ],
            ['class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {homologar-custo} {delete}',
                        'contentOptions' => ['style' => 'width: 7%;'],
                        'buttons' => [

                        //VISUALIZAR/IMPRIMIR
                        'view' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-print"></span> ', $url, [
                                        'target'=>'_blank', 
                                        'data-pjax'=>"0",
                                        'class'=>'btn btn-info btn-xs',
                                        'title' => Yii::t('app', 'Imprimir'),
                       
                            ]);
                        },

                        //VISUALIZAR/IMPRIMIR
                        'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span> ', $url, [
                                        'class'=>'btn btn-default btn-xs',
                                        'title' => Yii::t('app', 'Atualizar'),
                       
                            ]);
                        },

                        //HOMOLOGAÇÃO DO PEDIDO DE CUSTO
                        'homologar-custo' => function ($url, $model) {
                            return !isset($model->custo_homologador) || !isset($model->custo_datahomologacao) ? Html::a('<span class="glyphicon glyphicon-ok"></span> ', $url, [
                                        'class'=>'btn btn-success btn-xs',
                                        'title' => Yii::t('app', 'Homologar Custo'),
                                        'data' =>  [
                                                        'confirm' => 'Você tem CERTEZA que deseja HOMOLOGAR ESSE <b>PEDIDO DE CUSTO</b>?',
                                                        'method' => 'post',
                                                   ],
                            ]): '';
                        },

                        //DELETAR
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span> ', $url, [
                                        'class'=>'btn btn-danger btn-xs',
                                        'title' => Yii::t('app', 'Deletar'),
                                        'data' =>  [
                                                        'confirm' => 'Você tem CERTEZA que deseja EXCLUIR esse item?',
                                                        'method' => 'post',
                                                   ],
                       
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
    'rowOptions' =>function($model){
                if(isset($model->custo_homologador))
                {
                    return['class'=>'success'];                        
                }
    },
    'beforeHeader'=>[
        [
            'columns'=>[
                ['content'=>'Detalhes do Pedido de Custo', 'options'=>['colspan'=>11, 'class'=>'text-center warning']], 
                ['content'=>'Área de Ações', 'options'=>['colspan'=>1, 'class'=>'text-center warning']], 
            ],
        ]
    ],
        'hover' => true,
        'panel' => [
        'type'=>GridView::TYPE_PRIMARY,
        'heading'=> '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> Listagem - Pedido de Custo</h3>',
    ],
]);
    ?>
    <?php Pjax::end(); ?>

</div>
