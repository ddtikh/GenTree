<?php
//считываем параметры из коммандной строки
$cli = getopt('',array('input:','output:','compare:'));

//проверяем существование входящего файла
if (!file_exists($cli['input'])) {
    echo "error: --input file {$cli['input']} not found \n"; exit;
}

//проверяем указание пути сгенерированного файла
if (empty($cli['output'])) {
    echo "error: --output path cannot be empty \n"; exit;
}

//автозагрузка классов
spl_autoload_register(function ($class_name) {
    include 'classes/'.$class_name . '.class.php';
});

//загрузка функций
include 'functions/genTree.php';
include 'functions/compareJson.php';

//запускаем таймер выполнения
$start_total = microtime(true);

//сообщаем о начале выполнения скрипта
echo "generate json...";

//создаем файл с json деревом
genTree($cli['input'],$cli['output']);

//выводим время выполнения
echo round(microtime(true) - $start_total, 4)."sec";

//если указан compare
if (!empty($cli['compare'])) {
    //сравниваем сгенерированный и эталонный файл
    if (compareJson($cli['output'],$cli['compare'])==true) {
        echo " json equal";
    } else {
        echo " error: compare json don't equal"; exit;
    }
}

//сообщаем о завершении скрипта
echo " complete\n";