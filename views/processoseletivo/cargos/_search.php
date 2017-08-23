<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CargosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cargos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idcargo') ?>

    <?= $form->field($model, 'descricao') ?>

    <?= $form->field($model, 'area') ?>

    <?= $form->field($model, 'ch_semana') ?>

    <?= $form->field($model, 'salario') ?>

    <?php // echo $form->field($model, 'encargos') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>