<?php

use Aws\S3\S3Client;

class S3Controller extends Zend_Controller_Action
{
    public function indexAction() {
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
        
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            )
        ));
        
        // $result = $s3Client->listObjects(array('Bucket' => 'declan-developer-upload/testupload/'));
        
        // foreach ($result['Contents'] as $object) {
        //     echo "<a href='https://s3-eu-west-1.amazonaws.com/declan-developer-upload/{$object['Key']}'>https://s3-eu-west-1.amazonaws.com/declan-developer-upload/{$object['Key']}</a><br>";
        // }

        $objects = $s3Client->getIterator('ListObjects', array(
            "Bucket" => 'declan-developer-upload',
            "Prefix" => 'testupload/' //must have the trailing forward slash "/"
        ));

        foreach ($objects as $object) {
            echo "<a href='https://s3-eu-west-1.amazonaws.com/declan-developer-upload/{$object['Key']}'>https://s3-eu-west-1.amazonaws.com/declan-developer-upload/{$object['Key']}</a><br>";
        }
        
//        $result2 = $s3Client->putObject(array(
//            'Bucket' => 'declan-developer-upload',
//            'Key'    => 'data.txt',
//            'Body'   => 'Hello!'
//        ));
        
//        $result2 = $s3Client->putObject(array(
//            'Bucket' => 'declan-developer-upload',
//            'Key'    => 'Koala.jpg',
//            'SourceFile' => 'C:\Users\Public\Pictures\Sample Pictures\Koala.jpg'
//        ));
//        
//        echo $result2['ObjectURL'] . "\n"; //Test commit
    }
    
    public function uploadAction() {
        
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            )
        ));
        
        if ($this->getRequest()->isPost()) {
            
            //$keyName = basename($_FILES['picture']['name']);
            //$file_location = $_FILES['picture']['tmp_name'];
            
            // Taking in request from angular application and not zend view
            $keyName = basename($_FILES['file']['name']);
            $file_location = $_FILES['file']['tmp_name'];
            
            $upload_img = $s3Client->putObject(array(
                'Bucket' => 'declan-developer-upload',
                'Key'    => 'testupload/'.$keyName, // Example of posting an image to a folder in a bucket
                'SourceFile' => $file_location,
                'ContentType' => $_FILES['file']['type'] // If i dont set the content type the image will download instead of render in the browser
            ));
        
        //echo "<a href='{$upload_img['ObjectURL']}'>{$upload_img['ObjectURL']}</a>";
          if ($upload_img) {
              $this->_helper->json(['status' => 'success']);
          } else {
              $this->_helper->json(['status' => 'fail']);
          }
          
        }
    }
    
    public function paperuploadAction() {
        
        $body = $this->getRequest()->getRawBody();
        $answer = json_encode($this->getRequest());
        $this->_helper->json($answer);
    }
    
    public function testawsurlsAction() {
        die("Testing git orphan branch -- push back to ics computer--push back from new clone");
    }
    
    public function uploadCvAction() {
        
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            )
        ));
        
        if ($_FILES['file']['size'] == 0) {
            
            $this->_helper->json("No image uploaded");
            
        } else {
            
            $keyName = basename($_FILES['file']['name']);
            $file_location = $_FILES['file']['tmp_name'];
            $img_type = $_FILES['file']['type'];
            
            $upload_img = $s3Client->putObject(array(
                'Bucket' => 'declan-developer-upload',
                'Key'    => 'cvupload/'.$keyName, // Example of posting an image to a folder in a bucket
                'SourceFile' => $file_location,
                'ContentType' => $img_type
            ));
        
        
            if ($upload_img) {
                $this->_helper->json(['status' => 'success', 'name' => $keyName, 'file_location' => $file_location, 'type' => $img_type]);
            } else {
                $this->_helper->json(['status' => 'fail']);
            }
            
        }
        
    }
    
    public function imgWithPostDataAction() {
        
        //Below is how to add extra form data within the angular compnent for uploading imgs
        //on change event
        //user_id: any = 1298;
        //this.formData.append('user_id', this.user_id);
        
        $this->_helper->json(array('img_data' => $_FILES, 'form_data' => $_POST));
    }
}

