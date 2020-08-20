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
        
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            )
        ));
        
        if (empty($_FILES) || ($_FILES['file']['size'] == 0)) {
            
            $this->_helper->json("No image uploaded");
            
        } else {
            #$originalFileName is the name of the file to be uploaded (s3_document.txt)
            $originalFileName = basename($_FILES['file']['name']);
            #Generate a unique identifier
            $uniqueId = md5(uniqid(rand(), true));
            
            #Generate a new file name which will be uniqueId, dierctory (in this case .) and file extension (txt)
            #Produces 23a833e89122d1eaf5464e13313c3a0c.txt
            $newFileName = $uniqueId.pathinfo($originalFileName)['dirname'].pathinfo($originalFileName)['extension'];
            
            #Get the location of file to be uploaded
            $file_location = $_FILES['file']['tmp_name'];
            #Get the file type
            $file_type = $_FILES['file']['type'];
            
            #upload image to s3 bucket
            $upload_img = $s3Client->putObject(array(
                'Bucket' => 'declan-developer-upload',
                'Key'    => 'cvupload/'.$newFileName, // Example of posting an image to a folder in a bucket
                'SourceFile' => $file_location,
                'ContentType' => $file_type
            ));
        
        
            if ($upload_img) {
                
                $user_id = $_POST['user_id']; // capture the user_id that was appended to the angular form. See above at top of function
                $document_id = $newFileName;
                $description = $originalFileName;
                $stub = 'https://iitpsa.s3-eu-west-1.amazonaws.com/uploads/';
                $uploaded_dt = new Zend_Db_Expr('NOW()');
                
                // Stop inserting into table
                //$db_doc = new Zend_Db_Table('upload_documents');

                //$db_doc->insert(compact('user_id', 'document_id', 'description', 'stub', 'uploaded_dt'));
                
                $this->_helper->json(['status' => 'success', 'user_id' => $user_id, 'document_id' => $document_id, 'description' => $description, 'stub' => $stub]);
                
            } else {
                $this->_helper->json(['status' => 'fail']);
            }
            
        }
        
    }
    
    public function deleteDocumentAction()
    {
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            )
        ));
        
        #https://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.S3.S3Client.html#_deleteObject
        #https://docs.aws.amazon.com/AmazonS3/latest/API/API_DeleteObject.html
        # In the docs it does also show you how to delete multiple objects with one request
        $result = $s3Client->deleteObject(array(
            // Bucket is required
            'Bucket' => 'declan-developer-upload',
            // Key is required
            'Key' => 'cvupload/23a833e89122d1eaf5464e13313c3a0c.JPG'
            // There are other parameters but we dont need them. See docs above
        ));
        
        #The response is the same wheather is successfully deletes an object or doesn't
        die(print_r($result));
        
        #When uploading images to s3 bucket, we should also append a timestamp to the unique id so file name no matter how unlikely will never be the same.
        #When uploading or deleting files from aws s3 buckets, if the same file name is found amazon will copy over already existant file with same name
        #This is why we should produce a file name with a unique code and time stamp so this scenario never happens
    }
}

