<?php

namespace app\controllers;

class SRGBclass {
	
	public $arrayOferrors;

	/* конструктор */
	
	public function __construct()
    {
        //echo "I'm alive!<br>";    
    }
	
	/* деструктор */
	
	public function __destruct()
    {
        //echo "I'm dead now :(<br>";
    }
	
	/* высчитать критерий контрастности для текста или фона */
	
	private function SRGBcolor ($color, &$L) {
		
		/* Поправка на длину входного числа и знак # */
		
		$length = strlen($color);
		
		$color = substr($color, 1, $length - 1);
		
		$mod = fmod($length - 1, 2);
		
		if (!$mod) 
			$div = 2;
		else 
			$div = 1;		
		
		$color_array = str_split($color, $div);
		
		//echo '<pre>';
		//print_r($color_array);
		//echo '</pre>';
		
		foreach ($color_array as $color_key => $color_el) {
			
			//echo "color_key=".$color_key."; color_el=".$color_el."<br>";

			/* hexdec - перевод из шестнадцатеричной системы в двоичную */			
			$color_dec = hexdec($color_el)/255; //echo $shr.'<br>';
			
			if ($color_dec <= 0.0328) {
				$color_dec = $color_dec/12.98;
			} else {
				$color_dec = pow($color_dec + 0.055, 2.4)/1.055;
			}
			
			$color_dec_array[$color_key] = $color_dec;		
		}
		
		$L = 0.2126 * $color_dec_array[0] + 0.7152 * $color_dec_array[1] + 0.0722 * $color_dec_array[2];
		
		//echo 'L='.$L.'<br>';
	}
	
	/* высчитать разницу между критериями контрастности для текста и фона */
		
	private function SRGBdelta ($color, $backgroundcolor, &$delta) {
		
		$L1 = 0;
		$L2 = 0;
		
		/*
		Для фона и текста, обозначения вместо цифр фона:
		
		background-color: <цвет> | transparent | inherit
		tranceparent - прозрачный
		inherit - наследует фон родителя
		*/
		
		$answer1 = $this->verifyColor($color);
		$answer2 = $this->verifyColor($color);
		
		if ( ($answer1) && ($answer2) ) { 
		
		$this->SRGBcolor ($color, $L1);
		$this->SRGBcolor ($backgroundcolor, $L2);
		}		
		
		/*В случае несоответствия одного из вводимых чисел шаблону, сравнения не происходит и в базу данных записываются все 0, то есть контрастность слишком мала */
		
		$delta = ($L1 + 0.05)/($L2 + 0.05);	    		
	}
	
	/* выдать результат в зависимости от величины разницы контрастностей */

	public function compareColorAndBack ($color, $backgroundcolor, &$answer) {
		
		/* Порог */
		$threshold = array
			(
			1 => 3,
			2 => 4.5,
			3 => 7
			);
		/* Тип ответа */	
		$answer_type = array
			(
			1 => 'с хорошим зрением',
			2 => 'с дефектами зрения',
			3 => 'с потерей контрастной чувствительности'
			);
		
		$delta = 0;
		
		$this->SRGBdelta ($color, $backgroundcolor, $delta);

		$conteiner ='delta = '.$delta.'<br>';
		
		$flag_contrast = true; /* Контраст значителен (больше 1)*/
				
		foreach ($threshold as $threshold_key => $threshold_element) {
			
			if ($delta <= $threshold_element) {
				$conteiner .= $threshold_key.'. Контрастность слишком мала для людей '.$answer_type[$threshold_key].'!<br>';
				if ($threshold_key == 1) {
					$flag_contrast = false;
				}	
			} else {
				$conteiner .= ' контрастность для людей '.$answer_type[threshold_key].' достаточна!<br>';	
			}
		}

		$answer = $conteiner;

		return $flag_contrast; /* Флаг показывает, значителен ли контраст */
	}
	
	/* выдать результат в зависимости от величины разницы контрастностей, пригодный для записи в базу данных */

	public function compareColorAndBackForDataBase ($color, $backgroundcolor, &$answer) {
		
		/* Порог */
		$threshold = array
			(
			1 => 3,
			2 => 4.5,
			3 => 7
			);
		/* Тип ответа */	
		$answer_type = array
			(
			1 => 'с хорошим зрением',
			2 => 'с дефектами зрения',
			3 => 'с потерей контрастной чувствительности'
			);
		
		$delta = 0;
		
		$this->SRGBdelta ($color, $backgroundcolor, $delta);

		//$conteiner ='delta = '.$delta.'<br>';
		$conteiner = null;
		
		$flag_contrast = true; /* Контраст значителен (больше 1)*/
				
		foreach ($threshold as $threshold_key => $threshold_element) {
			
			if ($delta <= $threshold_element) {
				$conteiner[ $answer_type[$threshold_key] ] = 0; /* Контрастность слишком мала */
				if ($threshold_key == 1) {
					$flag_contrast = false;
				}	
			} else {
				$conteiner[ $answer_type[$threshold_key] ] = 1;  /* Контрастность достаточна */
			}
		}
		
		$conteiner[ 'delta' ] = $delta;

		$answer = $conteiner;

		//return $flag_contrast; /* Флаг показывает, значителен ли контраст */
	}
	/* Проверка на соответствие шаблону шестнадцатеричного числа */
	public function verifyColor($color) {
			$verify = '((#[0-9a-fA-F]{6})|(#[0-9a-fA-F]{3}))';
			$answer = preg_match($verify, $color);			
			
			return $answer;		
	}	
}

/*

$color = '333333';
$backgroundcolor = 'FFFFFF';

$sRGBcl = new SRGBclass();

$flag = $sRGBcl->compareColorAndBack ($color, $backgroundcolor, $answer);

echo $answer;

if (!$flag) {
	echo '<< Контрастность слишком незначительна. Текст сливается! >> <br>';
}

*/

?>
