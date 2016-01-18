<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => 'Processo Seletivo - Senac AM',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Solicitação de Contratação', 'url' => ['/contratacao/index']],

                    ['label' => 'Controle - Contratações',
                'items' => [
                 '<li class="dropdown-header">Área Administrativa</li>',
                 ['label' => 'Contratações Pendentes', 'url' => ['/contratacao-pendente/index']],
                 ['label' => 'Contratações Em Andamento', 'url' => ['/contratacao-em-andamento/index']],
                           ],
                    ],

                    ['label' => 'Processos Seletivos',
                'items' => [
                 '<li class="dropdown-header">Gerenciamento do Site</li>',
                 ['label' => 'Listagem de Candidatos', 'url' => ['/curriculos/index']],
                 ['label' => 'Processos Seletivos', 'url' => ['/processo-seletivo/index']],
                           ],
                    ],

                    ['label' => 'Administração',
                'items' => [
                 '<li class="dropdown-header">Administração</li>',
                 ['label' => 'Cargos', 'url' => ['/cargos/index']],
                           ],
                    ],

                    ['label' => 'Sair', 'url' => 'http://portalsenac.am.senac.br/portal_senac/control_base_vermodulos/control_base_vermodulos.php'],
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Gerência de Informática Corporativa <?= date('Y') ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
