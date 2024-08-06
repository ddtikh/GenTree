<?php
class genTree
{ 
    public $input; //путь входящего файла
    public $output; //путь исходящего файла

    public function generate() {

        //проверяем сформированы ли индексы, если нет формируем
        if (!isset($parent) and !isset($relation)) {
        
            //Считываем csv файл в массив строк
            $csv = file($this->input, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);


            //Определяем порядок столбцов csv по первой строке (независимо от того, что порядок может быть известен заранее)
            $csv[0] = str_getcsv($csv[0],';');
            $column['Item Name']=array_search('Item Name', $csv[0]);
            $column['Parent']=array_search('Parent', $csv[0]);
            $column['Relation']=array_search('Relation', $csv[0]);
            unset($csv[0]);



            //--- Построение индексов --- Начало---//

            foreach ($csv as $key => $value) {

                //создаем массивы значений из строк csv
                $csv[$key] = str_getcsv($value,';');    


                //создаем индекс $parent[Имя родительского элемента][0...][имя дочернего элемента] - двумерный массив, где корневые ключи это названия родительских узлов, а элементы это названия их дочерних узлов
                if ($csv[$key][$column['Parent']]!='') {

                    //если у узла есть parent, то создать ключ
                    $parent [ $csv[$key][$column['Parent']] ] [$key] = $csv[$key][$column['Item Name']];

                } else {
                    
                    //если у узла нет parent, то создать ключ null
                    $parent[null][$key] = $csv[$key][$column['Item Name']];
                
                }
                

                //создаем индекс $relation[узел][его relation] - массив, где ключи это названия узлов которые содержат Relation, а значения это названия Relation (которые уже содержаться как родительские узлы в индексе Parent)
                
                if ($csv[$key][$column['Relation']]!='') { 

                    //если есть relation, то создать ключ
                    $relation [ $csv[$key][$column['Item Name']] ] = $csv[$key][$column['Relation']];

                } 
                
            }

            // Освобождаем память от первичных данных csv, все необходимое уже есть в индексах
            unset ($csv,$value,$key); 

            //--- Построение индексов --- Конец ---//

        }


        //--- Формирование дерева на основе индексов --- Начало---//

        //Создаем рекурсивную функцию для обхода индексов от корня (branch='')
        //$currentBranch - параметр необходим чтобы дочерние узлы Relation могли унаследовать parent от родительского узла

        //Для экономии памяти индексы в функцию передаем по ссылке

        function genIndexTree($branch='',$currentBranch=null,&$parent=[],&$relation=[]) {

            //формируем узлы дерева по индексу parent 
            foreach ($parent[$branch] as $item) {

                //Присваиваем значения элементам (и узлам) дерева
                $node['itemName'] = $item;
                $node['parent'] = $currentBranch;
                $node['children'] = [];


                //Если ключ item есть в индексе parent, то строим для него дочернюю ветку
                if (isset($parent[$item])) {
                    $node['children'] = genIndexTree($item,$item,$parent,$relation);
                }
                    

                //Если ключ item есть в индексе relation и в индексе parent (как значение relation[item]), то строим для него дочернюю ветку
                if ( isset($relation[$item]) and isset($parent[$relation[$item]]) ) {
                    $node['children'] = genIndexTree($relation[$item],$item,$parent,$relation);
                }

                //Добавляем сформированную ветку к результирующему массиву
                $tree[] = $node;
                
            }

            return $tree;
        }

        //--- Формирование дерева на основе индексов --- Конец---//


        $branch=''; //пустая стартовая ветка
        $currentBranch=null; //null стартовая родительская ветка


        $tree=genIndexTree($branch,$currentBranch,$parent,$relation); //$tree - готовый массив дерева


        //Конвертация массива дерева в json и запись его в файл
        file_put_contents($this->output, json_encode($tree,JSON_UNESCAPED_UNICODE), LOCK_EX);

    }


}