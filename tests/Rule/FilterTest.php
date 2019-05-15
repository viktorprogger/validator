<?php
namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Filter;

/**
 * @group validators
 */
class FilterTest extends TestCase
{
    public function testAssureExceptionOnInit()
    {
        $this->expectException('yii\exceptions\InvalidConfigException');
        new Filter();
    }

    public function testValidateAttribute()
    {
        $m = FakedValidationModel::createWithAttributes([
                'attr_one' => '  to be trimmed  ',
                'attr_two' => 'set this to null',
                'attr_empty1' => '',
                'attr_empty2' => null,
                'attr_array' => ['Maria', 'Anna', 'Elizabeth'],
                'attr_array_skipped' => ['John', 'Bill'],
        ]);
        $val = new Filter(['filter' => 'trim']);
        $val->validateAttribute($m, 'attr_one');
        $this->assertSame('to be trimmed', $m->attr_one);
        $val->filter = function ($value) {
            return null;
        };
        $val->validateAttribute($m, 'attr_two');
        $this->assertNull($m->attr_two);
        $val->filter = [$this, 'notToBeNull'];
        $val->validateAttribute($m, 'attr_empty1');
        $this->assertSame($this->notToBeNull(''), $m->attr_empty1);
        $val->skipOnEmpty = true;
        $val->validateAttribute($m, 'attr_empty2');
        $this->assertNotNull($m->attr_empty2);
        $val->filter = function ($value) {
            return implode(',', $value);
        };
        $val->skipOnArray = false;
        $val->validateAttribute($m, 'attr_array');
        $this->assertSame('Maria,Anna,Elizabeth', $m->attr_array);
        $val->skipOnArray = true;
        $val->validateAttribute($m, 'attr_array_skipped');
        $this->assertSame(['John', 'Bill'], $m->attr_array_skipped);
    }

    public function notToBeNull($value)
    {
        return 'not null';
    }
}