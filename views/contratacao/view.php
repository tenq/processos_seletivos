<?php

use kartik\helpers\Html;
use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\helpers\ArrayHelper;
use app\models\Recrutamento;
use app\models\Contratacao;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contratacao */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Solcicitação de Contratação', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contratacao-view">


    <?php
/**
 * THE VIEW BUTTON
 */
echo Html::a('<i class="fa glyphicon glyphicon-print"></i> Imprimir', ['imprimir','id' => $model->id], [
    'class'=>'btn pull-right btn-info btn-lg', 
    'target'=>'_blank', 
    'data-toggle'=>'tooltip', 
    'title'=>' Clique aqui para gerar um arquivo PDF'
]);

    ?>


    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('imprimir', [
        'model' => $model,
    ]) ?>
</div>
