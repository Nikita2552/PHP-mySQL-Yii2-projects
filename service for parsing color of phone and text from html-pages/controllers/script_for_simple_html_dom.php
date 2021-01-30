<?php

//include './scripts/simple_htmldom/simple_html_dom.php';

require_once 'simple_html_dom.php';
$data = file_get_html('http://xdan.ru');
if($data->innertext!='' and count($data->find('a'))){
  foreach($data->find('a') as $a){
    echo '<a href="http://xdan.ru/'.$a->href.'">'.$a->plaintext.'</a></br>';
  }
}

$data3 = $data->find('html')->children(1)->id;//->children();

function recure_html ($data1) {
	
	//$data2 = $data1->find('html')->children();
	
	//if ($data2)
	//	echo "Есть дочерние элементы!";
	//else
	//	echo "Нет дочерних элементов!";
	
	//foreach($data1->children() as $a){
//    echo '<a href="http://xdan.ru/'.$a->href.'">'.$a->plaintext.'</a></br>';
  // }
}

recure_html ($data);

?>