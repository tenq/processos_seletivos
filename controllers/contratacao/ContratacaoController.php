<?php

namespace app\controllers\contratacao;

use Yii;
use app\models\processoseletivo\Cargos;
use app\models\etapasprocesso\EtapasProcesso;
use app\models\etapasprocesso\EtapasItens;
use app\models\contratacao\Sistemas;
use app\models\contratacao\SistemasContratacao;
use app\models\contratacao\ContratacaoJustificativas;
use app\models\contratacao\Contratacao;
use app\models\contratacao\ContratacaoSearch;
use app\models\contratacao\SituacaoContratacao;
use app\models\contratacao\SituacaoContratacaoSearch;
use app\models\contratacao\Emailusuario;
use app\models\contratacao\EmailusuarioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use yii\helpers\Json;

use mPDF;

/**
 * ContratacaoController implements the CRUD actions for Contratacao model.
 */
class ContratacaoController extends Controller
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
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Contratacao models.
     * @return mixed
     */
    public function actionIndex()
    {

        $session = Yii::$app->session;
    //VERIFICA SE O COLABORADOR É GERENTE OU SE FAZ PARTE DA EQUIPE DE SELEÇÃO PARA REALIZAR A SOLICITAÇÃO
    if($session['sess_responsavelsetor'] == 0 && $session['sess_coddepartamento'] != 82){

        $this->layout = 'main-acesso-negado';
        return $this->render('/site/acesso_negado');

    }else

        $searchModel = new ContratacaoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEtapas($id)
    {
        $this->layout = 'main-imprimir';
        
        $connection = Yii::$app->db;
        $command = $connection->createCommand('SELECT *
                    FROM `db_processos`.`etapas_processo` 
                    INNER JOIN `pedidocusto_itens` ON `pedidocusto_itens`.`pedidocusto_id` = `etapas_processo`.`pedidocusto_id`
                    INNER JOIN `processo` ON `processo`.`id` = `etapas_processo`.`processo_id`
                    INNER JOIN `contratacao` ON `contratacao`.`id` = `pedidocusto_itens`.`contratacao_id`
                    INNER JOIN `cargos` ON `cargos`.`idcargo` = `contratacao`.`cargo_id`
                    WHERE `pedidocusto_itens`.`contratacao_id` = '.$id.' AND `etapas_processo`.`etapa_cargo` = `cargos`.`descricao` ');
        $model = $command->queryOne();

        $itens = EtapasItens::find()->where(['etapasprocesso_id' => $model['etapa_id']])->orderBy(['itens_pontuacaototal' => SORT_DESC])->all();

        return $this->render('etapas', [
            'model' => $model,
            'itens' => $itens,
        ]);
    }

    public function actionImprimir($id) {

            $model = $this->findModel($id);

            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
                'content' => $this->renderPartial('imprimir', ['model' => $model]),
                'options' => [
                    'title' => 'Recrutamento e Seleção - Senac AM',
                    //'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
                ],
                'methods' => [
                    'SetHeader' => ['SOLICITAÇÃO DE CONTRATAÇÃO - SENAC AM||Gerado em: ' . date("d/m/Y - H:i:s")],
                    'SetFooter' => ['Recrutamento e Seleção - GRH||Página {PAGENO}'],
                ]
            ]);

        return $pdf->render('imprimir', [
            'model' => $model,
        ]);
    }

    //Localiza os cargos vinculado ao Processo Seletivo
    public function actionAreasCargo() {
                $out = [];
                if (isset($_POST['depdrop_parents'])) {
                    $parents = $_POST['depdrop_parents'];
                    if ($parents != null) {
                        $cat_id = $parents[0];
                        $out = Contratacao::getAreasCargoSubCat($cat_id);
                        echo Json::encode(['output'=>$out, 'selected'=>'']);
                        return;
                    }
                }
                echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    /**
     * Displays a single Contratacao model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $session = Yii::$app->session;
    //VERIFICA SE O COLABORADOR FAZ É GERENTE PARA REALIZAR A SOLICITAÇÃO
    if($session['sess_responsavelsetor'] == 0 && $session['sess_coddepartamento'] != 82){

        $this->layout = 'main-acesso-negado';
        return $this->render('/site/acesso_negado');

    }else

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    //Localiza os dados do Cargo
    public function actionGetCargos($cargosId){

        $getCargos = Cargos::findOne($cargosId);
        echo Json::encode($getCargos);
    }

    /**
     * Creates a new Contratacao model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $session = Yii::$app->session;
    //VERIFICA SE O COLABORADOR É GERENTE OU SE FAZ PARTE DA EQUIPE DE SELEÇÃO PARA REALIZAR A SOLICITAÇÃO
    if($session['sess_responsavelsetor'] == 0 && $session['sess_coddepartamento'] != 82){

        $this->layout = 'main-acesso-negado';
        return $this->render('/site/acesso_negado');

    }else

        $model = new Contratacao();

        $cargos = Cargos::find()->where(['status' => 1])->andWhere(['!=','homologacao', ''])->orderBy('descricao')->all();
        $sistemas = Sistemas::find()->where(['status' => 1])->all();

        $session = Yii::$app->session;
            $model->cod_colaborador     = $session['sess_codcolaborador'];
            $model->colaborador         = $session['sess_nomeusuario'];
            $model->cargo               = $session['sess_cargo'];
            $model->cod_unidade_solic   = $session['sess_codunidade'];
            $model->unidade             = $session['sess_unidade'];
            $model->data_solicitacao    = date('Y-m-d');
            $model->hora_solicitacao    = date('H:i:s');
            $model->nomesituacao        = 'Em Elaboração';
            $model->situacao_id         = '1';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            //Quando a solicitação é enviada, atualiza a solicitação para RECEBIDO PELO GRH.
            $connection = Yii::$app->db;
            $command = $connection->createCommand(
            "UPDATE `db_processos`.`contratacao` SET `situacao_id` = '3' WHERE `id` = '".$model->id."' AND `cod_unidade_solic` =" . $session['sess_codunidade']);
            $command->execute();


         //ENVIANDO EMAIL PARA OS RESPONSÁVEL DO PROCESSO SELETIVO INFORMANDO SOBRE O RECEBIMENTO DE UMA NOVA SOLICITAÇÃO DE CONTRATAÇÃO
          $sql_email = "SELECT emus_email FROM emailusuario_emus,colaborador_col,responsavelambiente_ream,responsaveldepartamento_rede WHERE ream_codunidade = '7' AND rede_coddepartamento = '82' AND rede_codcolaborador = col_codcolaborador AND col_codusuario = emus_codusuario GROUP BY emus_email";
      
      $email_solicitacao = Emailusuario::findBySql($sql_email)->all(); 
      foreach ($email_solicitacao as $email)
          {
            $email_gerente  = $email["emus_email"];

                            Yii::$app->mailer->compose()
                            ->setFrom(['contratacao@am.senac.br' => 'Contratação - Senac AM'])
                            ->setTo($email_gerente)
                            ->setSubject('Solicitação de Contratação - ' . $model->unidade)
                            ->setTextBody('Existe uma solicitação de contratação de código: '.$model->id.' PENDENTE')
                            ->setHtmlBody('<h4>Prezado(a) Senhor(a), <br><br>Existe uma solicitação de contratação de <strong style="color: #337ab7"">código: '.$model->id.'</strong> PENDENTE. <br> Por favor, não responda esse e-mail. Acesse https://portalsenac.am.senac.br para ANALISAR a solicitação de contratação. <br><br> Atenciosamente, <br> Contratação de Pessoal - Senac AM.</h4>')
                            ->send();
                        } 
            //MENSAGEM DE CONFIRMAÇÃO DA SOLICITAÇÃO DE CONTRATAÇÃO CRIADA COM SUCESSO
            Yii::$app->session->setFlash('success', '<strong>SUCESSO! </strong> A solicitação de Processo Seletivo de código <strong>' .$model->id. '</strong> foi enviada para a Gerência de Recursos Humanos!</strong>');

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'cargos' => $cargos,
                'sistemas' => $sistemas,
            ]);
        }
    }

    /**
     * Updates an existing Contratacao model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $session = Yii::$app->session;
    //VERIFICA SE O COLABORADOR É GERENTE OU SE FAZ PARTE DA EQUIPE DE SELEÇÃO PARA REALIZAR A SOLICITAÇÃO
    if($session['sess_responsavelsetor'] == 0 && $session['sess_coddepartamento'] != 82){

        $this->layout = 'main-acesso-negado';
        return $this->render('/site/acesso_negado');

    }else
    
        $model = $this->findModel($id);

       $cargos = Cargos::find()->where(['status' => 1])->andWhere(['!=','homologacao', ''])->orderBy('descricao')->all();
       $sistemas = Sistemas::find()->where(['status' => 1])->all();

        //Retrieve the stored checkboxes
        $model->permissions = \yii\helpers\ArrayHelper::getColumn(
            $model->getSistemasContratacao()->asArray()->all(),
            'sistema_id'
        );

        //USUÁRIOS APENAS IRÃO EDITAR AS SOLICITAÇÕES DE CONTRATAÇÃO COM STATUS DE 'EM ELABORAÇÃO' e 'EM CORREÇÃO'
        if($model->situacao_id != 1 && $model->situacao_id != 2 ){

        Yii::$app->session->setFlash('warning', '<strong>AVISO! </strong> Não é possível <strong>EDITAR</strong> a Solicitação de Contratação de código: ' . '<strong>' .$id. '</strong>' . ' pois a mesma está com status de  ' . '<strong>' . $model->situacao->descricao . '.</strong>');

        return $this->redirect(['index']);
        }

        $session = Yii::$app->session;
            $model->cod_colaborador     = $session['sess_codcolaborador'];
            $model->colaborador         = $session['sess_nomeusuario'];
            $model->cargo               = $session['sess_cargo'];
            $model->cod_unidade_solic   = $session['sess_codunidade'];
            $model->unidade             = $session['sess_unidade'];
            $model->data_solicitacao    = date('Y-m-d');
            $model->hora_solicitacao    = date('H:i:s');
            $model->nomesituacao        = $model->situacao->descricao;
            

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            //Quando a solicitação é enviada, atualiza a solicitação para RECEBIDO PELO GRH.
            $connection = Yii::$app->db;
            $command = $connection->createCommand(
            "UPDATE `db_processos`.`contratacao` SET `situacao_id` = '3' WHERE `id` = '".$model->id."' AND `cod_unidade_solic` =" . $session['sess_codunidade']);
            $command->execute();


         //ENVIANDO EMAIL PARA OS RESPONSÁVEL DO PROCESSO SELETIVO INFORMANDO SOBRE O RECEBIMENTO DE UMA NOVA SOLICITAÇÃO DE CONTRATAÇÃO
          $sql_email = "SELECT emus_email FROM emailusuario_emus,colaborador_col,responsavelambiente_ream,responsaveldepartamento_rede WHERE ream_codunidade = '7' AND rede_coddepartamento = '82' AND rede_codcolaborador = col_codcolaborador AND col_codusuario = emus_codusuario GROUP BY emus_email";
      
      $email_solicitacao = Emailusuario::findBySql($sql_email)->all(); 
      foreach ($email_solicitacao as $email)
          {
            $email_gerente  = $email["emus_email"];

                            Yii::$app->mailer->compose()
                            ->setFrom(['contratacao@am.senac.br' => 'Contratação - Senac AM'])
                            ->setTo($email_gerente)
                            ->setSubject('Solicitação de Contratação - ' . $model->unidade)
                            ->setTextBody('Existe uma solicitação de contratação de código: '.$model->id.' PENDENTE')
                            ->setHtmlBody('<h4>Prezado(a) Senhor(a), <br><br>Existe uma solicitação de contratação de <strong style="color: #337ab7"">código: '.$model->id.'</strong> PENDENTE. <br> Por favor, não responda esse e-mail. Acesse https://portalsenac.am.senac.br para ANALISAR a solicitação de contratação. <br><br> Atenciosamente, <br> Contratação de Pessoal - Senac AM.</h4>')
                            ->send();
                        } 

            //MENSAGEM DE CONFIRMAÇÃO DA SOLICITAÇÃO DE CONTRATAÇÃO CRIADA COM SUCESSO
            Yii::$app->session->setFlash('success', '<strong>SUCESSO! </strong> A solicitação de Processo Seletivo de código <strong>' .$model->id. '</strong> foi enviada para a Gerência de Recursos Humanos!</strong>');
           
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'cargos' => $cargos,
                'sistemas' => $sistemas,
            ]);
        }
    }

    public function actionObservacoes($id) 
    {


        $model = Contratacao::findOne($id);
        $session = Yii::$app->session;
        $session->set('sess_contratacao', $model->id);

        return $this->redirect(Yii::$app->request->BaseUrl . '/index.php?r=contratacao/contratacao-justificativas-pendentes%2Fobservacoes', [
             'model' => $model,
         ]);
    }


    /**
     * Deletes an existing Contratacao model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        //USUÁRIOS APENAS IRÃO EXCLUIR AS SOLICITAÇÕES DE CONTRATAÇÃO COM STATUS DE 'EM ELABORAÇÃO' e 'EM CORREÇÃO'
        if($model->situacao_id != 1 && $model->situacao_id != 2 ){

        Yii::$app->session->setFlash('danger', '<strong>ERRO! </strong> Não é possível <strong>EXCLUIR</strong> a Solicitação de Contratação de código: ' . '<strong>' .$id. '</strong>' . ' pois a mesma está com status de  ' . '<strong>' . $model->situacao->descricao . '.</strong>');

        return $this->redirect(['index']);
        }

         //BUSCA NO BANCO SE EXISTE JUSTIFICATIVAS PARA A SOLICITAÇÃO
         $checarJustificativa = ContratacaoJustificativas::find()->where(['id_contratacao' => $_GET])->all();
         foreach ($checarJustificativa as $value) {
                $justificativa = $value["id_contratacao"];

        //Caso tenha justificativa será excluida.
        $connection = Yii::$app->db;
        $command = $connection->createCommand(
        "DELETE FROM `contratacao_justificativas` WHERE `contratacao_justificativas`.`id_contratacao`= '".$justificativa."'");
        $command->execute();

         }

         //BUSCA NO BANCO SE EXISTE SISTEMAS CADASTRADOS PARA A SOLICITAÇÃO
         $checarSistema = SistemasContratacao::find()->where(['contratacao_id' => $_GET])->all();
         foreach ($checarSistema as $value) {
                $sistema = $value["contratacao_id"];

        //Caso tenha justificativa será excluida.
        $connection = Yii::$app->db;
        $command = $connection->createCommand(
        "DELETE FROM `sistemas_contratacao` WHERE `sistemas_contratacao`.`contratacao_id`= '".$sistema."'");
        $command->execute();

         }

         //Executa a exclusão da solicitação de transporte
        $model = $this->findModel($id);

        $this->findModel($id)->delete();

        //MENSAGEM DE EXCLUSÃO DA SOLICITAÇÃO DE CONTRATAÇÃO CRIADA COM SUCESSO
            Yii::$app->session->setFlash('success', '<strong>SUCESSO! </strong> A solicitação de Processo Seletivo de código <strong>' .$model->id. '</strong> foi EXCLUÍDA!</strong>');


        return $this->redirect(['index']);
    }

    /**
     * Finds the Contratacao model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Contratacao the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contratacao::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('A página solicitada não existe.');
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
}

