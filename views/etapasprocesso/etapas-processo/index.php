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

$this->title = 'Etapas do Processo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="etapas-processo-index">
<?php
//Pega as mensagens
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
echo '<div class="alert alert-'.$key.'">'.$message.'</div>';
}
?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::button('Criar Etapas do Processo', ['value'=> Url::to('index.php?r=etapasprocesso/etapas-processo/create'), 'class' => 'btn btn-success', 'id'=>'modalButton']) ?>
    </p>

    <?php
        Modal::begin([
            'options' => ['tabindex' => false ], // important for Select2 to work properly
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => true],
            'header' => '<h4>Geração de Etapas do Processo</h4>',
            'id' => 'modal',
            'size' => 'modal-lg',
            ]);

        echo "<div id='modalContent'></div>";

        Modal::end();
    ?>

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
                $itens = EtapasItens::find()->where(['etapasprocesso_id' => $model->etapa_id])->orderBy(['itens_pontuacaototal' => SORT_DESC])->all();
                return Yii::$app->controller->renderPartial('view-expand', ['model'=>$model, 'etapasItens' => $itens]);
             },
             'headerOptions'=>['class'=>'kartik-sheet-style'], 
             'expandOneOnly'=>true
             ],
            'etapa_id',
            [
              'attribute' => 'contratacao_id',
              'format' => 'raw',
              'value' => function($data){
               $query = (new \yii\db\Query())->select(['contratacao_id'])->from('pedidocusto_itens')->innerJoin('contratacao', 'pedidocusto_itens.itemcusto_area = contratacao.cargo_area')->where(['itemcusto_cargo' => $data->etapa_cargo])->andWhere(['pedidocusto_id' => $data->pedidocusto_id])->groupBy(['pedidocusto_itens.id']);//->andWhere(['itemcusto_unidade' => $data->etapa_local]);
                 $command = $query->createCommand();
                 $data= $command->queryOne();
                   return  Html::a($data['contratacao_id'], ['/contratacao/contratacao/view', 'id' => $data['contratacao_id']], ['target'=>'_blank', 'data-pjax'=>"0"]);
               }
            ],
            [
                   'attribute' => 'pedidocusto_id',
                   'format' => 'raw',
                   'value' => function ($data) {
                                 return Html::a($data->pedidocusto_id, ['/pedidos/pedido-custo/view', 'id' => $data->pedidocusto_id], ['target'=>'_blank', 'data-pjax'=>"0"]);
                             },
            ],
            [
                   'attribute' => 'processo_id',
                   'format' => 'raw',
                   'value' => function ($data) {
                                 return Html::a($data->processo->numeroEdital, ['/processoseletivo/processo-seletivo/view', 'id' => $data->processo->numeroEdital], ['target'=>'_blank', 'data-pjax'=>"0"]);
                             },
            ],
            'etapa_cargo',
            [
                'attribute'=>'etapa_perfil',
                'width'=>'10%',
                'value' => function ($data) { return $data->etapa_perfil == 0 ? 'Administrativo' : 'Docente/Motorista/Cozinha'; },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=> ['0'=>'Administrativo','1'=>'Docente/Motorista/Cozinha'],
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                    'filterInputOptions'=>['placeholder'=>'Selecione a Etapa'],
            ],
            [
                'attribute'=>'etapa_situacao',
                'width'=>'10%',
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=> ['Em Processo'=>'Em Processo', 'Em Homologação' => 'Em Homologação', 'Encerrado'=>'Encerrado', 'Encerrado sem Classificados'=>'Encerrado sem Classificados'],
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                    'filterInputOptions'=>['placeholder'=>'Selecione a Situação'],
            ],

            ['class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
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

                        //DELETAR
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span> ', $url, [
                                        'class'=>'btn btn-danger btn-xs',
                                        'title' => Yii::t('app', 'Deletar Item'),
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
        if($model->etapa_situacao == 'Em Homologação'){
            return['class'=>'warning'];                        
        }else if($model->etapa_situacao == 'Em Processo'){
            return['class'=>'info'];    
        }else if($model->etapa_situacao == 'Encerrado' || $model->etapa_situacao == 'Encerrado sem Classificados'){
            return['class'=>'success'];    
        }
    },
    'beforeHeader'=>[
        [
            'columns'=>[
                ['content'=>'Detalhes das Etapas do Processo', 'options'=>['colspan'=>8, 'class'=>'text-center warning']], 
                ['content'=>'Área de Ações', 'options'=>['colspan'=>1, 'class'=>'text-center warning']], 
            ],
        ]
    ],
        'hover' => true,
        'panel' => [
        'type'=>GridView::TYPE_PRIMARY,
        'heading'=> '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> Listagem - Etapas do Processo</h3>',
    ],
]);
    ?>
    <?php Pjax::end(); ?>

</div>

