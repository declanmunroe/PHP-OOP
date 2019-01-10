<?php

use Aws\S3\S3Client;

class S3Controller extends Zend_Controller_Action
{
    public function indexAction() {
//        if(isset($_FILES['image'])){
//		$file_name = $_FILES['image']['name'];   
//		$temp_file_location = $_FILES['image']['tmp_name']; 
//
//		//require 'vendor/autoload.php';
//
//		$s3 = new Aws\S3\S3Client([
//			'region'  => 'eu-west-1',
//			'version' => 'latest',
//			'credentials' => [
//				'key'    => "AKIAIFQ5FAMYGBOJLG2Q",
//				'secret' => "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",
//			]
//		]);		
//
//		$result = $s3->putObject([
//			'Bucket' => 'declan-developer-upload',
//			'Key'    => $file_name,
//			'SourceFile' => $temp_file_location			
//		]);
//
//		var_dump($result);
//	}
        
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            )
        ));
        
        $result = $s3Client->listObjects(array('Bucket' => 'declan-developer-upload'));
        
        foreach ($result['Contents'] as $object) {
            echo $object['Key'];
        }
        //die(var_dump($result['Contents']['Key']));
    }
}

