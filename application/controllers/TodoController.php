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

//            $this->view->filepath = $filepath = '/public/img';
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination(PUBLIC_PATH . '/img');
            $files = $upload->getFileInfo();
            $newName = $files['picture']['name'];
            $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $newName, 'overwrite' => true)));

            if ($upload->receive() && isset($files['picture']['name'])) {
                $formData['pic_url'] = $newName;
            }


            $db = new Zend_Db_Table('todo_app');

            $data = array(
                'task_for' => $task_for,
                'task_details' => $task_details,
                'complete_on' => date('Y-m-d', strtotime(' +1 day')),
                'pic_url' => $formData['pic_url']
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

    public function loopjsonAction()
    {

    }

    public function myfirstapiAction()
    {

    }

    public function viewpeopleAction()
    {

    }
}