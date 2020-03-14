<?php
	include_once('B24Deal.php');
	$b24Deal = new B24Deal();
	$b24Deal->url = ''; //How to create webhoock key ($b24Deal->url )   https://helpdesk.bitrix24.ru/open/5408147/
	$b24Deal->order = array(
		'ORDER_ID' => 3530,
		'PRICE' => 23.50,
		'DESCRIPTION' => 'Транспортная компания GTD',
		'FIO' => 'Шобанова Надежда Валериевна',
		'EMAIL' => 'nadushka1505@mail.ru',
		'PHONE' => '+7-902-580-24-78',
		'ITEMS' => Array
        (
            Array
                (
                    'NAME' => 'Полотенце 35х60, КУПОН, вафельное полотно, 100 % хлопок, "Кофе"',
                    'QUANTITY' => 1,
                    'PRICE' => 23.50
                )

        )
	);
	$b24Deal->addB24ProductsToDeal();


?>
