<?php

use Aws\S3\S3Client;

class S3Controller extends Zend_Controller_Action
{
    public function indexAction() {
        
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            )
        ));
        
        $result = $s3Client->listObjects(array('Bucket' => 'declan-developer-upload'));
        
        foreach ($result['Contents'] as $object) {
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
            
            $keyName = basename($_FILES['picture']['name']);
            $file_location = $_FILES['picture']['tmp_name'];
            
            $upload_img = $s3Client->putObject(array(
                'Bucket' => 'declan-developer-upload',
                'Key'    => 'personal/'.$keyName, // Example of posting an image to a folder in a bucket
                'SourceFile' => $file_location,
                'ContentType' => $_FILES['picture']['type'] // If i dont set the content type the image will download instead of render in the browser
            ));
        
        echo "<a href='{$upload_img['ObjectURL']}'>{$upload_img['ObjectURL']}</a>";
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
}

