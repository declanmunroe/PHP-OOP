<?php

class IssueCertsController extends Zend_Controller_Action
{
    public function indexAction() 
    {
        $studentInfo = array('student_fname' => 'Declan', 'student_lname' => 'Munroe', 'centre_name' => 'Milverton');
        
        $this->getResponse()->setHeader('Content-Type', 'application/pdf;');
        header('Content-Type: application/pdf; charset=UTF-8');
        echo $this->generateScratchCert($studentInfo);
    }
    
    private function generateScratchCert($data)
    {
        //$pdf = new Zend_Pdf();
        
        $margin_x = 50;
        $topMargin = -110;
        $topline_y = $topMargin + 450;
        $name_y = $topMargin + 375;
        $line1_y = $topMargin + 325;
        
        $path = realpath(dirname(__FILE__).'../../../public');
        //die(var_dump($path));
        $pdf = Zend_Pdf::load($path . "/scratch_cert.pdf");
        $page = $pdf->pages[0];
            
//        $page->drawText("This is to certify that", $margin_x, $topline_y);
//
//        $page->setFont($page->_normalFont, 48);
//        $page->drawText($data['student_fname'] . " " . $data['student_lname'], $margin_x, $name_y);
//
//        $page->_boldFont = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
//        $page->setFont($page->_boldFont, 30);
//        $page->drawText("Scratch Completion Certificate", $margin_x+160, $topline_y+100);
//
//        $page->_italicFont = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC);
//        $page->setFont($page->_italicFont, 16);
//        $page->drawText("This is to certify that", $margin_x+290, $topline_y+30);
//
//        $page->setFont($page->_boldFont, 24);
//        $page->drawText($data['student_fname'] . " " . $data['student_lname'], $margin_x, $name_y+50);
//
//        $page->setFont($page->_italicFont, 16);
//        $page->drawText("has completed the", $margin_x+300, $line1_y+50);
//
//        $page->setFont($page->_boldFont, 17);
//        $page->drawText("Scratch for Primary Schools, Computer Programming Course", $margin_x, $line1_y);
//
//        $page->setFont($page->_italicFont, 16);
//        $page->drawText("at the {$data['centre_name']}", $margin_x, $line1_y-50);

        return $pdf->render();
    }
}