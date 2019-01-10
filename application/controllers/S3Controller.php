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
            echo "<a href='https://s3-eu-west-1.amazonaws.com/declan-developer-upload/{$object['Key']}'>https://s3-eu-west-1.amazonaws.com/declan-developer-upload/{$object['Key']}</a>";
        }
    }
}

