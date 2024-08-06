<?php

function genTree($input, $output) {

    $file = new genTree(); //создаем объект дерева
    $file -> input = $input; //путь входящего файла
    $file -> output = $output; //путь сформированного файла
    $file -> generate(); //запуск формирования файла

};