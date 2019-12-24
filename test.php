<?php

namespace zplite;

require './src/Pinyin.php';
$pinyin = new Pinyin();

print_r($pinyin->convert("水电费"));
print_r($pinyin->abbr("水电费"));
print_r($pinyin->sentence("水电费"));
print_r($pinyin->name("尉迟恭"));

