<?php 
  namespace WebpConverters;

  class ImagickConverter
  {

    public function convertToWebp($path){
        try{
            $appDir = rtrim(ABSPATH, '/');
            $response = $this->createImage($appDir.$path);
            $fileName = basename($path);

            if (!$response['success']) throw new \Exception($response['message']);
            else $image = $response['data'];
            
            $image->setImageFormat('WEBP');
            $image->stripImage();
            $image->setImageCompressionQuality(80);
            $blob = $image->getImageBlob();
            $success = file_put_contents(WEBP_IMAGES_DIR.'/'.$fileName.'.webp', $blob);

            if($success)
                return array(
                  'success'=> true, 
                  'data'=> WEBP_IMAGES_URL.'/'.$fileName.'.webp'
                );
            else
                return false;
        }catch(\Exception $e){
            return array('success'=>false, 'message'=>$e->getMessage());
        }

    }


    private function createImage($path)
    {
      $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
      try {
        if (!extension_loaded('imagick') || !class_exists('Imagick')) {
          throw new \Exception('Server configuration: Imagick module is not available with this PHP installation.');
        } else if (!$image = new \Imagick($path)) {
          throw new \Exception(sprintf('"%s" is not a valid image file.', $path));
        }
        if (!isset($image)) {
          throw new \Exception(sprintf('Unsupported extension "%s" for file "%s"', $extension, $path));
        }

        return [
          'success' => true,
          'data'    => $image,
        ];
      } catch (\Exception $e) {
        return [
          'success' => false,
          'message' => $e->getMessage(),
        ];
      }
    }

}