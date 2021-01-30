<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\Pagination;

use app\models\Addressarchives;
use app\models\ContrastForm;
use app\models\Htmlpages;
use app\models\Textbackgroundclasses;

use yii\data\ActiveDataProvider;

use Yii;
use app\models\UploadForm;
use yii\web\UploadedFile;

class AddresspageviewController extends Controller
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
		/* Здесь будет нужно получитиь id, найти все совпадающие с ним строчки в другой таблице и отобразить ссылки на них */
		/* Соединение таблиц по id */
		//$query = Textbackgroundclasses::find()->where(['id_page' => $id])->join('RIGHT JOIN', 'textbackgroundcolors', 'textbackgroundcolors.id_text_back = textbackgroundclasses.id_text_back');
		
		/* Запрос выбора с соединением двух связанных таблиц */
		
		$query = (new \yii\db\Query())
				->select(['id_class', 'class', 'text_color', 'back_color', 'delta', 'flag_of_colors_good', 'flag_of_colors_def', 'flag_of_colors_contr'])
				->from('textbackgroundclasses')
				->join('RIGHT JOIN', 'textbackgroundcolors', 'textbackgroundclasses.id_text_back = textbackgroundcolors.id_text_back')
				->where(['id_page' => $id])
				->all();
				
		/* Находим id_архива для возврата */
		
		$queryBack = (new \yii\db\Query())
				->select(['id_of_archive', ])
				->from('htmlpages')
				->where(['id_page' => $id])
				->one();
		
		/*
		$query = Textbackgroundclasses::find()
			->select(['id_class', 'class', 'text_color', 'back_color', 'delta', 'flag_of_colors_good', 'flag_of_colors_def', 'flag_of_colors_contr'])
			->from('textbackgroundclasses')
			->join('RIGHT JOIN', 'textbackgroundcolors', 'textbackgroundclasses.id_text_back = textbackgroundcolors.id_text_back')
			->where(['id_page' => $id])
			->all();
		*/
		
		//$query
		/*
		$address = $query->address_page;
		
		$buffer = array();
		
		$handle = fopen($address, "r");
		while (!feof($handle)) {
			//$buffer = fgets($handle, 4096);
			array_push($buffer, fgets($handle, 4096) ); 
			//echo $buffer;
		}
		fclose($handle);
		
		$countBuffer = count($buffer);		
		*/
		
		/* Нахождение всех строк таблицы */
		//$query = Htmlpages::find();
		
		/*----- Создание таблицы  */

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
			'pageSize' => 20,
			],
			]);

		/*----- */
		return $this->render('index', [
            'dataProvider' => $dataProvider,
			'query' => $query,
			'queryBack' => $queryBack,
			//'id' => $id,
			//'buffer' => $buffer, 'countBuffer' => $countBuffer//, 'address' => $address, 'query' => $query 
        ]);
    }
	
	/*
     Создание экшена (view), показывающего скачанную страницу html.
     */
    public function actionView($id = Null)
    {
 	
        return $this->redirect(['index']);
		
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