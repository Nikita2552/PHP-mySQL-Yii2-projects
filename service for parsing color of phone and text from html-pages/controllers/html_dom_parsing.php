<?php

namespace app\controllers;

include 'simple_html_dom.php';
include 'cssparser_v2-1.php';
//include './scripts/simplehtmldom/simple_html_dom.php';
//include './scripts/cssparser-2014-08-08/v2.1/cssparser_v2-1.php';
//include 'SRGB_func.php';

class HtmlAndCssParsing {
	
	public $arrayOferrors;
	
	private $html; /* Объект парсинга html-страницы */
	private $e_css; /* Массив со ссылками на css для данной страницы */	
	private $css; /* Объект парсинга css-страницы */
	
	/* конструктор */
	
	public function __construct()
    {
        //echo "<br>I'm alive!<br>";    
    }
	
	/* деструктор */
	
	public function __destruct()
    {
        //echo "<br>I'm dead now :(<br>";
    }
	
	/* Получение со страницы всех адресов css */
	public function getCSSadresses($address) {
		$this->html = file_get_html($address);
		
		if (!file_exists($address)) { /*Если файл не существует, то выходим*/
			$e_href = null;
			$errorLog = array( 0 => 'страницы html не существует', $address);
			array_push($this->arrayOferrors, $errorLog);
			return $e_href;
		}		
		if (!fopen($address, "r")) { /*Если файл не удаётся открыть, то выходим*/
			$e_href = null;
			$errorLog = array( 0 => 'страницу html не удаётся открыть', $address);
			array_push($this->arrayOferrors, $errorLog);
			return $e_href;
		}
		if (!file_get_contents($address)) { /*Если файл пуст, то выходим*/
			$e_href = null;
			$errorLog = array( 0 => 'страница html пуста', $address);
			array_push($this->arrayOferrors, $errorLog);
			return $e_href;
		}

		$this->e_css = $this->html->find('link[type="text/css"]'); /* Находит все теги с ссылками на файлы css */ 

		foreach ($this->e_css as $e_key => $e_element) {
			$e_href[$e_key] = $e_element->href;
		}
		if (!isset($e_href)) {
		$e_href = null;
		}
					
		return $e_href;
	}
	
	public function getCSSObjects($htmlAdress, $e_href) {
		$e_count = count($e_href); /* количество элементов массива */
		$e_number = 0; /* текущий элемент массива */
		$e_key_number = 0; /* текущий элемент массива */
			
		$AllColorAndBack = Null;
			
    	foreach ($e_href as $e_key => $e_element) {
				
			$ColorAndBack = $this->getCSSObject($htmlAdress, $e_element, 2); //???
				
			if ($ColorAndBack) {
				$AllColorAndBack[$e_number] = $ColorAndBack;
				$e_number++;
			
			} /* else {
			$AllColorAndBack[$e_number][0] = $str;
			$AllColorAndBack[$e_number][1] = 'error!';
			} */
			$ColorAndBack = Null;
		}

		return $AllColorAndBack;
	}
	
	public function getSearchFromCSSs($AllColorAndBack) {
		foreach ($AllColorAndBack as $e_key => $e_element) {
			$AllColorAndBack[$e_key] = $this->getSearchFromCSS($e_element);
		}
		
		return $AllColorAndBack;
	
	}
	
	public function genParseHtmlAndCsses($AllColorAndBacks) {
	
		$AllColorAndBacksOut = array();
		
		foreach($AllColorAndBacks as $key => $ColorAndBack) {
				$AllColorAndBack = $this->genParseHtmlAndCss($AllColorAndBack);
				//array_push($AllColorAndBacksOut, $AllColorAndBack);
				$AllColorAndBacksOut[$key] = $AllColorAndBack;
		}
	return $AllColorAndBacksOut;	
		
	}	
	
	public function genParseHtmlAndCss($AllColorAndBack) {
		
		$AllColorAndBackOut = array();
		
		foreach($AllColorAndBack as $ColorAndBack) {
			foreach($ColorAndBack as $ColorAndBack_elements) {
				/* перебор перечисленных элементов - через запятую */
				foreach($ColorAndBack_elements as $ColorAndBack_element) {
					/* перебор элементов, объединённых пробелом */
					foreach ($ColorAndBack_element as $element) {
					//echo $element.'<br>'; 
					array_push($AllColorAndBackOut, $element);	
					}	
				}
			}
		}

	return $AllColorAndBackOut;	
	}	
	
	private function getSearchFromCSS($ColorAndBack) {
		foreach ($ColorAndBack as $key => $element) {
			
			$testPieces = explode(',', $key);		
			$testPiecesNumber = count($testPieces);
		
			for ($i = 0; $i < $testPiecesNumber; $i++) { 	
				$testPieces[$i] = explode(' ', $testPieces[$i]);
			}

			$ColorAndBack[$key] = array();
			$ColorAndBack[$key][0] = $testPieces;
			$ColorAndBack[$key][1] = $element;
		}
		
		return $ColorAndBack;		
	}	
	
