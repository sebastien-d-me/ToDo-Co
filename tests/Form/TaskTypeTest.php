<?php

namespace App\Form;

use App\Form\TaskType;
use App\Entity\Task;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testTaskType(): void
    {   
        $formData = [
            "title" => "Nunc gravida ligula non est convallis faucibus",
            "content" => "Morbi sit amet molestie sem. Quisque mattis posuere neque ullamcorper pulvinar.",
        ];

        $task = new Task();
        $form = $this->factory->create(TaskType::class, $task);

        $expected = new Task();
        $expected->setTitle($formData["title"]);
        $expected->setContent($formData["content"]);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $task);
    }
}