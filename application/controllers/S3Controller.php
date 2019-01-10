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
            echo $object['Key'];
        }
    }
}

