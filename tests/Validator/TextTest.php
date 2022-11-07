<?php declare(strict_types=1);

namespace Validator;

use PHPUnit\Framework\TestCase;
use pp\Validator\Text;

final class TextTest extends TestCase
{

    public function testEmptyValues(): void
    {

        $validator = new Text(required: true, minlength: 1, maxlength:5);
        $validator->isValid('');
        $this->assertEquals('Value cannot be empty', $validator->error);

        
        $validator->isValid(@$value);
        $this->assertEquals('Value cannot be empty', $validator->error);

        
        $validator->isValid(false);
        $this->assertEquals('Value must be a string', $validator->error);

        
        $validator->isValid('0');
        $this->assertEquals(false, $validator->error);

        $validator = new Text(required: false, minlength: 1, maxlength:5);
        $validator->isValid('');
        $this->assertEquals(false, $validator->error);

        
        $validator->isValid(@$value);
        $this->assertEquals(false, $validator->error);

        $validator->isValid(false);
        $this->assertEquals('Value must be a string', $validator->error);

        
        $validator->isValid('0');
        $this->assertEquals(false, $validator->error);

    }

    public function testMin(): void
    {

        $validator = new Text(required: false, minlength: 2, maxlength:5);
        $validator->isValid('s');
        $this->assertEquals('Value must be greater than or equal to 2 characters', $validator->error);
        
        $validator->isValid('è');
        $this->assertEquals('Value must be greater than or equal to 2 characters', $validator->error);

        $validator->isValid('èè');
        $this->assertEquals(false, $validator->error);


    }

    public function testMax(): void
    {

        $validator = new Text(required: false, minlength: 1, maxlength:5);
        $validator->isValid('Hello World!');
        $this->assertEquals('Value must be less than or equal to 5 characters', $validator->error);
        
        $validator->isValid('Hello');
        $this->assertEquals(false, $validator->error);
        
        $validator->isValid('èèèèè');
        $this->assertEquals(false, $validator->error);
        
        $validator->isValid('èèèèèè');
        $this->assertEquals('Value must be less than or equal to 5 characters', $validator->error);

    }

    public function testPattern(): void
    {

        $validator = new Text(required: false, pattern: '[a-c]+/[a-c]+');
        $validator->isValid('Hello');
        $this->assertEquals('Value does not match the requested format', $validator->error);
        
        $validator->isValid('ab/ab');
        $this->assertEquals(false, $validator->error);

        $validator->isValid('1ab/ab');
        $this->assertEquals('Value does not match the requested format', $validator->error);

        $validator->isValid('ab/ab1');
        $this->assertEquals('Value does not match the requested format', $validator->error);

    }

}