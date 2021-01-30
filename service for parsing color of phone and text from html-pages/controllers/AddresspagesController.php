<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\Pagination;

use app\models\Addressarchives;
use app\models\ContrastForm;
use app\models\Htmlpages;

use yii\data\ActiveDataProvider;

use Yii;
use app\models\UploadForm;
use yii\web\UploadedFile;

class AddresspagesController extends Controller
{
	public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
			'page' => [
				'class' => 'yii\web\ViewAction',
		],
        ];
    }
	
	/*Создания экшена (index) по умолчанию*/
	public function actionIndex($id = null)
    {
        /*Получение id - номера в таблице */
		/*Получение $typeView - тип вида (список страниц, либо страница) - необязательный параметр */
		/* Здесь будет нужно получитиь id, найти все совпадающие с ним строчки в другой таблице и отобразить ссылки на них */
		
		/* Нахождение всех строк таблицы */
		//$query = Htmlpages::find();
		
		/*Нахождение строк только по нужному id*/
		$query = Htmlpages::find()->where(['id_of_archive' => $id]);
		
		/*----- Создание таблицы  */

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
			'pageSize' => 5,
			],
			]);

		/*----- */
		return $this->render('index', [
            'dataProvider' => $dataProvider,
			//'id' => $id,
        ]);
    }
	
	/*
     Создание экшена (view), показывающего скачанную страницу html.
     */
    public function actionView($id = Null)
    {
        //$this->findModel($id)->delete();
		
		//echo $id;	

        return $this->redirect(['addresspageview/index', 'id'=> $id]);
		//return Yii::$app->response->sendFile('K:\Server\data\htdocs\nikita\views\addresspages\pages\mytest.php', ['inline'=>true]);
    }
	
	/*
     Создание экшена (Update), показывающего, какие страницы проверены и находятся в данном архиве.
     */
	 
    public function actionUpdate($id)
    {
        //$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
	
	
	/*
     Создание экшена (Delete), показывающего, какие страницы проверены и находятся в данном архиве.
     */
	 
    public function actionDelete($id)
    {
        //$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
	
	//Загрузка файла
	public function actionUpload()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload()) {
                // file is uploaded successfully
                return;
            }
        }

        return $this->render('upload', ['model' => $model]);
    }
	
	/**
     * Finds the Usertable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Usertable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usertable::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

?>