<?php

### Код для примера

function tokenize($array) {
	@$array['tokens'] = explode(' ', $array['name']);
	return $array;
}

###

/*
  Минимально необходимый набор полей
  Также в каждом объекте должен быть ключ tokens,
  содержимое которого должно представлять собой массив строк,
  полученных путём разбиения ключа name по символу пробела (см. функцию tokenize)
*/


$data = array(
	array(
		'id' => 1,
		'link' => '',
		'image' => 'images/temp/photo_66-10.jpg',
		'category' => 'Стулья',
		'name' => 'Chair Eames DSW dark beech',
		'stickers' => array('sale', 'new'),
		'sale' => '10',
		'price' => '3 900',
	),
	array(
		'id' => 2,
		'link' => '',
		'image' => 'images/temp/photo_66-11.png',
		'category' => 'Столы',
		'name' => 'Детский стол Eiffel Wood',
		'sale' => '10',
		'price' => '6 500',
	),
	array(
		'id' => 3,
		'link' => '',
		'image' => 'images/temp/photo_66-12.png',
		'category' => 'Освещение',
		'name' => 'Торшер Arco Round',
		'price' => '11 360',
	)
);

### Код для примера

$result = array();
$resultsCount = mt_rand(0, 4);

$i = 0;
while ($i++ < $resultsCount) {
	$result[] = tokenize($data[mt_rand(0, (count($data) - 1))]);
}

###

# Отдаём данные клиенту

echo json_encode($result);
