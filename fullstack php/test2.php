<?php

/**
 * @charset UTF-8
 *
 * Задание 2. Работа с массивами и строками.
 *
 * Есть список временных интервалов (интервалы записаны в формате чч:мм-чч:мм).
 *
 * Необходимо написать две функции:
 *
 *
 * Первая функция должна проверять временной интервал на валидность
 * 	принимать она будет один параметр: временной интервал (строка в формате чч:мм-чч:мм)
 * 	возвращать boolean
 *
 *
 * Вторая функция должна проверять "наложение интервалов" при попытке добавить новый интервал в список существующих
 * 	принимать она будет один параметр: временной интервал (строка в формате чч:мм-чч:мм). Учесть переход времени на следующий день
 *  возвращать boolean
 *
 *  "наложение интервалов" - это когда в промежутке между началом и окончанием одного интервала,
 *   встречается начало, окончание или то и другое одновременно, другого интервала
 *
 *
 *
 *  пример:
 *
 *  есть интервалы
 *  	"10:00-14:00"
 *  	"16:00-20:00"
 *
 *  пытаемся добавить еще один интервал
 *  	"09:00-11:00" => произошло наложение
 *  	"11:00-13:00" => произошло наложение
 *  	"14:00-16:00" => наложения нет
 *  	"14:00-17:00" => произошло наложение
 */
 
 # Можно использовать список:

$list = array (
	'09:00-11:00',
	'11:00-13:00',
	'15:00-16:00',
	'17:00-20:00',
	'20:30-21:30',
	'21:30-22:30',
	'23:00-01:00',
);

echo '<p>This is test2</p>';

function splittime($current)
{
	$answer = array();
	$values = explode('-',$current);
	foreach($values as $value)
	{
		$tmpvalues = explode(':', $value);
		foreach($tmpvalues as $tmpvalue)
			array_push($answer, (int)$tmpvalue);	
	}
	return $answer;
}

function importtomin($hour, $min)
{
	return ($hour*60+$min);
}

function validate($str)
{
	$numbers = splittime($str);
	if (((($numbers[0] >= 0) && ($numbers[0] < 24))
		&& (($numbers[1] >= 0) && ($numbers[1] < 60)))
		&& ((($numbers[2] >= 0) && ($numbers[2] < 24))
		&& (($numbers[3] >= 0) && ($numbers[3] < 60))))
		return true;
	else
		return false;
}

function importtoarray($period)
{
	$numbers = splittime($period);	
	$start = importtomin($numbers[0], $numbers[1]);
	$finish = importtomin($numbers[2], $numbers[3]);
	return array($start, $finish); 
}

function compare($period0, $period1)
{	
	$arrayperiod0 = importtoarray($period0);
	$arrayperiod1 = importtoarray($period1);
	
	if ($arrayperiod0[0] < $arrayperiod0[1])
	{
		if ((($arrayperiod1[0] < $arrayperiod0[0]) && ($arrayperiod1[1] < $arrayperiod0[0]))
		xor (($arrayperiod1[0] > $arrayperiod0[1]) && ($arrayperiod1[1] > $arrayperiod0[1])))
			return true;
		else
			return false;
	}
	else
	{
		if (($arrayperiod1[0] > $arrayperiod0[1]) && ($arrayperiod1[1] < $arrayperiod0[0]))
			return true;
		else
			return false;
	}
}

function echovalidate()
{
	global $list;
	foreach($list as $element)
	{
		echo '"'.$element.'"';
		if (validate($element))
			echo ' => написано верно<br>';
		else
			echo ' => написано не верно<br>';
	}
}

function echocompare($compareperiod)
{
	global $list;
	foreach($list as $element)
	{
		echo '"'.$element.'"';
		if (compare($element, $compareperiod))
			echo ' => наложения нет<br>';
		else
			echo ' => произошло наложение<br>';
	}
}

echovalidate();
echo "<br>";

$str = '15:30-17:30';
echo "<br>".$str.'<br>';
echocompare($str);
echo "<br>";

$str = '00:30-03:00';
echo "<br>".$str.'<br>';
echocompare($str);
 
?>