<?php

namespace app\controllers\contratacao;

use Yii;
use app\models\contratacao\Contratacao;
use app\models\contratacao\ContratacaoJustificativas;
use app\models\contratacao\ContratacaoJustificativasSearch;
use app\models\contratacao\Emailusuario;
use app\models\contratacao\EmailusuarioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ContratacaoJustificativasController implements the CRUD actions for ContratacaoJustificativas model.
 */
class ContratacaoJustificativasPendentesController extends Controller
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
     * Lists all ContratacaoJustificativas models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $searchModel = new ContratacaoJustificativasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionObservacoes()
    {
        $searchModel = new ContratacaoJustificativasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('observacoes', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ContratacaoJustificativas model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $session = Yii::$app->session;
        //VERIFICA SE O COLABORADOR FAZ É GERENTE PARA REALIZAR A SOLICITAÇÃO
        if($session['sess_responsavelsetor'] == 0 && $session['sess_coddepartamento'] != 82){
            return $this->AccessoAdministrador();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ContratacaoJustificativas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    
    public function actionCreate()
    {
        $session = Yii::$app->session;
        //VERIFICA SE O COLABORADOR FAZ É GERENTE PARA REALIZAR A SOLICITAÇÃO
        if($session['sess_responsavelsetor'] == 0 && $session['sess_coddepartamento'] != 82){
            return $this->AccessoAdministrador();
        }

        $model = new ContratacaoJustificativas();

        $model->id_contratacao = $session['sess_contratacao'];
        $model->usuario = $session['sess_nomeusuario'];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {


            //envia para correção a contratação que está em recebido pelo GRH
    $sql_contratacao = "SELECT * FROM contratacao WHERE id = '".$model->id_contratacao."' ";

     $contratacao = Contratacao::findBySql($sql_contratacao)->one(); 

     $connection = Yii::$app->db;
     $command = $connection->createCommand(
     "UPDATE `db_processos`.`contratacao` SET `situacao_id` = '2' WHERE `id` = '".$contratacao->id."'");
     $command->execute();

     $contratacao->situacao_id = 2;
     if($contratacao->situacao_id == 2){

         //ENVIANDO EMAIL PARA O GERENTE INFORMANDO SOBRE O PROCESSO  DE CONTRATAÇÃO QUE FOI ENVIADO PARA CORREÇÃO
          $sql_email = "SELECT emus_email FROM emailusuario_emus, colaborador_col, responsavelambiente_ream WHERE ream_codunidade = '".$contratacao->cod_unidade_solic."' AND ream_codcolaborador = col_codcolaborador AND col_codusuario = emus_codusuario";
      
              $email_solicitacao = Emailusuario::findBySql($sql_email)->all(); 
              foreach ($email_solicitacao as $email)
                  {
                    $email_gerente  = $email["emus_email"];

                                    Yii::$app->mailer->compose()
                                    ->setFrom(['contratacao@am.senac.br' => 'Contratação - Senac AM'])
                                    ->setTo($email_gerente)
                                    ->setSubject('Solicitação de Contratação '.$contratacao->id.' - ' . $contratacao->situacao->descricao)
                                    ->setTextBody('A solicitação de contratação de código: '.$contratacao->id.' está com status de '.$contratacao->situacao->descricao.' ')
                                    ->setHtmlBody('<h4>Prezado(a) Gerente, <br><br>Existe uma solicitação de contratação de <strong style="color: #337ab7"">código: '.$contratacao->id.'</strong> com status de '.$contratacao->situacao->descricao.'. <br> Por favor, não responda esse e-mail. Acesse https://portalsenac.am.senac.br para ANALISAR a solicitação de contratação. <br><br> Atenciosamente, <br> Contratação de Pessoal - Senac AM.</h4>')
                                    ->send();
                 } 
        }

         //MENSAGEM DE CONFIRMAÇÃO DA SOLICITAÇÃO DE CONTRATAÇÃO ENVIADA PARA CORRECAO  
         Yii::$app->session->setFlash('info', '<b>SUCESSO!</b> A solicitação de Contratação foi ENVIADA PARA CORREÇÃO.</b>');

            return $this->redirect(['contratacao/contratacao-pendente/index']);
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ContratacaoJustificativas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $session = Yii::$app->session;
        //VERIFICA SE O COLABORADOR FAZ É GERENTE PARA REALIZAR A SOLICITAÇÃO
        if($session['sess_responsavelsetor'] == 0 && $session['sess_coddepartamento'] != 82){
            return $this->AccessoAdministrador();
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ContratacaoJustificativas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ContratacaoJustificativas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContratacaoJustificativas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContratacaoJustificativas::findOne($id)) !== null) {
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

    public function AccessoAdministrador()
    {
            $this->layout = 'main-acesso-negado';
            return $this->render('/site/acesso_negado');
    }
}
