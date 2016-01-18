<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\editable\Editable;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContratacaoEmAndamentoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contratações em Andamento';
$this->params['breadcrumbs'][] = $this->title;


//Get all flash messages and loop through them
foreach (Yii::$app->session->getAllFlashes() as $message):; ?>
            <?php
            echo \kartik\widgets\Growl::widget([
                'type' => (!empty($message['type'])) ? $message['type'] : 'danger',
                'title' => (!empty($message['title'])) ? Html::encode($message['title']) : '',
                'icon' => (!empty($message['icon'])) ? $message['icon'] : 'fa fa-info',
                'body' => (!empty($message['message'])) ? Html::encode($message['message']) : '',
                'showSeparator' => true,
                'delay' => 1, //This delay is how long before the message shows
                'pluginOptions' => [
                    'delay' => (!empty($message['duration'])) ? $message['duration'] : 3000, //This delay is how long the message shows for
                    'placement' => [
                        'from' => (!empty($message['positonY'])) ? $message['positonY'] : '',
                        'align' => (!empty($message['positonX'])) ? $message['positonX'] : '',
                    ]
                ]
            ]);
            ?>
        <?php endforeach; ?>

<div class="contratacao-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php

$gridColumns = [
                        [
                        'class'=>'kartik\grid\ExpandRowColumn',
                        'width'=>'50px',
                        'format' => 'raw',
                        'value'=>function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detail'=>function ($model, $key, $index, $column) {
                            return Yii::$app->controller->renderPartial('/contratacao/pdf_contratacao', ['model'=>$model]);
                        },
                        'headerOptions'=>['class'=>'kartik-sheet-style'], 
                        'expandOneOnly'=>true
                        ],

                        'id',
                        [
                            'attribute' => 'data_solicitacao',
                            'format' => ['date', 'php:d/m/Y'],
                        ],
                        'colaborador',
                        
                        'unidade',
                      [
                            'class' => 'kartik\grid\EditableColumn',
                            'attribute' => 'situacao_id',
                            'value' => 'situacao.descricao',  
                            'readonly'=>function($model, $key, $index, $widget) {
                                return (!$model->situacao_id); // do not allow editing of inactive records
                            },
                            //CAIXA DE ALTERAÇÕES DA SITUAÇÃO
                            'editableOptions' => [
                                'header' => 'Situação',
                                'data'=>[7 => 'Análise de Currículo', 8 => 'Avaliação Escrita', 9 => 'Avaliação Didática', 10 => 'Avaliação Comportamental', 11 => 'Entrevista', 12 => 'Formação Pedagógica' ],
                                'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                            ],          
                        ],

                        ['class' => 'yii\grid\ActionColumn',
                        'template' => '{encerrar} {correcao} {cancelar}',
                        'contentOptions' => ['style' => 'width: 380px;'],
                        'buttons' => [


                        //ENCERRAR PROCESSO SOLETIVO
                        'encerrar' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-ok"></span> Encerrar', $url, [
                                        'class'=>'btn btn-success btn-xs',
                                        //INSERIR SWEETALERT - ESTUDAR SOBRE ISSO
                                         'data' => [
                                                   'confirm' => 'Você tem CERTEZA que deseja ENCERRAR essa Solicitação de Contratação?',
                                                   'method' => 'post',
                                                   ],
                            ]);
                        },
                        //ENVIAR PARA CORREÇÃO
                        'correcao' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-repeat"></span> Enviar para Correção', $url, [
                                        'class'=>'btn btn-warning btn-xs',
                       
                            ]);
                        },

                        //CANCELAR
                        'cancelar' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-remove"></span> Cancelar', $url, [
                                        'class'=>'btn btn-danger btn-xs',
                       
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
                ['content'=>'Detalhes da Solicitação de Contratação', 'options'=>['colspan'=>5, 'class'=>'text-center warning']], 
                ['content'=>'Área de Ações', 'options'=>['colspan'=>3, 'class'=>'text-center warning']], 
            ],
        ]
    ],
        'hover' => true,
        'panel' => [
        'type'=>GridView::TYPE_PRIMARY,
        'heading'=> '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> Listagem - Contratações em Andamento</h3>',
    ],
]);
    ?>
    <?php Pjax::end(); ?>

</div>
