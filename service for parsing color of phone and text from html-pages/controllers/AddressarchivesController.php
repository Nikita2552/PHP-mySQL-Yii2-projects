<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\Pagination;

use app\models\Addressarchives;
use app\models\ContrastForm;
use app\models\Htmlpages;
use app\models\Textbackgroundcolors;
use app\models\Textbackgroundclasses;

use yii\data\ActiveDataProvider;

use Yii;
use app\models\UploadForm;
use yii\web\UploadedFile;

include 'html_dom_parsing.php';
include 'SRGB_func.php';
//include 'testingClass.php';

class AddressarchivesController extends Controller
{
	/*Создания экшена (index) по умолчанию*/
	public function actionIndex()
    {
 
		/* Создание формы для загрузки (с зелёной кнопкой) */
		$form_model = new ContrastForm();
		
		/*----- */
		
		/* Нахождение всех строк таблицы */
		$query = Addressarchives::find();
		
		/*----- Создание списка  */

        /* Объявление разбивки на страницы. Размер стр - 5, количество строк в таблице получаем по count() */
		/* $pagination - выдача одновременно только одной страницы данных, в зависимости от выбранной страницы */
		$pagination = new Pagination([
            'defaultPageSize' => 5,
            'totalCount' => $query->count(),
        ]);
		
		/* Сортировка по столбцу address_archive*/
        $addresses = $query->orderBy('address_archive')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
			
		/*----- */

		/*----- Создание таблицы  */

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
			'pageSize' => 5,
			],
			]);

		/*----- */

		/* Создание формы для загрузки (со светлыми кнопками) */
		$up_model = new UploadForm();

        /* isPost - если поступил POST запрос с кнопки формы, то проделываем следующие операции: загрузка файла на сервер через вызов метода upload() модели формы UploadForm */
		if (Yii::$app->request->isPost) {
			/* Формирование данных для таблиц Addressarchives и Htmlpages */
            $up_model->zipArchive = UploadedFile::getInstance($up_model, 'zipArchive');
			
			$flag_upload = $up_model->upload();
			$flag_unzip = $up_model->unzip();
			
			/* htmls - архив список с названиями и адресами файлов */
			$up_model->cut = array (  0 => '/', 1 => '.html', 2 => '.htmls', );
			$flag_htmls = $up_model->htmllist();
			
			/* Формирование данных для таблиц textbackgroundclasses и textbackgroundcolors */
			$HtmlCss = new HtmlAndCssParsing();
			
			$ArrayOfErrors = array(); /* Отправка ссылки на ошибки */
			$HtmlCss->arrayOferrors = &$ArrayOfErrors;
			
			//$str = 'K:/Server/data/htdocs/nikita/views/addressarchives/folders_of_archives/';
			//$str = $str.$up_model->zipNoFormat.'/';
			$AllColorAndBacks = Null;
			
			$test1 = array();
			$test2 = array();
			$test3 = array();
			
			$htmls_count = count($up_model->htmls); //количество найденных файлов подходящего формата. В тестовом архиве - 7
			
			for($i = 0; $i < $htmls_count; $i++)
			{
				$e_href = $HtmlCss->getCSSadresses($up_model->htmls[$i][1]);

				/*
				1. Решить проблемы с кодировкой раскрываемого файла. - html, ссылки на которые не работают, будут пропускаться. В данном случае +
				2. Подумать о выводе ошибок и их показе на странице. +
				3. Подумать об обработке отказа с пустыми страницами. +
				4. Вывести список страниц. +
				*/
				
				$test1[$i] = $up_model->htmls[$i][1];
				
				if (isset($e_href)) {					
					$test2[$i] = $e_href; /* тестовый e_href */
					$AllColorAndBack = $HtmlCss->getCSSObjects($up_model->htmls[$i][1], $e_href); //???
					
					if (isset($AllColorAndBack)) {
						$test3[$i] = $AllColorAndBack; //На сложной тестовой странице работает
						// Разделение по запятым, чтобы отделить каждый класс в перечислении, и пробелам, чтобы отделить разные ступени вложенных классов
						$AllColorAndBacks[$i] = $HtmlCss->getSearchFromCSSs($AllColorAndBack);
						//$test3[$i] = $AllColorAndBacks[$i] ;
					}				
				} 
			}
			
			/*Вставка данных в таблицы*/
			/*Вставка данных в таблицу Addressarchives*/
			$addarch = new Addressarchives();
			/* Создание индивидуального ключа */
			$flag_primary_key = true;
			
			while ($flag_primary_key) {
				$addarch_primary_key= rand(0, 100000);
				
				$query = $addarch->find()->where(['id_archive' => $addarch_primary_key])->one();
				if (!$query) { // Если такого ключа нет, то берём его и завершаем цикл
					$flag_primary_key = false;
				}
			}
	
			$single_table = 'Addressarchives';
			$single_row = [
				'id_archive' => $addarch_primary_key, //Позже использовать для update строки в ячейке флага
				'address_archive' => $up_model->zipName,
				'address_folder' => $up_model->downloadWay,
				];
			Yii::$app->db->createCommand()->insert($single_table, $single_row)->execute();
			
			/* Вставка данных в таблицу Htmlpages */
			$htmlp = new Htmlpages();
			$htmlp_count = $htmlp->find()->count() + 2; //Количество строк в заполянемой таблице + 1, то есть номер первой несуществующей строки

			$multiple_table = 'Htmlpages';
			$multiple_columns = ['id_page', 'address_page', 'id_of_archive'];
			$multiple_rows;
			for($i = 0; $i < $htmls_count; $i++)
			{
				$multiple_rows[] = [$htmlp_count + $i, $up_model->htmls[$i][1], $addarch_primary_key];
				$FortextbackgroundclassesTable[$i] = $htmlp_count + $i;
			}
			Yii::$app->db->createCommand()->batchInsert($multiple_table, $multiple_columns, $multiple_rows)->execute();
			
			/*Вставка данных в таблицу textbackgroundcolors*/
			$textBackColors = new Textbackgroundcolors();
			$colorsp_count = $textBackColors->find()->count() + 2;
			$colorsp_i = 0;
			
			$colorsp_table = 'Textbackgroundcolors';
			$colorsp_columns = ['id_text_back', 'text_color', 'back_color', 'delta', 'flag_of_colors_good', 'flag_of_colors_def', 'flag_of_colors_contr'];
			$colorsp_rows;
			
			/*Вставка данных в таблицу textbackgroundclasses*/
			$textBackClasses = new Textbackgroundclasses();
			$classesp_count = $textBackClasses->find()->count() + 2;
			$classesp_i = 0;
			
			$classesp_table = 'Textbackgroundclasses';
			$classesp_columns = ['id_class', 'class', 'id_text_back', 'id_page'];
			$classesp_rows;
			
			/*Обновление данных в таблице htmppages*/
			//$htmlpages_table = 'HtmlPages';
			//$htmlpages_columns = ['id_class', 'class', 'id_text_back', 'id_page'];
			//$htmlpages_rows;
			
			$RGBforcompare = new SRGBclass();
			//$RGBforcompare->arrayOferrors = &$ArrayOfErrors;
			
			$parentsFlag = 1;
			
			if (isset($AllColorAndBacks)) {
			
				//$ArrayOfErrors = array(); /* текстовый */
				foreach ($AllColorAndBacks as $htmlKey => $AllColorAndBack) { /*htmlKey - цифровой ключ, соответствующий ключу i в массиве $up_model->htmls[$i][1] */
					foreach ($AllColorAndBack as $ColorAndBack) {
						/* уровень перечисления строк селекторов */
						foreach ($ColorAndBack as $ColorAndBack_element) {
						/* уровень перечисления массивов 0 - цвет, 1 - фон, 2 - одиночный селектор */
						//array_push($ArrayOfErrors, array( 0 => $ColorAndBack_element[1][0], 1 => $ColorAndBack_element[1][1], 2 => $ColorAndBack_element[0][0][0],) );
						$answer = null;
						$RGBforcompare->compareColorAndBackForDataBase($ColorAndBack_element[1][0], $ColorAndBack_element[1][1], $answer);
						$colorsp_rows[] = [$colorsp_count + $colorsp_i, $ColorAndBack_element[1][0], $ColorAndBack_element[1][1], $answer['delta'], $answer['с хорошим зрением'], $answer['с дефектами зрения'], $answer['с дефектами зрения']];
						$colorsp_i++;

						/*Адаптация сформированных данных для вставки в таблицу textbackgroundclasses*/
						//$ColorAndBack_element[1] - нас интересует этот массив
						foreach ($ColorAndBack_element[0][0] as $element) {
							$htmlArchivePrimary = $FortextbackgroundclassesTable[$htmlKey]; //Индивидуальные ключи в таблице HtmlPages. Так как лишние html-страницы, которых не сущетсвует, ещё не переберались, они там есть.
							$classesp_rows[] = [$classesp_count + $classesp_i, $element, $colorsp_count + $colorsp_i, $htmlArchivePrimary];   
							$classesp_i++;
							}
						/* Проверка, есть ли в массиве флагов прохождения барьеров контрастности хоть одна 1, и если да, то выставление
						в ячейки строки таблицы HtmlPages флага 0 (страница) и в таблице AddressArchives флага 0 (архив)*/
						$parentsFlag = in_array(0,$answer);
						
						if ($parentsFlag) {
							//Исправяем таблицу HtmlPages
							$htmlUpdate = $htmlp->findOne($htmlArchivePrimary);
							$htmlUpdate['flag_of_page'] = 0;
							$htmlUpdate->update();
							}						
						}					
					}
				}
				Yii::$app->db->createCommand()->batchInsert($colorsp_table, $colorsp_columns, $colorsp_rows)->execute();
				Yii::$app->db->createCommand()->batchInsert($classesp_table, $classesp_columns, $classesp_rows)->execute();
				
				if ($parentsFlag) {
							//Исправяем таблицу AddressArchives
							$htmlUpdate = $addarch->findOne($addarch_primary_key);
							$htmlUpdate['flag_of_archive'] = 0;
							$htmlUpdate->update();
							}
			}

			if ($flag_upload & $flag_unzip & $flag_htmls) {
                // file is uploaded successfully
                return $this->render('index', [
					'form_model' => $form_model,
					'addresses' => $addresses,
					'pagination' => $pagination,
					'dataProvider' => $dataProvider,
					'up_model' => $up_model,
					'AllColorAndBacks' => $AllColorAndBacks,
					'ArrayOfErrors' => $ArrayOfErrors,
					'test1' => $test1,
					'test2' => $test2,
					'test3' => $test3
				]);
            }
        }
		
		/*----- */
		$AllColorAndBacks = Null; /*тестовый*/
		$ArrayOfErrors = Null; /*тестовый*/
		$test1 = Null;
		$test2 = Null;
		$test3 = Null;		

		/* Выдача во view (index) информации, которая будет показываться в списке и таблице */
        return $this->render('index', [
			'form_model' => $form_model,
            'addresses' => $addresses,
            'pagination' => $pagination,
			'dataProvider' => $dataProvider,
			'up_model' => $up_model,
			'AllColorAndBacks' => $AllColorAndBacks,
			'ArrayOfErrors' => $ArrayOfErrors,
			'test1' => $test1,
			'test2' => $test2,
			'test3' => $test3
        ]);
    }
	
	/*
     Создание экшена (view), показывающего, какие страницы проверены и находятся в данном архиве.
     */
    public function actionView($id)
    {
 		return $this->redirect(['addresspages/index', 'id'=> $id]);
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