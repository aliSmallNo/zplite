<?php

namespace zplite;

use InvalidArgumentException;
use Closure;

define('PINYIN_NONE', 'none');
define('PINYIN_ASCII', 'ascii');
define('PINYIN_UNICODE', 'unicode');

class Pinyin
{
    const NONE = 'none';
    const ASCII = 'ascii';
    const UNICODE = 'unicode';

    /**
     * Punctuations map.
     *
     * @var array
     */
    protected $punctuations = array(
        '，' => ',',
        '。' => '.',
        '！' => '!',
        '？' => '?',
        '：' => ':',
        '“' => '"',
        '”' => '"',
        '‘' => "'",
        '’' => "'",
    );

    protected $path;

    public function __construct()
    {
        $this->path = dirname(__DIR__) . '/data/';
    }

    /**
     * 汉字转拼音
     */
    public function convert($string, $option = self::NONE)
    {
        $pinyin = $this->romanize($string);

        return $this->splitWords($pinyin, $option);
    }

    /**
     * Convert 人名 to pinyin.
     */
    public function name($stringName, $option = self::NONE)
    {
        $pinyin = $this->romanize($stringName, true);

        return $this->splitWords($pinyin, $option);
    }

    /**
     * Return a pinyin permalink from string.
     */
    public function permalink($string, $delimiter = '-')
    {
        if (!in_array($delimiter, array('_', '-', '.', ''), true)) {
            throw new InvalidArgumentException("Delimiter must be one of: '_', '-', '', '.'.");
        }

        return implode($delimiter, $this->convert($string, false));
    }

    /**
     * Return first letters.
     */
    public function abbr($string, $delimiter = '')
    {
        return implode($delimiter, array_map(function ($pinyin) {
            return $pinyin[0];
        }, $this->convert($string, false)));
    }

    /**
     * Chinese phrase to pinyin.
     */
    public function phrase($string, $delimiter = ' ', $option = self::NONE)
    {
        return implode($delimiter, $this->convert($string, $option));
    }

    /**
     * Chinese to pinyin sentense.
     */
    public function sentence($sentence, $withTone = false)
    {
        $marks = array_keys($this->punctuations);
        $punctuationsRegex = preg_quote(implode(array_merge($marks, $this->punctuations)), '/');
        $regex = '/[^üāēīōūǖáéíóúǘǎěǐǒǔǚàèìòùǜa-z0-9' . $punctuationsRegex . '\s_]+/iu';

        $pinyin = preg_replace($regex, '', $this->romanize($sentence));

        $punctuations = array_merge($this->punctuations, array("\t" => ' ', '  ' => ' '));
        $pinyin = trim(str_replace(array_keys($punctuations), $punctuations, $pinyin));

        return $withTone ? $pinyin : $this->format($pinyin, false);
    }

    /**
     * Preprocess.
     */
    protected function prepare($string)
    {
        $string = preg_replace_callback('~[a-z0-9_-]+~i', function ($matches) {
            return "\t" . $matches[0];
        }, $string);

        return preg_replace("~[^\p{Han}\p{P}\p{Z}\p{M}\p{N}\p{L}\t]~u", '', $string);
    }

    /**
     * Convert Chinese to pinyin.
     */
    protected function romanize($string, $isName = false)
    {
        $string = $this->prepare($string);

        if ($isName) {
            $string = $this->convertSurname($string);
        }

        $this->map(function ($dictionary) use (&$string) {
            $string = strtr($string, $dictionary);
        });

        return $string;
    }

    /**
     * Convert Chinese Surname to pinyin.
     */
    protected function convertSurname($string)
    {
        $this->mapSurname(function ($dictionary) use (&$string) {
            foreach ($dictionary as $surname => $pinyin) {
                if (strpos($string, $surname) === 0) {
                    $string = $pinyin . mb_substr($string, mb_strlen($surname, 'UTF-8'),
                            mb_strlen($string, 'UTF-8') - 1, 'UTF-8');
                    break;
                }
            }
        });

        return $string;
    }

    protected function map(Closure $callback)
    {
        for ($i = 0; $i < 6; ++$i) {
            $segment = $this->path . '/' . sprintf('words_%s', $i);

            $dictionary = include $segment;
            $callback($dictionary);
        }
    }

    protected function mapSurname(Closure $callback)
    {
        $surnames = $this->path . '/surnames';

        $dictionary = include $surnames;
        $callback($dictionary);
    }

    /**
     * Split pinyin string to words.
     */
    public function splitWords($pinyin, $option)
    {
        $split = array_filter(preg_split('/[^üāēīōūǖáéíóúǘǎěǐǒǔǚàèìòùǜa-z\d]+/iu', $pinyin));

        if ($option !== self::UNICODE) {
            foreach ($split as $index => $pinyin) {
                $split[$index] = $this->format($pinyin, $option === self::ASCII);
            }
        }

        return array_values($split);
    }

    /**
     * Format.
     */
    protected function format($pinyin, $tone = false)
    {
        $replacements = array(
            'üē' => array('ue', 1),
            'üé' => array('ue', 2),
            'üě' => array('ue', 3),
            'üè' => array('ue', 4),
            'ā' => array('a', 1),
            'ē' => array('e', 1),
            'ī' => array('i', 1),
            'ō' => array('o', 1),
            'ū' => array('u', 1),
            'ǖ' => array('v', 1),
            'á' => array('a', 2),
            'é' => array('e', 2),
            'í' => array('i', 2),
            'ó' => array('o', 2),
            'ú' => array('u', 2),
            'ǘ' => array('v', 2),
            'ǎ' => array('a', 3),
            'ě' => array('e', 3),
            'ǐ' => array('i', 3),
            'ǒ' => array('o', 3),
            'ǔ' => array('u', 3),
            'ǚ' => array('v', 3),
            'à' => array('a', 4),
            'è' => array('e', 4),
            'ì' => array('i', 4),
            'ò' => array('o', 4),
            'ù' => array('u', 4),
            'ǜ' => array('v', 4),
        );

        foreach ($replacements as $unicde => $replacement) {
            if (false !== strpos($pinyin, $unicde)) {
                $pinyin = str_replace($unicde, $replacement[0], $pinyin) . ($tone ? $replacement[1] : '');
            }
        }

        return $pinyin;
    }
}
