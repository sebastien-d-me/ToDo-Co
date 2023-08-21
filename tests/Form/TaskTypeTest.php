<?php

namespace App\Tests\Form;

use App\Form\TaskType;
use App\Entity\Task;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testTaskType(): void
    {   
        $task = new Task();

        $formData = [
            "title" => "Nunc gravida ligula non est convallis faucibus",
            "content" => "Morbi sit amet molestie sem. Quisque mattis posuere neque ullamcorper pulvinar."
        ];
        $form = $this->factory->create(TaskType::class, $task);
        $form->submit($formData);

        $expected = new Task();
        $expected->setTitle($formData["title"]);
        $expected->setContent($formData["content"]);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $task);
    }
}