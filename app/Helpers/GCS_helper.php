<?php

namespace App\Helpers;

use Google\Cloud\Storage\StorageClient;
use Google\Cloud\ServiceBuilder;
use Illuminate\Support\Facades\Config;

class GCS_helper {

    static public function upload_to_gcs($file_path) {
        $gcloud = new ServiceBuilder([
            'keyFilePath' => Config::get('constants.gcs_key'),
            'projectId' => Config::get('constants.gcs_bucket_name')
        ]);

        // Fetch an instance of the Storage Client
        $storage = $gcloud->storage();

        $bucket = $storage->bucket(Config::get('constants.gcs_bucket_name'));

        // Upload a file to the bucket.
        try {
            $bucket->upload(
                    fopen($file_path, 'r'), [
                'predefinedAcl' => 'publicRead'
                    ]
            );
        } catch (Exception $e) {
            return FALSE;
        }

        return TRUE;
    }

    static public function delete_from_gcs($objectName) {

        $storage = new StorageClient([
            'keyFilePath' => Config::get('constants.gcs_key'),
            'projectId' => Config::get('constants.gcs_bucket_name')
        ]);
        $bucket = $storage->bucket(Config::get('constants.gcs_bucket_name'));
        $object = $bucket->object($objectName);
        try {
            $object->delete();
        } catch (Exception $e) {
            return FALSE;
        }

        return TRUE;
    }
    
    static public function download_object($objectName, $destination) {
        $storage = new StorageClient([
            'keyFilePath' => Config::get('constants.gcs_key'),
            'projectId' => Config::get('constants.gcs_bucket_name')
        ]);
        $bucket = $storage->bucket(Config::get('constants.gcs_bucket_name'));
        $object = $bucket->object($objectName);
        $object->downloadToFile($destination);
    }

}
