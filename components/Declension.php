<?php

namespace frontend\modules\pmnbd\components;

use Yii;

class Declension {
	public static function end_restaurants($num) {
		$ost=$num%10;
		$ost100 = $num%100;
		if (($ost100<10 || $ost100>20) && $ost!=0) {
			switch ($ost) {
				case 1:	$end='';	break;
				case 2:
				case 3:
				case 4: $end='а'; break;
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:	$end='ов'; break;
			}
		} else $end='ов';
		return $end;
	}

	public static function end_rooms($num) {
		$ost=$num%10;
		$ost100 = $num%100;
		if (($ost100<10 || $ost100>20) && $ost!=0) {
			switch ($ost) {
				case 1:	$end='';	break;
				case 2:
				case 3:
				case 4: $end='а'; break;
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:	$end='ов'; break;
			}
		} else $end='ов';
		return $end;
	}

	/**
     * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
     * @param  $number Integer Число на основе которого нужно сформировать окончание
     * @param  $endingsArray  Array Массив слов или окончаний для чисел (1, 4, 5),
     *         например array('яблоко', 'яблока', 'яблок')
     * @return String
     */
    public static function get_num_ending($number, $endingArray)
    {
        $number = $number % 100;
        if ($number>=11 && $number<=19) {
            $ending=$endingArray[2];
        } else {
            $i = $number % 10;
            switch ($i)
            {
                case (1): $ending = $endingArray[0]; break;
                case (2):
                case (3):
                case (4): $ending = $endingArray[1]; break;
                default: $ending=$endingArray[2];
            }
        }
        return $ending;
    }

    public static function csrfToken(){
    	return Yii::$app->request->getCsrfToken();
    }
}