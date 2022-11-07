<?php declare(strict_types=1);

namespace pp\Form;

use PHPUnit\Framework\TestCase;

final class FormTest extends TestCase
{

    //static $form;

    public function setUp(): void
    {

        $form = new Form;
        $form->add('button', new Button);
        $form->add('checkbox', new Checkbox);
        $form->add('checklist', new CheckList(list: [
            'a' => 'Check A',
            'b' => 'Check B',
        ]));
        $form->add('color', new Color);
        $form->add('date', new Date);
        $form->add('datetime', new DateTime);
        $form->add('email', new Email);
        $form->add('multiple_email',  new Email(
            attributes: [
                'multiple' => true
            ]
        ));
        $form->add('file', new File);
        $form->add('hidden', new Hidden);
        $form->add('image', new Image);
        $form->add('month', new Month);
        $form->add('number', new Number);
        $form->add('password', new Password);
        $form->add('radio', new Radio);
        $form->add('radiolist', new RadioList(
            list: [
                'yes' => 'Yes',
                'no' => 'No',
            ]
        ));
        $form->add('range', new Range);
        $form->add('reset', new Reset);
        $form->add('search', new Search);
        $form->add('submit', new Submit);
        $form->add('tel', new Tel);
        $form->add('text', new Text);
        $form->add('textarea', new Textarea);
        $form->add('time', new Time);
        $form->add('url', new Url);
        $form->add('week', new Week);

        $this->form = $form;

    }

    public function testData(): void
    {
        $data = [
            'button' => 'button data',
            'checkbox' => '1',
            'checklist' => [
                'a' => 'a',
                'b' => 'b'
            ],
            'color' => 'color data',
            'date' => 'date data',
            'datetime' => 'datetime data',
            'email' => 'email data',
            'multiple_email' => 'email, data',
            'file' => 'file data',
            'hidden' => 'hidden data',
            'image' => 'image data',
            'month' => 'month data',
            'number' => 'number data',
            'password' => 'password data',
            'radio' => '1',
            'radiolist' => 'yes',
            'range' => 'range data',
            'reset' => 'reset data',
            'search' => 'search data',
            'submit' => 'submit data',
            'tel' => 'tel data',
            'text' => 'text data',
            'textarea' => 'textarea data',
            'time' => 'time data',
            'url' => 'url data',
            'week' => 'week data'
        ];

        $this->form->setData($data);

        $data['checklist'] = ['a', 'b'];
        $data['multiple_email'] = ['email', 'data'];

        $this->assertEquals($data, $this->form->getData($data));

    }

}