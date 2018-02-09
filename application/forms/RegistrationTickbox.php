<?php

class Application_Form_RegistrationTickbox extends Zend_Form
{

    public function init()
    {
        // $this->setName('checkbox');
        // $user_id = new Zend_Form_Element_Hidden('user_id');
        // $user_id->setLabel('user_id')
        //         ->setRequired(false)
        //     //   ->addValidator('Regex',false,array('pattern' => '/^[a-z ,.\'-]+$/i'))
        //         ->addFilter('StripTags')
        //         ->addFilter('StringTrim')
        //         ->addValidator('NotEmpty')
        //         ->addValidator('Digits');

        

        $contact_info = new Zend_Form_Element_Checkbox('contact');
        $contact_info->setLabel('Share contact information to sponsers')
                     ->setRequired(true)
                     ->addFilter('StripTags')
                     ->addFilter('StringTrim')
                     ->addValidator('NotEmpty');

        $photos = new Zend_Form_Element_Checkbox('photo');
        $photos->setLabel('Accept that photos will be taken during the conference')
                    ->setRequired(true)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->addValidator('NotEmpty');
             

        

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit')->setAttrib('id', 'submitbutton');

        
        $this->addElements(array(
            $contact_info,
            $photos,
            $submit
        ));
    }
}
