<?php
include './Loader.php';
spl_autoload_register('\Loader::_autoload');

require __DIR__ . '/vendor/autoload.php';

use Nezimi\Error;
use Nezimi\Validate;

new Error();

class  tempValidate extends Validate{

    protected $field = [
        'name' => '名字',
        'email' => '邮件',
    ];

    protected $rule = [
        'name'  => ['require'],
        'email' => ['number'],
    ];
    
    // protected $message = [
    //     'name.require' => '名字绝对不能为空',
    //     'email.number' => '邮件绝对必须为数字',
    // ];

    // protected $message = [
    //     'name' => [
    //         'require' => '名字绝对不能为空2',
    //     ],
    //     'email.number' => '邮件绝对必须为数字',
    // ];

    protected $message = [
        'name' => '名字需要符合规则',
        'email.number' => '邮件绝对必须为数字',
    ];
}
$validate = new tempValidate();

$data = [
    'name'  => '',
    'email' => 'df'
];

// p($validate->isRequire(''));
// p($validate->isNumber(2));
// p($validate->isInteger('2'));
p($validate->isEmail('2@qq.com'));

// if (!$validate->batch()->check($data)) {
//     p($validate->getError());
// }