Pinyin
======
词典的中文转拼音工具，更准确的支持多音字的汉字转拼音解决方案。


## 安装

使用 Composer 安装:

```
composer require "alismall/zplite"
```

## 使用


### 拼音数组

```php

$pinyin = new Pinyin(); // 默认

$pinyin->convert('带着希望去旅行，比到达终点更美好');
// ["dai", "zhe", "xi", "wang", "qu", "lv", "xing", "bi", "dao", "da", "zhong", "dian", "geng", "mei", "hao"]

$pinyin->convert('带着希望去旅行，比到达终点更美好', PINYIN_UNICODE);
// ["dài","zhe","xī","wàng","qù","lǚ","xíng","bǐ","dào","dá","zhōng","diǎn","gèng","měi","hǎo"]

$pinyin->convert('带着希望去旅行，比到达终点更美好', PINYIN_ASCII);
//["dai4","zhe","xi1","wang4","qu4","lv3","xing2","bi3","dao4","da2","zhong1","dian3","geng4","mei3","hao3"]
```

选项：

|      选项      | 描述                                                |
| -------------  | ---------------------------------------------------|
| `PINYIN_NONE`   | 不带音调输出: `mei hao`                           |
| `PINYIN_ASCII`  | 带数字式音调：  `mei3 hao3`                    |
| `PINYIN_UNICODE`  | UNICODE 式音调：`měi hǎo`                    |

### 生成用于链接的拼音字符串

```php
$pinyin->permalink('带着希望去旅行'); // dai-zhe-xi-wang-qu-lv-xing
$pinyin->permalink('带着希望去旅行', '.'); // dai.zhe.xi.wang.qu.lv.xing
```

### 获取首字符字符串

```php
$pinyin->abbr('带着希望去旅行'); // dzxwqlx
$pinyin->abbr('带着希望去旅行', '-'); // d-z-x-w-q-l-x
```

### 翻译整段文字为拼音

将会保留中文字符：`，。 ！ ？ ： “ ” ‘ ’` 并替换为对应的英文符号。

```php
$pinyin->sentence('带着希望去旅行，比到达终点更美好！');
// dai zhe xi wang qu lv xing, bi dao da zhong dian geng mei hao!

$pinyin->sentence('带着希望去旅行，比到达终点更美好！', true);
// dài zhe xī wàng qù lǚ xíng, bǐ dào dá zhōng diǎn gèng měi hǎo!
```

### 翻译姓名

姓名的姓的读音有些与普通字不一样，比如 ‘单’ 常见的音为 `dan`，而作为姓的时候读 `shan`。

```php
$pinyin->name('单某某'); // ['shan', 'mou', 'mou']
$pinyin->name('单某某', PINYIN_UNICODE); // ["shàn","mǒu","mǒu"]
```

## 参考

- [详细参考资料](https://github.com/overtrue/pinyin-resources)

# License

MIT

# 写在最后

参考了 [资料](https://github.com/overtrue/pinyin)写的demo, 不足之处烦请指正。

