<?php

namespace app\controllers\pedidos;

use Yii;
use app\models\contratacao\Contratacao;
use app\models\etapasprocesso\EtapasProcesso;
use app\models\pedidos\pedidocusto\PedidoCusto;
use app\models\pedidos\pedidocusto\PedidocustoItens;
use app\models\pedidos\pedidocusto\PedidoCustoSearch;
use app\models\pedidos\pedidocusto\PedidoCustoAprovacaoGgpSearch;
use app\models\pedidos\pedidocusto\PedidoCustoAprovacaoDadSearch;
use app\models\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * PedidoCustoController implements the CRUD actions for PedidoCusto model.
 */
class PedidoCustoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $this->AccessAllow(); //Irá ser verificado se o usuário está logado no sistema
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PedidoCusto models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'main-full';
        //VERIFICA SE O COLABORADOR FAZ PARTE DO SETOR GRH E DO DEPARTAMENTO DE PROCESSO SELETIVO
        $session = Yii::$app->session;
        if($session['sess_codunidade'] != 7 || $session['sess_coddepartamento'] != 82){
            return $this->AccessoAdministrador();
        }
        $searchModel = new PedidoCustoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->post('hasEditable')) {
                // instantiate your PedidoCusto model for saving
                $pedidoCustoId = Yii::$app->request->post('editableKey');
                $model = PedidoCusto::findOne($pedidoCustoId);

                // store a default json response as desired by editable
                $out = Json::encode(['output'=>'', 'message'=>'']);

                $posted = current($_POST['PedidoCusto']);
                $post = ['PedidoCusto' => $posted];

                // load model like any single model validation
                if ($model->load($post)) {
                // can save model or do something before saving model
                $model->save(false);

                $output = '';

                $out = Json::encode(['output'=>$output, 'message'=>'']);
                }
                // return ajax json encoded response and exit
                echo $out;
                return $this->redirect(['index']);
            }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionHomologarCusto($id)
    {
        $session = Yii::$app->session;
        $model = $this->findModel($id);

        //Se não existir as aprovações da GGP e DAD, não será possível homologar o processo
        if($model->custo_situacaoggp != 4 || $model->custo_situacaodad != 4) {
            Yii::$app->session->setFlash('danger', '<b>ERRO! </b>Solicitação sem aprovações!</b>');
            return $this->redirect(['index']);
        }
        //Homologa o Pedido de Custo
        $connection = Yii::$app->db;
        $connection->createCommand()
            ->update('pedido_custo', ['custo_homologador' => $session['sess_nomeusuario'], 'custo_datahomologacao' => date('Y-m-d')], ['custo_id' => $model->custo_id])
            ->execute();

        Yii::$app->session->setFlash('success', '<b>SUCESSO!</b> Pedido de Custo <b> '.$model->custo_id.' </b> foi Homologado!</b>');

        return $this->redirect(['index']);
    }

    public function actionGgpIndex()
    {
        $this->layout = 'main-full';

        $searchModel = new PedidoCustoAprovacaoGgpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('ggp-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDadIndex()
    {
        $this->layout = 'main-full';

        $searchModel = new PedidoCustoAprovacaoDadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('dad-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAprovarGgp($id)
    {
        $session = Yii::$app->session;
        $model = $this->findModel($id);

        //Aprovado o Pedido de Custo
        $connection = Yii::$app->db;
        $command = $connection->createCommand(
        "UPDATE `db_processos`.`pedido_custo` SET `custo_aprovadorggp` = '".$session['sess_nomeusuario']."', `custo_situacaoggp` = '4', `custo_dataaprovacaoggp` = ".date('"Y-m-d"')." WHERE `custo_id` = '".$model->custo_id."'");
        $command->execute();
        
        Yii::$app->session->setFlash('success', '<b>SUCESSO!</b> Pedido de Custo <b> '.$model->custo_id.' </b> foi Aprovado!</b>');

        return $this->redirect(['ggp-index']);
    }
    
    public function actionAprovarDad($id)
    {
        $session = Yii::$app->session;
        $model = $this->findModel($id);

        //Aprovado o Pedido de Custo
        $connection = Yii::$app->db;
        $command = $connection->createCommand(
        "UPDATE `db_processos`.`pedido_custo` SET `custo_aprovadordad` = '".$session['sess_nomeusuario']."', `custo_situacaodad` = '4', `custo_dataaprovacaodad` = ".date('"Y-m-d"')." WHERE `custo_id` = '".$model->custo_id."'");
        $command->execute();
        
        Yii::$app->session->setFlash('success', '<b>SUCESSO!</b> Pedido de Custo <b> '.$model->custo_id.' </b> foi Aprovado!</b>');

        return $this->redirect(['dad-index']);
    }

    public function actionReprovarGgp($id)
    {
        $session = Yii::$app->session;
        $model = $this->findModel($id);

        //Reprova o Pedido de Custo
        $connection = Yii::$app->db;
        $command = $connection->createCommand(
        "UPDATE `db_processos`.`pedido_custo` SET `custo_aprovadorggp` = '".$session['sess_nomeusuario']."', `custo_situacaoggp` = '3', `custo_dataaprovacaoggp` = ".date('"Y-m-d"')." WHERE `custo_id` = '".$model->custo_id."'");
        $command->execute();
        
        Yii::$app->session->setFlash('success', '<b>SUCESSO!</b> Pedido de Custo <b> '.$model->custo_id.' </b> foi Reprovado!</b>');

        return $this->redirect(['ggp-index']);
    }

    public function actionReprovarDad($id)
    {
        $session = Yii::$app->session;
        $model = $this->findModel($id);

        //Reprova o Pedido de Custo
        $connection = Yii::$app->db;
        $command = $connection->createCommand(
        "UPDATE `db_processos`.`pedido_custo` SET `custo_aprovadordad` = '".$session['sess_nomeusuario']."', `custo_situacaodad` = '3', `custo_dataaprovacaodad` = ".date('"Y-m-d"')." WHERE `custo_id` = '".$model->custo_id."'");
        $command->execute();
        
        Yii::$app->session->setFlash('success', '<b>SUCESSO!</b> Pedido de Custo <b> '.$model->custo_id.' </b> foi Reprovado!</b>');

        return $this->redirect(['dad-index']);
    }

    /**
     * Displays a single PedidoCusto model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $this->layout = 'main-imprimir';
        //VERIFICA SE O COLABORADOR FAZ PARTE DO SETOR GRH E DO DEPARTAMENTO DE PROCESSO SELETIVO
        $session = Yii::$app->session;
        if($session['sess_codunidade'] != 7 || $session['sess_coddepartamento'] != 82){
            return $this->AccessoAdministrador();
        }
        $model = $this->findModel($id);

        $modelsItens = $model->pedidocustoItens;

        return $this->render('view', [
            'model' => $model,
            'modelsItens' => $modelsItens,
        ]);
    }

    //Localiza os dados da contratação
    public function actionGetContratacao($contratacaoId){

        $connection = Yii::$app->db;
        $command = $connection->createCommand('
             SELECT
            `contratacao`.`unidade`,
            `cargos`.`descricao` AS `cargo_descricao`,
            `contratacao`.`quant_pessoa`,
            `contratacao`.`periodo`,
            `contratacao`.`cargo_area`,
            `contratacao`.`cargo_chsemanal`,
            `contratacao`.`cargo_salario`,
            `contratacao`.`cargo_encargos`,
            `contratacao`.`cargo_valortotal`,
            `contratacao`.`motivo`,
            `contratacao`.`data_ingresso_prevista`
            FROM
            `contratacao`
            INNER JOIN `cargos` ON `contratacao`.`cargo_id` = `cargos`.`idcargo` 
            WHERE `id`='.$contratacaoId.'
            ');
           $queryResult = $command->queryOne();

        echo Json::encode($queryResult);
    }

    /**
     * Creates a new PedidoCusto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        //VERIFICA SE O COLABORADOR FAZ PARTE DO SETOR GRH E DO DEPARTAMENTO DE PROCESSO SELETIVO
        $session = Yii::$app->session;
        if($session['sess_codunidade'] != 7 || $session['sess_coddepartamento'] != 82){
            return $this->AccessoAdministrador();
        }
        $model       = new PedidoCusto();
        $modelsItens = [new PedidocustoItens];

        $model->custo_situacaoggp = 1; //Aguardando Autorização GPP
        $model->custo_situacaodad = 1; //Aguardando Autorização DAD
        $model->custo_situacao    = 2; //Em Processo
        $model->custo_data        = date('Y-m-d');
        $model->custo_responsavel = $session['sess_nomeusuario'];
        $model->custo_recursos    = 'PRÓPRIOS';

        //1 => Em elaboração / 2 => Em correção pelo setor / 3 => Recebido pelo GGP / 5 - Finalizado
        $subQuery = PedidocustoItens::find()->select('id', 'contratacao_id')->all();
        $contratacoes = Contratacao::find()
        ->where(['NOT IN','situacao_id', [1, 2, 3, 5]])
        ->andWhere(['NOT IN','id', $subQuery])
        ->orderBy('id')
        ->all();

        if ($model->load(Yii::$app->request->post())) {

            //Inserir vários itens
            $modelsItens = Model::createMultiple(PedidocustoItens::classname());
            Model::loadMultiple($modelsItens, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();

            if ($valid ) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsItens as $modelItens) {
                            $modelItens->pedidocusto_id = $model->custo_id;
                            if (! ($flag = $modelItens->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                    if ($flag) {
                            //Verifica se existe já a contratação inserida anterioemente em algum pedido de custo
                            foreach ($modelsItens as $i => $modelItens) {
                            if(PedidocustoItens::find()->where(['contratacao_id' => $_POST['PedidocustoItens'][$i]['contratacao_id']])->count() >= 2) {
                                Yii::$app->session->setFlash('danger', '<b>ERRO! </b>Solicitação <b>'.$_POST['PedidocustoItens'][$i]['contratacao_id'].'</b> já inserida no Pedido de Custo!</b>');
                                return $this->redirect(['index']);
                                }
                            }
                    $model->save();
                    $transaction->commit();
                            
                        Yii::$app->session->setFlash('success', '<b>SUCESSO!</b> Pedido de Custo Cadastrado!</b>');
                       return $this->redirect(['index']);
                    }
                }
                }  catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

            Yii::$app->session->setFlash('success', '<b>SUCESSO!</b> Pedido de Custo Cadastrado!</b>');

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'contratacoes' => $contratacoes,
                'modelsItens' => $modelsItens,
            ]);
        }
    }

    /**
     * Updates an existing PedidoCusto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        //VERIFICA SE O COLABORADOR FAZ PARTE DO SETOR GRH E DO DEPARTAMENTO DE PROCESSO SELETIVO
        $session = Yii::$app->session;
        if($session['sess_codunidade'] != 7 || $session['sess_coddepartamento'] != 82){
            return $this->AccessoAdministrador();
        }
        $model = $this->findModel($id);
        $modelsItens = $model->pedidocustoItens;

        $model->custo_situacaoggp = 1; //Aguardando Autorização GPP
        $model->custo_situacaodad = 1; //Aguardando Autorização DAD
        $model->custo_data = date('Y-m-d');
        $model->custo_responsavel = $session['sess_nomeusuario'];

        //Verifica se o Pedido de Custo já foi homologado
        if(isset($model->custo_homologador) || isset($model->custo_datahomologacao)) {
            Yii::$app->session->setFlash('danger', '<b>ERRO!</b> Pedido de Custo já Homologado. Não é possível executar esta ação!');
            return $this->redirect(['index']);
        }

        //[4,7,8,9,10,11,12,13,14] -> Situações EM ANDAMENTO
        $contratacoes = Contratacao::find()->where(['IN','situacao_id', [4,7,8,9,10,11,12,13,14,15,16,17]])->orderBy('id')->all();

        //Verifica se já existe alguma etapa de processo criada
        if(isset($model->etapasProcesso->pedidocusto_id)) {
            Yii::$app->session->setFlash('danger', '<b>ERRO! </b> Não é possível <b>EDITAR</b> pois já existem <b>Etapas do Processo</b> criadas para esse Pedido de Custo.');
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {

        //--------Itens do Pedido de Custo--------------
        $oldIDsItens = ArrayHelper::map($modelsItens, 'id', 'id');
        $modelsItens = Model::createMultiple(PedidocustoItens::classname(), $modelsItens);
        Model::loadMultiple($modelsItens, Yii::$app->request->post());
        $deletedIDsItens = array_diff($oldIDsItens, array_filter(ArrayHelper::map($modelsItens, 'id', 'id')));

        // validate all models
        $valid = $model->validate();
        $valid = (Model::validateMultiple($modelsItens) && $valid);

                        if ($valid) {
                            $transaction = \Yii::$app->db->beginTransaction();
                            try {
                                if ($flag = $model->save(false)) {
                                    if (! empty($deletedIDsItens)) {
                                        PedidocustoItens::deleteAll(['id' => $deletedIDsItens]);
                                    }
                                    foreach ($modelsItens as $modelItens) {
                                        $modelItens->pedidocusto_id = $model->custo_id;
                                        if (! ($flag = $modelItens->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                    }
                                }

                               if ($flag) {
                                    //Verifica se existe já a contratação inserida anterioemente em algum pedido de custo
                                    foreach ($modelsItens as $i => $modelItens) {
                                    if(PedidocustoItens::find()->where(['contratacao_id' => $_POST['PedidocustoItens'][$i]['contratacao_id']])->count() >= 2) {
                                        Yii::$app->session->setFlash('danger', '<b>ERRO! </b>Solicitação <b>'.$_POST['PedidocustoItens'][$i]['contratacao_id'].'</b> já inserida no Pedido de Custo!</b>');
                                        return $this->redirect(['update', 'id' => $model->custo_id]);
                                        }
                                    }
                        $model->save();
                        $transaction->commit();
                            
                        Yii::$app->session->setFlash('success', '<b>SUCESSO!</b> Pedido de Custo Atualizado!</b>');
                       return $this->redirect(['index']);
                    }
                }catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

            Yii::$app->session->setFlash('success', '<b>SUCESSO!</b> Pedido de Custo Atualizado!</b>');

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'contratacoes' => $contratacoes,
                'modelsItens' => (empty($modelsItens)) ? [new PedidocustoItens] : $modelsItens,
            ]);
        }
    }

    /**
     * Deletes an existing PedidoCusto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        //Verifica se já existe alguma etapa de processo criada
        if(isset($model->etapasProcesso->pedidocusto_id)) {
            Yii::$app->session->setFlash('danger', '<b>ERRO! </b> Não é possível <b>EXCLUIR</b> pois já existem <b>Etapas do Processo</b> criadas para esse Pedido de Custo.');
            return $this->redirect(['index']);
        //Verifica se o Pedido de Custo já foi homologado
        }else if(isset($model->custo_homologador) || isset($model->custo_datahomologacao)){
            Yii::$app->session->setFlash('danger', '<b>ERRO!</b> Pedido de Custo já Homologado. Não é possível executar esta ação!');
            return $this->redirect(['index']);
        }else{
            PedidocustoItens::deleteAll('pedidocusto_id = "'.$id.'"');
            $model->delete(); //Exclui o pedido de custo
            Yii::$app->session->setFlash('success', '<b>SUCESSO! </b> Pedido de Custo excluido!</b>');
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the PedidoCusto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PedidoCusto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PedidoCusto::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function AccessAllow()
    {
        $session = Yii::$app->session;
        if (!isset($session['sess_codusuario']) 
            && !isset($session['sess_codcolaborador']) 
            && !isset($session['sess_codunidade']) 
            && !isset($session['sess_nomeusuario']) 
            && !isset($session['sess_coddepartamento']) 
            && !isset($session['sess_codcargo']) 
            && !isset($session['sess_cargo']) 
            && !isset($session['sess_setor']) 
            && !isset($session['sess_unidade']) 
            && !isset($session['sess_responsavelsetor'])) 
        {
           return $this->redirect('https://portalsenac.am.senac.br');
        }
    }

    public function AccessoAdministrador()
    {
            $this->layout = 'main-acesso-negado';
            return $this->render('/site/acesso_negado');
    }
}
