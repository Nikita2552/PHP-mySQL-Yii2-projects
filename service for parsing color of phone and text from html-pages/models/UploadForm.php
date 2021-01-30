<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $zipArchive;
	public $downloadWay = 'K:/Server/data/htdocs/nikita/views/addressarchives/archives';
	public $unzipWay = 'K:/Server/data/htdocs/nikita/views/addressarchives/folders_of_archives';
	public $zipName;
	public $zipNoFormat;
	public $htmls;
	public $cut;
	

	/* Метод, возвращающий правила обработки данных.
	Обозначает - нужно проверить переменную zipArchive, её тип file, пропустить его, если пуст, со связями 'zip'*/
    public function rules()
    {
        return [
            [['zipArchive'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip'],
        ];
    }
    
	/* Функция загрузки зип-архива на сервер */
    public function upload()
    {
        $this->zipName = $this->zipArchive->baseName . '.' . $this->zipArchive->extension;
		if ($this->validate()) {
            $this->zipArchive->saveAs($this->downloadWay . $this->zipName);
            return true;
        } else {
            return false;
        }
    }
	
	/* Функция распаковки зип-архива */
	public function unzip()
	{

	/* \ - принудительно вызывает ZipArchive из стандартного пространства имён PHP, так как пространство имён у YII собсвенное, и по умолчанию операции происходят только в нём  */
	$zip = new \ZipArchive;
	
	$res = $zip->open($this->downloadWay . $this->zipName);
	
	/*Находим последние вхождения .zip в путях архива*/
	$number = strrpos($this->zipName, '.zip');  //echo " number=".$number."<br>";
	$this->zipNoFormat = substr($this->zipName, 0, $number); //echo " zipNoFormat=".$zipNoFormat."<br>";	
	/*Создать папку*/
	$filename = $this->unzipWay .'/'. $this->zipNoFormat .'/';
	if (!file_exists($filename)) {
		if (!mkdir($filename, 0777, true)) {
			die('Не удалось создать директорию...');
			}
	} 		
	if ($res === TRUE) {
		$zip->extractTo($this->unzipWay .'/'. $this->zipNoFormat .'/');
		$zip->close();
		//echo 'ok';
		return true;
		} else {
		//echo 'failed';	
		return false;
		}
	}
	
	/* Функция распаковки зип-архива */
	public function unzip_alternative()
	{
		exec('K:/Server/optionsprograms/7zip_portable e '.$this->downloadWay . $this->zipName.' -o'.$this->unzipWay .'/'. $this->zipNoFormat .'/');
		
		return true;
	}
	
	/* Функция преобразования кодировки распакованного архива */
	public function uncode()
	{
		/*Создать папку*/
		$filename = $this->unzipWay .'/'. $this->zipNoFormat .'/';
		$files = scandir($filename);
		foreach ($files as $files_el)
		{
			iconv('Windows-1252', 'utf-8', $files_el).PHP_EOL;
		}
		return true;
	}	
	
	/* Создание списка файлов html */
	public function htmllist()
	{
		
	$zip = new \ZipArchive;
	
	//$htmls = array ( array ( 1 => 'test1', 2 => 'testway1'), array ( 1 => 'test2', 2 => 'testway2'));
	
	$res = $zip->open($this->downloadWay . $this->zipName);
	
	//$this->cut[] = '/';
	//$this->cut = array (  0 => '/', 1 => '.html', 2 => '.htmls', );
	
	if ($res === TRUE) {
		for($i = 0; $i < $zip->numFiles; $i++)
		{  
          //echo 'Filename: ' . $zip->getNameIndex($i) . '<br />';
		  $getNI = $zip->getNameIndex($i);
		  $getCount = strlen($getNI);
		  
		  $cutArrayCount = count($this->cut);
		  
		  for ($j = 1; $j < $cutArrayCount; $j++)
		  {
			  /*Находим последние вхождения .html и .htmls в путях архива*/
			  $number = strrpos($getNI, $this->cut[$j]);
			  /*Находим длину проверяемой подстроки с расширением файла страницы*/
			  $cutСount = strlen($this->cut[$j]);
			  /*находим разность между длинной входной строки и длинной последнего вхождения пути .html и .htmls в архиве */
			  $numberLen = $getCount - $number;
			  if ($cutСount == $numberLen) {		
					/*Если нашли, то находим вхождение последнего / */
					$number = strrpos($getNI, $this->cut[0]);
					
					if ($number) {
						$getNAME = substr($getNI, $number + 1);
					} else {
						$getNAME = $getNI;
					}
					/* Добавление элемента в конец массива */
					//array_push($this->htmls, array(0 => "test.zip", 1 => "C:/text") ); //array ( 0 => $getNAME, 1 => $this->unzipWay . $getNI) ); //echo "  ".$htmls[0]."  ".$htmls[1];
					$this->htmls[] = array ( 0 => $getNAME, 1 => $this->unzipWay .'/'. $this->zipNoFormat .'/'. $getNI ); //array(0 => "test.zip", 1 => "C:/text"); //array ( 0 => $getNAME, 1 => $this->unzipWay . $getNI) ); //echo "  ".$htmls[0]."  ".$htmls[1];
			  }
		  }
		} //echo '<pre>'; print_r($this->htmls); echo '</pre>';//$this->htmls = array ( array(0 => "test.zip", 1 => "C:/text"), array(0 => "test.zip", 1 => "C:/text") );
		return true;
	} else 	{
     //echo 'Error reading zip-archive!';
	 return false;
     }
	
	//return $htmls;
	}
	
	/* Парсинг файлов html */
	public function htmlparsing()
	{
	}
}
?>