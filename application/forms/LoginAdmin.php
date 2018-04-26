<?php

class Application_Form_LoginAdmin extends Zend_Form
{
    public function init()
    {
        $this->setName('login');

        $user = new Zend_Form_Element_Text('username');
        $user->setLabel('username (email)')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty')
            ->addValidator('EmailAddress', true);

//        $pw = new Zend_Form_Element_Password('password');
//        $pw->setLabel('password')
//            ->setRequired(true)
//            ->addFilter('StripTags')
//            ->addFilter('StringTrim')
//            ->addValidator('NotEmpty');

        $submit = new Zend_Form_Element_Submit('submit');
        //$submit->removeDecorator('label');
        //$submit->setDecorators(array('ViewHelper'));
        $submit->setLabel('Login')->setAttrib('id', 'submitbutton');

        $this->addElements(array($user, $submit));

        //http://richard.parnaby-king.co.uk/2012/04/zend-form-default-decorators/
        $this->setDecorators(array('FormElements', 'Form'));

        //Tell all of our form elements to render only itself and the label
        $this->setElementDecorators(array('ViewHelper', 'Label'));

        $submit = $this->getElement('submit');
        $submit->removeDecorator('label');
    }
}

?>