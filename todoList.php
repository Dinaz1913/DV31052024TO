<?php

require 'vendor/autoload.php';

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

const TODO_FILE = 'todos.json';

function loadTodos(array $emptyArray = []): array
{
    if (!file_exists(TODO_FILE)) {
        return $emptyArray;
    }
    $json = file_get_contents(TODO_FILE);
    return json_decode($json, true);
}

function saveTodos(array $todos): void
{
    $json = json_encode($todos, JSON_PRETTY_PRINT);
    file_put_contents(TODO_FILE, $json);
}

function displayTodos(): void
{
    $todos = loadTodos();
    $output = new ConsoleOutput();
    $table = new Table($output);
    $table->setHeaders
    (['ID', 'Task', 'Status']);

    foreach ($todos as $idOfTodo => $todo) {
        $table->addRow
        (
            [$idOfTodo + 1,
                $todo['task'],
                $todo['status']]);
    }

    $table->render();
}

while (true) {
    echo "\nTODO Application\n";
    echo "1. Create new TODO\n";
    echo "2. Display list of TODOs\n";
    echo "3. Mark TODO as completed\n";
    echo "4. Delete TODO\n";
    echo "5. Exit\n";
    echo "Enter your choice: ";

    $choice = (int)readline();

    switch ($choice) {
        case 1:
            $todos = loadTodos();
            $task = readline
            ("Enter the TODO task: ");
            $todos[] =
                ["task" => $task,
                    "status" => "pending"];

            saveTodos($todos);
            echo "TODO added successfully.\n";
            break;

        case 2:
            displayTodos();
            break;

        case 3:
            $todos = loadTodos();
            displayTodos();
            $id = (int)readline
                ("Enter the TODO ID to mark as completed: ") - 1;

            if (!isset($todos[$id])) {
                echo "Invalid TODO ID.\n";
                break;
            }

            $todos[$id]['status'] = 'completed';
            saveTodos($todos);
            echo "TODO marked as completed.\n";
            break;

        case 4:
            $todos = loadTodos();
            displayTodos();
            $id = (int)readline
                ("Enter the TODO ID to delete: ") - 1;

            if (!isset($todos[$id])) {
                echo "Invalid TODO ID.\n";
                break;
            }

            array_splice($todos, $id, 1);
            saveTodos($todos);
            echo "TODO deleted successfully.\n";
            break;

        case 5:
            exit("Goodbye!\n");

        default:
            echo "Invalid choice, please try again.\n";
            break ;
    }
}
