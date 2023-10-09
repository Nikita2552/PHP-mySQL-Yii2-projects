<?php

/**
 * @charset UTF-8
 *
 * Задание 3
 * В данный момент компания X работает с двумя перевозчиками
 * 1. Почта России
 * 2. DHL
 * У каждого перевозчика своя формула расчета стоимости доставки посылки
 * Почта России до 10 кг берет 100 руб, все что cвыше 10 кг берет 1000 руб
 * DHL за каждый 1 кг берет 100 руб
 * Задача:
 * Необходимо описать архитектуру на php из методов или классов для работы с
 * перевозчиками на предмет получения стоимости доставки по каждому из указанных
 * перевозчиков, согласно данным формулам.
 * При разработке нужно учесть, что количество перевозчиков со временем может
 * возрасти. И делать расчет для новых перевозчиков будут уже другие программисты.
 * Поэтому необходимо построить архитектуру так, чтобы максимально минимизировать
 * ошибки программиста, который будет в дальнейшем делать расчет для нового
 * перевозчика, а также того, кто будет пользоваться данным архитектурным решением.
 *
 */

# Использовать данные:
# любые

echo '<p>This is test3</p>';

include 'test3classes.php'; 

echo '<form action="test3.php" method="POST"><select name="myMailSelect">';
foreach ($mails as $key => $value)
{
	echo '<option value="'.$key.'">'.$value.'</option>';
}
echo '</select><br>Введите вес:<br><input name="myWeightName" type="text" value="" /> 
    <input name="myActionName" type="submit" value="Выполнить" /></form>';

    if (isset($_POST['myActionName']))
    {
  		if (is_numeric($_POST['myWeightName']) && ((int)$_POST['myWeightName'] >= 0))		
		{
			$factorymail = new FactoryMails();
			$currentmail = $factorymail->getMail((int)$_POST['myWeightName'], $_POST['myMailSelect']);
			echo 'Итоговая стоимость при работе с перевозчиком "'.$mails[$_POST['myMailSelect']].'": '.$currentmail->getcost().' руб.<br>';
		}
		else
			echo 'Ошибка. Введите количество правильно';		
    }
?>