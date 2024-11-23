<?php

namespace App\Traits;
use Illuminate\Support\Facades\Log;
use Exception, Storage, File;

trait UploadFileTrait
{
    //upload media in local system
    public function uploadFileOnLocal($file,$directoryName = "image") {
        try{
            $profileFolder = "/uploads/$directoryName";
            if (!File::exists($profileFolder)) {
                Storage::disk('public')->makeDirectory($profileFolder, 0777);
            }     
            $filePathStr = Storage::disk('public')->put($profileFolder, $file); 
            $filePathArr = !empty($filePathStr)?explode('/',$filePathStr):[];
            $response = [];
            $fileName = NULL;
            $response['status'] = false;
            if(!empty(end($filePathArr))) {
                $fileName = end($filePathArr);
                $response['status'] = true;
                $response['file_name'] = $fileName;
            }
            return $response;
        } catch(Exception $e){
            $response = [];
            $response['status'] = false;
            Log::error('Exception in the Uploading image');
            Log::error($e);
            return $response;
        }
        
    }
    
    //remove media from local system
    public function removeFileOnLocal($file,$directoryName = "image") {
        try{ 
            $filePath = "/uploads/$directoryName/$file";
            $response['status'] = false;
            if (Storage::disk('public')->exists($filePath) && !empty($filePath)) {
                $status  = Storage::disk('public')->delete($filePath);
                $response['status'] = true;
            } 
            return $response;
        } catch(Exception $e){
            $response = [];
            $response['status'] = false;
            Log::info('Exception in the remove image');
            Log::info($e);
            return $response;
        }
        
    }

    //upload media in aws bucket
    public function fileUploadToAws($file,$bucketDirName = "image") { 
        try{
            $awsResponse = \Storage::disk('s3')->put($bucketDirName, $file);
            $filePath = \Storage::disk('s3')->url($awsResponse);
            $response = [];
            //file upload error
            if(empty($filePath)) {
                $response['status'] = false;
                return $response;
            }

            //file upload success
            if(!empty($filePath)) {
                $response['status'] = true;
                $response['file_name'] = $filePath;
                return $response;
            }
        } catch(Exception $e){
            Log::error('Exception in the upload image in aws');
            Log::error($e);
            $response = [];
            $response['status'] = false;
            return $response;
        }
    }

    //remove media from aws bucket
    public function removeFileFromAws($fileName,$bucketDirName = "image") { 
        try{
            $response = [];
            if(empty($fileName)) {
                $response['status'] = false;
                $response['message'] = "Filename is empty";
                return $response;
            }
            $fileNameArr = explode("/",$fileName);
            $newFileName = end($fileNameArr);

            $action = \Storage::disk('s3')->delete($bucketDirName.'/'. $newFileName);
            if($action == 1) {
                $response['status'] = true;
                $response['message'] = "File removed successfully.";
            } else {
                $response['status'] = false;
                $response['message'] = "Filename not deleted";
            }
            return $response;

        } catch(Exception $e){
            Log::error('Exception in the remove image aws');
            Log::error($e);
            $response = [];
            $response['status'] = false;
            return $response;
        }
    }
}