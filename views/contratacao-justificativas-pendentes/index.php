<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContratacaoJustificativasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$session = Yii::$app->session;
$id_contratacao = $session['sess_contratacao'];

$this->title = 'Histórico de Justificativas  ';
$this->params['breadcrumbs'][] = ['label' => 'Contratações Pendentes', 'url' => ['contratacao-pendente/index']];
$this->params['breadcrumbs'][] = ['label' => $id_contratacao, 'url' => ['contratacao-pendente/view', 'id' => $id_contratacao]];
$this->params['breadcrumbs'][] = $this->title;

?>


<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
}
?>


<div class="contratacao-justificativas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::button('Inserir Justificativa', ['value'=> Url::to('index.php?r=contratacao-justificativas-pendentes/create'), 'class' => 'btn btn-success', 'id'=>'modalButton']) ?>
    </p>

    <?php
        Modal::begin([
            'header' => '<h4>Justificativa</h4>',
            'id' => 'modal',
            'size' => 'modal-lg',
            ]);

        echo "<div id='modalContent'></div>";

        Modal::end();

   ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'descricao',
            'usuario',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
