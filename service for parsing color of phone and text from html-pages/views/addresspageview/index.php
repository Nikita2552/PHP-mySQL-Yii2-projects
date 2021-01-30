<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

use yii\grid\GridView;
use yii\widgets\ActiveForm;
?>
<h1>Инструкция для работы с сервисом:</h1>
<ul>
<li>Вы находитесь на странице, где расположены распознанные селектора.</li>
<li>В этой таблице:</li>
<li>Число delta: - коэффициент контрастности, вычисляемый алгоритмом, согласно стандарту ... .</li>
<li>Флаг Flag Of Colors Good: 1 - контрастность данного селектора достаточна для людей с хорошим зрением.</li>
<li>Флаг Flag Of Colors Def: 1 - контрастность данного селектора достаточна для людей с дефекатами зрения (близорукость, дальнозоркость).</li>
<li>Флаг Flag Of Colors Contr: 1 - контрастность данного селектора достаточна для людей с нарушением восприятия контрастности.</li>
<br>Если страница пуста, то в загруженной странице html не найдены селекторы с плохой контрастностью. Это обозначает, что, либо, их там нет, либо возможности алгоритма пока не позволяют
их распознать. Обратите внимание на ограничения, список которых приведён внизу начальной страницы.</li>
</ul><br>
<h1>Таблица селекторов выбранной html-страницы:</h1>
<div>
	<table class='table table-striped table-bordered table-hover'>
		<thead>
			<td>№</td>
			<td>class</td>
			<td>text color</td>
			<td>back color</td>
			<td>delta</td>
			<td>flag of colors good</td>
			<td>flag of colors def</td>
			<td>flag of colors contr</td>
		</thead>	
	
	<?php

	if (isset($query)) {
		$i = 1;
		foreach ($query as $column) {
			//echo '<tr><td>'.$column['id_class'].'</td>';
			echo '<tr><td>'.$i.'</td>';
			echo '<td>'.$column['class'].'</td>';
			echo '<td>'.$column['text_color'].'</td>';
			echo '<td>'.$column['back_color'].'</td>';
			echo '<td>'.$column['delta'].'</td>';
			echo '<td>'.$column['flag_of_colors_good'].'</td>';
			echo '<td>'.$column['flag_of_colors_def'].'</td>';
			echo '<td>'.$column['flag_of_colors_contr'].'</td></tr>';
			$i++;
		}
	}
	
	?>			
	</table>
</div>
<?= Html::a('Назад', ['/addresspages/index', 'id' => $queryBack['id_of_archive']], ['class'=>'btn btn-primary']) ?>
<?php
/*
echo '<pre>';
print_r($query[0]);
print_r($queryBack);
echo '</pre>';
*/	
?>
