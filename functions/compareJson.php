<?php
//функция для сравнения json файлов
function compareJson ($output, $compare) {

    //т.к. json может быть отформатирован поразному, сбрасываем форматирование

    //считываем json файлы и переводим их в массивы, затем обратно в json без опции JSON_PRETTY_PRINT
    $output = json_encode(json_decode(file_get_contents($output)), JSON_UNESCAPED_UNICODE);
    $compare = json_encode(json_decode(file_get_contents($compare)), JSON_UNESCAPED_UNICODE);

    //сравниваем и возвращаем результат
    if ($output==$compare) { return true; } else { return false; }

}