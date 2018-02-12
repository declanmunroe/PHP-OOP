<?php

class Application_Form_PracticeForm extends Zend_Form
{

    public function init()
    {

        

        $first_name = new Zend_Form_Element_Text('first_name');
        $first_name->setLabel('First Name')
               ->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim')
               ->addValidator('NotEmpty');

        $last_name = new Zend_Form_Element_Text('last_name');
        $last_name->setLabel('Last Name')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        
        $contact_info = new Zend_Form_Element_Checkbox('share_contact_info');
        $contact_info->setLabel('Share contact information to sponsers')
                     ->setRequired(true)
                     ->addFilter('StripTags')
                     ->addFilter('StringTrim')
                     ->addValidator('NotEmpty');

        $photos = new Zend_Form_Element_Checkbox('allow_photos');
        $photos->setLabel('Accept that photos will be taken during the conference')
                    ->setRequired(true)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->addValidator('NotEmpty');
             

        

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit')->setAttrib('id', 'submitbutton');

        
        $this->addElements(array(
            $first_name,
            $last_name,
            $contact_info,
            $photos,
            $submit
        ));
    }
}
