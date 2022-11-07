<?php declare(strict_types=1);

namespace Validator;

use PHPUnit\Framework\TestCase;
use pp\Validator\Number;

final class NumberTest extends TestCase
{

    public function testEmptyValues(): void
    {

        $validator = new Number(required: true, min: 1, max:5);
        $validator->isValid('');
        $this->assertEquals('Value cannot be empty', $validator->error);

        
        $validator->isValid(@$value);
        $this->assertEquals('Value cannot be empty', $validator->error);

        
        $validator->isValid(false);
        $this->assertEquals('Value must be a number', $validator->error);

        
        $validator->isValid('0');
        $this->assertEquals('Value must be greater than or equal to 1', $validator->error);

    }

    public function testMin(): void
    {

        $validator = new Number(required: false, min: 1, max: 5);
        $validator->isValid('');
        $this->assertEquals(false, $validator->error);
        
        $validator->isValid(0);
        $this->assertEquals('Value must be greater than or equal to 1', $validator->error);

        $validator->isValid(1);
        $this->assertEquals(false, $validator->error);

    }

    public function testMax(): void
    {

        $validator = new Number(required: false, min: 1, max: 5);

        $validator->isValid(6);
        $this->assertEquals('Value must be less than or equal to 5', $validator->error);

        $validator->isValid(5);
        $this->assertEquals(false, $validator->error);

    }

    public function testStep(): void
    {

        $validator = new Number(required: false, min: 1, max: 5, step: 2);
        $validator->isValid('2');
        $this->assertEquals('Invalid value. The two nearest valid values are 1 and 3', $validator->error);
        
        $validator->isValid('3');
        $this->assertEquals(false, $validator->error);

    }

}