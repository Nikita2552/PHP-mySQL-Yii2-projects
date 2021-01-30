<?php

/*
	Пространство имён, определяет, для какой группы функций
	будет доступна эта модель.
	
	models - пространство имён моделей (классов баз данных)
 
 */

namespace app\models;

/*

Объявление, от какого класса наследовать наш класс. 

*/

use yii\db\ActiveRecord;

/*

Объявление класса, соответствующего имени таблицы в базе данных.
Связывание таблицы с этиим классом будет выполнено автоматически.

*/

class Addressarchives extends ActiveRecord
{
	
/*
rules - правила валидации, используются при проверке данных.
Также могут использоваться переопределения:
attributes()
attributeLabels()
Также могут использоваться объявления сценариев для больших проектов.

атрибуты rules:
required - поле обязательно для заполнения
trim - удаление пробелов из поля
message => "любой текст" - показ сообщения при неправильном вводе данного
when => function() - условная валидация. Можно проверять соответствие аттрибута какому то условию.
default - обработка пустого входного поля.
*/
	public function rules()
	{
		return [
			// атрибут required указывает, что данные обязательны для заполнения
			[['id_archive', 'address_archive', 'address_folder'], 'required'],
		];
	}

}

?>