	private function getCSSObject($strOfhtml, $strOfcss, $flag_color_back) { /* 2 - ColorAndBack, 1 - ColorNoBack, 0 - NoColorAndBack */
		$this->css = new CSSparser();
		/* Создание абсолютного пути для css */
		$absoluteWay = $this->cutWays($strOfhtml, $strOfcss);
		
		if (!file_exists($absoluteWay)) { /*Если файл не существует, то выходим*/
			$ColorAndBack = Null;
			$errorLog = array( 0 => 'файл css не существует', $absoluteWay);
			array_push($this->arrayOferrors, $errorLog);
			return $ColorAndBack;
		}		
		if (!fopen($absoluteWay, "r")) { /*Если файл не удаётся открыть, то выходим*/
			$ColorAndBack = Null;
			$errorLog = array( 0 => 'файл css не удаётся открыть', $absoluteWay);
			array_push($this->arrayOferrors, $errorLog);
			return $ColorAndBack;
		}
		$css_file = file_get_contents($absoluteWay);
		if ($css_file == false) { /*Если файл пуст, то выходим */
			$ColorAndBack = Null;
			$errorLog = array( 0 => 'файл css пуст', $absoluteWay);
			array_push($this->arrayOferrors, $errorLog);
			return $ColorAndBack;
		}	
		$index = $this->css->ParseCSS($css_file);
		/* Получение всех поддерживаемых типов */
		$media = $this->css->GetMediaList($index);
		/* Пока только для media0 - all - теги для всех отображаемых типов устройств */
		$tree = $this->css->GetCSSArray($index, $media[0]);
		$ColorAndBack = array();
				
	foreach ($tree as $tree_element) {
		/* поиск по ключу */
		$flag_color = array_key_exists('color', $tree_element);
		$flag_background = array_key_exists('background-color', $tree_element);
		$current_key = key($tree); /* Получаем текущий класс */
			
		if ( ($flag_color == true) && ($flag_background == true) && ($flag_color_back == 2) ) {
			
			$color = $tree_element['color'];
			$background = $tree_element['background-color'];
			$str = array();
			array_push($str, $color);
			array_push($str, $background);
			$ColorAndBack[$current_key] = $str;
		
		} else if ( ($flag_color == true) && ($flag_background == false) && ($flag_color_back == 1) ) {

			$color = $tree_element['color'];
			$str = array();
			array_push($str, $color);
			$ColorAndBack[$current_key] = $str;
			
		} else if ( ($flag_color == false) && ($flag_background == true) && ($flag_color_back == 0) ) {

			$background = $tree_element['background-color'];
			$str = array();
			array_push($str, $background);
			$ColorAndBack[$current_key] = $str;
		} 
		next($tree);
	}
	
	return $ColorAndBack;
		
	}

	/* Создание абсолютного пути для css файлов из относительных путей css и абсолютного пути html  */
	private function cutWays($strOfhtml, $strOfcss) {
		$strOfhtmlLen = strlen($strOfhtml);
		$lastSlashNumber = strripos($strOfhtml, '/');
		$strOfhtmlcut = substr($strOfhtml, 0, $lastSlashNumber);
		
		/* Смотрим, стоит ли ../ в начале строки  */
		$strOfcssLen = strlen($strOfcss);
		$firstDotSlashNumber = strpos($strOfcss, '../');
		
		/* Если ../ в строке не существует, то ищем ./, Понимаем, что каталог в строке адреса css дочерний, и складываем две строки */
		if ( $firstDotSlashNumber === false ) {
			
			$firstDotSlashNumber = strpos($strOfcss, './');
			$strOfcsscut = substr($strOfcss, $firstDotSlashNumber + 1, $strOfcssLen);
			$outputstr = $strOfhtmlcut.$strOfcsscut;
		
		/* Если ../ в строке является первым элементом, то перебераем все ../ и идём вверх по иерархии каталогов, строка которой описана в $strOfhtml, вырезая лишние. Потом складываем две обрезанные строки */
		} elseif ( $firstDotSlashNumber == 0 ) {
			$firstDotSlashNumber = 0;
			$flag = true;
			
			while ($flag) {
			$firstDotSlashNumber = strpos($strOfcss, '../');
			$strOfcssLen = strlen($strOfcss);
			if ($firstDotSlashNumber === false) {
				$flag = false;
			} elseif ( $firstDotSlashNumber == 0 ) {
				$strOfcss = substr($strOfcss, $firstDotSlashNumber + 3, $strOfcssLen - 1);
				$lastSlashNumber = strripos($strOfhtmlcut, '/');
				$strOfhtmlcut = substr($strOfhtmlcut, 0, $lastSlashNumber); /* В случае, если ../ больше, чем /, выдаст, что такого файла не существует  */
				}
			}
			$outputstr = $strOfhtmlcut.'/'.$strOfcss;
		}
		return $outputstr;
	}

}

/* Вызов функций */

/*

$address = "K:/Server/data/htdocs/test/web/testsites/Eminem _ Home.html";

$HtmlCss = new HtmlAndCssParsing();

$e_href = $HtmlCss->getCSSadresses($address);

$AllColorAndBack = $HtmlCss->getCSSObjects($e_href);

// Разделение по запятым, чтобы отделить каждый класс в перечислении, и пробелам, чтобы отделить разные ступени вложенных классов
$AllColorAndBack = $HtmlCss->getSearchFromCSSs($AllColorAndBack);

echo "<pre>";
echo "AllColorAndBack: color and back<br>";
print_r($AllColorAndBack);
echo "</pre><br>";

$HtmlCss->genParseHtmlAndCss($AllColorAndBack);

*/

?>