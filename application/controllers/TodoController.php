<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 03/05/2018
 * Time: 11:30
 */
class TodoController extends Zend_Controller_Action
{
    public function indexAction()
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $task_for = $formData['task_for'];
            $task_details = $formData['task_details'];

            $db = new Zend_Db_Table('todo_app');

            $data = array(
                'task_for' => $task_for,
                'task_details' => $task_details,
                'complete_on' => date('Y-m-d', strtotime(' +1 day'))
            );
            $db->insert($data);

            $database = new Zend_Db_Table('todo_app');

            $rows = $database->fetchAll()->toArray();

            $json = json_encode($rows);

            $file = 'todo.json';

            file_put_contents($file, $json);
        }
    }

    public function viewtodolistAction()
    {

    }
}