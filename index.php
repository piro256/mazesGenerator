<style>
    #CELL {
        background-color: white;
        width: 5px;
        height: 5px;
    }
    #WALL {
        background-color: black;
        width: 5px;
        height: 5px;
    }
    table {
        border-collapse: collapse;
        text-align: center;
    }
</style>

<?php
/**
 * Created by PhpStorm.
 * User: piro256
 * Date: 10.08.2016
 * Time: 18:38
 */

//задаём ширину и высоту лабиринта
$mazeWidth = 150;
$mazeHeight = 150;
$maze = array();
//генерируем заготовку для лабиринта
//все четные по i / j клетки комнаты, все остальное стены
for ($i = 0; $i <= $mazeHeight; $i++) {
    for ($j = 0; $j <= $mazeWidth; $j++) {
        if (($i%2 != 0) and ($j%2 != 0)) {
            $maze[$i][$j]= "CELL"; //комната
            $mazeMap[$i][$j] = 0;
        } else {
            $maze[$i][$j]= "WALL"; //стена
            $mazeMap[$i][$j] = 1;
        }
    }
}
//пытаемся проложить лабиринт
//$mazeMap - карта посещенных алгоритмом точек
$currentPointI = $startPointI = 1;
$currentPointJ = $startPointJ = 1;
do {
    //обнуляем количество шагов для данной точки
    $countStep = 0;
    //обнуляем массив возможных шагов для данной точки
    $step=array();
    //проверяем непосещенные точки вокруг
    //проверяем точку сверху
    if (($mazeMap[$currentPointI-2][$currentPointJ] === 0) and ($mazeMap[$currentPointI-2][$currentPointJ] != 2)) {
        //можем двигаться вверх
        $stepUp = true;
        $step[] = "stepUp";
        $countStep++;
    } else {
        $stepUp = false;
    }
    //проверяем точку снизу
    if (($mazeMap[$currentPointI+2][$currentPointJ] === 0) and ($mazeMap[$currentPointI+2][$currentPointJ] != 2)
        ) {
        //можем двигаться вниз
        $stepDown = true;
        $countStep++;
        $step[] = "stepDown";
    } else {
        $stepDown = false;
    }
    //проверяем точку слева
    if (($mazeMap[$currentPointI][$currentPointJ-2] === 0) and ($mazeMap[$currentPointI][$currentPointJ-2] != 2)
        ) {
        //можем двигаться влево
        $stepLeft = true;
        $countStep++;
        $step[] = "stepLeft";
    } else {
        $stepLeft = false;
    }
    //проверяем точку справа
    if (($mazeMap[$currentPointI][$currentPointJ+2] === 0) and ($mazeMap[$currentPointI][$currentPointJ+2] != 2)
        ) {
        //можем двигаться вправо
        $stepRight = true;
        $countStep++;
        $step[] = "stepRight";
    } else {
        $stepRight = false;
    }
    //проверяем не единичное ли количество шагов для данной точки
    //если больше 1, то запоминаем точку как стартовую для ветвления графа
    if ($countStep > 1) {
        $startPoints[] = array($currentPointI, $currentPointJ);
    }
    //отмечаем текущую клетку посещенной
    $mazeMap[$currentPointI][$currentPointJ] = 2;
	//выбираем куда пойдем
    $move = rand(0, count($step)-1);
    switch ($step[$move]) {
        case "stepUp":
            //меняем элементы в массивах maze and mazemap
            $mazeMap[$currentPointI-1][$currentPointJ] = 2;
            $maze[$currentPointI-1][$currentPointJ] = "CELL";
            //перемещаемся в следующую точку
            $currentPointI -= 2;
            break;
        case "stepDown":
            $mazeMap[$currentPointI+1][$currentPointJ] = 2;
            $maze[$currentPointI+1][$currentPointJ] = "CELL";
            $currentPointI += 2;
            break;
        case "stepLeft":
            $mazeMap[$currentPointI][$currentPointJ-1] = 2;
            $maze[$currentPointI][$currentPointJ-1] = "CELL";
            $currentPointJ -= 2;
            break;
        case "stepRight":
            $mazeMap[$currentPointI][$currentPointJ+1] = 2;
            $maze[$currentPointI][$currentPointJ+1] = "CELL";
            $currentPointJ +=2;
            break;
    }
    //если ходить некуда возвращаемся на случайную точку из массива $startPoint
    if ($countStep == 0) {
        //выбираем случайную точку из массива стартовых точек
        $startPointsIndex = rand(0, count($startPoints));
		//устанавливаем текущие координаты из массива начальных точек
        $currentPointI = $startPoints[$startPointsIndex][0];
        $currentPointJ = $startPoints[$startPointsIndex][1];
		//удаляем выбранную точку из стартового масива
		//что бы граф имел более 2 вершин из одной точки, надо проверять
		//кол-во возможных путей и удалять точку только если их меньше одной
        unset($startPoints[$startPointsIndex]);
		//перечитываем индексы массива
        $startPoints = array_values($startPoints);
    }
} while (count($startPoints) > 0);

//рисуем лабиринт
echo "<table>";
for ($i = 0; $i <= $mazeHeight; $i++) {
    echo "<tr>";
    for ($j = 0; $j <= $mazeWidth; $j++) {
        if ($maze[$i][$j] == "CELL") {
            echo "<td id='CELL'></td>";
        } elseif ($maze[$i][$j] == "WALL") {
            echo "<td id='WALL'></td>";
        }
    }
    echo "</tr>";
}
echo "</table><br>";
$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
printf('Лабиринт построен за %.3F сек.', $time);
