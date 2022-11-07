<?php declare(strict_types=1);

namespace Validator;

use PHPUnit\Framework\TestCase;
use pp\Validator\Email;

final class EmailTest extends TestCase
{

    public function testEmptyValues(): void
    {

        $validator = new Email(maxlength:5, minlength: 1, required: true);
        $validator->isValid('');
        $this->assertEquals('Value cannot be empty', $validator->error);

        
        $validator->isValid(@$value);
        $this->assertEquals('Value cannot be empty', $validator->error);

        
        $validator->isValid(false);
        $this->assertEquals('Value must be a string', $validator->error);

        
        $validator->isValid('0');
        $this->assertEquals('Invalid e-mail address', $validator->error);

        $validator = new Email(maxlength:5, required: false, minlength: 1);
        $validator->isValid('');
        $this->assertEquals(false, $validator->error);

        
        $validator->isValid(@$value);
        $this->assertEquals(false, $validator->error);

        $validator->isValid(false);
        $this->assertEquals('Value must be a string', $validator->error);

        
        $validator->isValid('0');
        $this->assertEquals('Invalid e-mail address', $validator->error);

    }

    public function testEmailValues(): void
    {

        $validator = new Email(required: false);
        $validator->isValid('hi@example.com');
        $this->assertEquals(false, $validator->error);

    }

    public function testMin(): void
    {

        $validator = new Email(maxlength:5, required: false, minlength: 2);
        $validator->isValid('s');
        $this->assertEquals('Value must be greater than or equal to 2 characters', $validator->error);
        
        $validator->isValid('è');
        $this->assertEquals('Value must be greater than or equal to 2 characters', $validator->error);

        $validator->isValid('èè');
        $this->assertEquals('Invalid e-mail address', $validator->error);


    }

    public function testMax(): void
    {

        $validator = new Email(maxlength:5, required: false, minlength: 1);
        $validator->isValid('Hello World!');
        $this->assertEquals('Value must be less than or equal to 5 characters', $validator->error);
        
        $validator->isValid('Hello');
        $this->assertEquals('Invalid e-mail address', $validator->error);
        
        $validator->isValid('èèèèè');
        $this->assertEquals('Invalid e-mail address', $validator->error);
        
        $validator->isValid('èèèèèè');
        $this->assertEquals('Value must be less than or equal to 5 characters', $validator->error);

    }

    public function testPattern(): void
    {

        $validator = new Email(required: false, pattern: '[a-c]+/[a-c]+');
        $validator->isValid('Hello');
        $this->assertEquals('Value does not match the requested format', $validator->error);
        
        $validator->isValid('ab/ab');
        $this->assertEquals('Invalid e-mail address', $validator->error);

        $validator->isValid('1ab/ab');
        $this->assertEquals('Value does not match the requested format', $validator->error);

        $validator->isValid('ab/ab1');
        $this->assertEquals('Value does not match the requested format', $validator->error);

    }

}