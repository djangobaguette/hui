<?php

namespace Brezgalov\PortTransitCommon\Utils;

class StringsHelper
{
    /**
     * @var array
     */
    public static $monthsSource = [
        1 =>    ['январь',      'января'    ],
        2 =>    ['февраль',     'февраля'   ],
        3 =>    ['март',        'марта'     ],
        4 =>    ['апрель',      'апреля'    ],
        5 =>    ['май',         'мая'       ],
        6 =>    ['июнь',        'июня'      ],
        7 =>    ['июль',        'июля'      ],
        8 =>    ['август',      'августа'   ],
        9 =>    ['сентябрь',    'сентября'  ],
        10 =>   ['октябрь',     'октября'   ],
        11 =>   ['ноябрь',      'ноября'    ],
        12 =>   ['декабрь',     'декабря'   ],
    ];

    /**
     * @param int $timestamp
     * @param bool $firstForm
     * @return string|null
     */
    public static function getMonthName($timestamp, $firstForm = true)
    {
        $monthNumber = date('n', $timestamp);
        $form = $firstForm ? 0 : 1;
        return @self::$monthsSource[$monthNumber][$form];
    }

    /**
     * Переводим кейс строки в кейс где первая буква каждого слова - заглавная
     * @param string $value
     * @return string
     */
    public static function setCaseLikeName($value){
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Удаляем из строки все, кроме цифр
     * @param string $value
     * @return string
     */
    public static function clearPhoneNumber($value){
        return preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Генерируем пароль из цифр и букв разного кейса
     * @param int $count - количество символов
     * @return string
     */
    public static function generatePasswordStrong($count)
    {
        $arr = [
            'a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's',
            't', 'u', 'v', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'R', 'S',
            'T', 'U', 'V', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0'
        ];
        $pass = '';
        for ($i = 0; $i < $count; $i++) {
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }

    /**
     * Переводим первую букву слова в верхний регистр
     * @param $string
     * @return string
     */
    public static function firstCharToUpperMb($string)
    {
        $strlen = mb_strlen($string);
        $firstChar = mb_substr($string, 0, 1);
        $then = mb_substr($string, 1, $strlen - 1);
        return mb_strtoupper($firstChar) . $then;
    }

    /**
     * применяем к строке маску. Например $phoneMask = [2, '(', 3, ')', 3, '-', 2, '-', 2];
     * @param $string
     * @param array $mask
     * @param array $dropChars
     * @param string $dropDigit
     * @return string
     */
    public static function putMask($string, array $mask)
    {
        $window = 0;
        $result = '';
        foreach ($mask as $rule) {
            if (is_integer($rule)) {
                $result .= mb_substr($string, $window, $rule);
                $window += $rule;
            } else {
                $result .= $rule;
            }
        }
        return $result;
    }

    /**
     * Выбираем правельную форму слова по числу
     * @param integer $number - число: 1, 2, 25
     * @param string $one - едичиная форма (1 слон)
     * @param string $two - хз какая форма (2 слона)
     * @param string $five - множ. форма (5 слонов)
     * @return string
     */
    public static function numberToWord($number, $one, $two, $five)
    {
        $titles = [$one, $two, $five];
        $cases = array(2, 0, 1, 1, 1, 2);
        return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * Конвертирует число в человекопонятную строку, стянуто с https://habr.com/ru/post/53210/
     * setup = [
     *  'decimals' => [
     *      'show' => true,
     *      'parse' => true,
     *      'words' => ['копейка', 'копейки', 'копеек', 1], //1 = флаг "женский пол"
     *  ],
     *  'units' => ['рубль', 'рубля', 'рублей', 0], //0 = флаг "мужской пол"
     * ]
     * @param $number
     * @param array $decimalsSetup
     * @return string
     */
    public static function numberByWords($number, array $setup = [])
    {
        $number = str_replace(',', '.', $number);
        $number = preg_replace('/[^.0-9]/', '', $number);

        $out = [];
        $decimalsSetup = [
            'show'  => true,
            'parse' => true,
            'words' => ['копейка', 'копейки', 'копеек', 1],
        ];
        if (array_key_exists('decimals', $setup)) {
            $decimalsSetup = array_merge($decimalsSetup, $setup['decimals']);
        }

        $unitsWords = ['рубль',       'рубля',    'рублей',       0];
        if (array_key_exists('units', $setup)) {
            $unitsWords = $setup['units'];
        }

        $nul = 'ноль';
        $ten = [
            ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
            ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
        ];
        $a20 = [
            'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать',
            'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'
        ];
        $tens = array(2=>'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];
        $unit = [
            $decimalsSetup['words'],
            $unitsWords,
            ['тысяча',      'тысячи',   'тысяч',        1],
            ['миллион',     'миллиона', 'миллионов',    0],
            ['миллиард',    'милиарда', 'миллиардов',   0],
        ];

        list($rub, $kop) = explode('.',sprintf("%015.2f", floatval($number)));
        if (intval($rub) > 0) {
            foreach(str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) {
                    continue;
                }
                $uk = sizeof($unit)- $uk -1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) {
                    $out[]= $tens[$i2].' '.$ten[$gender][$i3];
                } # 20-99
                else {
                    $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3];
                } # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) {
                    $out[]= self::numberToWord($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
                }
            }
        } else {
            $out[] = $nul;
        }
        $out[] = self::numberToWord(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]);
        if ($decimalsSetup['show']) {
            $out[] = $kop . ($decimalsSetup['parse'] ? (
                    ' ' . self::numberToWord($kop,$unit[0][0],$unit[0][1],$unit[0][2])
                ) : (
                ''
                ));
        }

        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }
}