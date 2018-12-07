<?php

class ExportController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $doc_body = "<div style='width: 100%; background-color: black; text-align: center; color: white; padding: 40px;'>This is the HEADER</div>";
        
        $rows_allowed_on_page = 27;
        $size_of_array = 26;
        
        for ($i = 1; $i <= $size_of_array; $i++)
        {
            $doc_body .= "<p style='text-align: center'>Row $i</p>";
        }
        
        if ($size_of_array < $rows_allowed_on_page)
        {
            $blanc_lines = $rows_allowed_on_page - $size_of_array;
            
            for ($e = 1; $e <= $blanc_lines; $e++)
            {
                $doc_body .= "<p style='text-align: center'>Empth row $e</p>";
            }
            $doc_body .= "<div style='width: 100%; background-color: black; text-align: center; color: white; padding: 40px;'>This is the Footer</div>";
        }
        else
        {
            $doc_body .= "<div style='width: 100%; background-color: black; text-align: center; color: white; padding: 40px;'>This is the Footer</div>";
        }
        
        header('Content-Type', 'application/pdf;');
        
        echo "<html>";
        echo $doc_body;
        echo "</html>";
    }
}

