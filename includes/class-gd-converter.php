<?php

  namespace WebpConverters;

  class GDConverter
  {
    
      /**
     * @ $path : path to original image
     * @ $quality : quality 0-100 
     */
    public function convertImage($path, $quality)
    {

      try {
        $status = $this->checkIfFileExists($path);
        if ($status !== true) throw new \Exception($status);

        $response = $this->createImage($path);
        if (!$response['success']) throw new \Exception($response['message']);
        else $image = $response['data'];

        $response = $this->convertColorPalette($image, $path);
        if (!$response['success']) throw new \Exception($response['message']);
        else $image = $response['data'];

        $newPaht = WEBP_IMAGES_DIR.'/'.basename($path).'.webp';
        $response = $this->convertToWebp($image, $newPaht, $quality);
        if (!$response['success']) throw new \Exception($response['message']);
        else return [
          'success' => true,
          'data'    => $response['data'],
        ];
      } catch (\Exception $e) {
        return [
          'success' => false,
          'message' => $e->getMessage(),
        ];
      }
    }

    private function createImage($path)
    {
      $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
      $methods   = [
        'imagecreatefromjpeg' => ['jpg', 'jpeg'],
        'imagecreatefrompng'  => ['png'],
        'imagecreatefromgif'  => ['gif'],
      ];
      try {
        foreach ($methods as $method => $extensions) {
          if (!in_array($extension, $extensions)) {
            continue;
          } else if (!function_exists($method)) {
            throw new \Exception(sprintf('Server configuration: "%s" function is not available.', $method));
          } else if (!$image = @$method($path)) {
            throw new \Exception(sprintf('"%s" is not a valid image file.', $path));
          }
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

    private function convertColorPalette($image)
    {
      try {
        if (!function_exists('imageistruecolor')) {
          throw new \Exception(sprintf('Server configuration: "%s" function is not available.', 'imageistruecolor'));
        } else if (!imageistruecolor($image)) {
          if (!function_exists('imagepalettetotruecolor')) {
            throw new \Exception(sprintf('Server configuration: "%s" function is not available.', 'imagepalettetotruecolor'));
          }
          imagepalettetotruecolor($image);
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

    private function convertToWebp($image, $path, $quality)
    {
      try {

        if (!function_exists('imagewebp')) {
          throw new \Exception(sprintf('Server configuration: "%s" function is not available.', 'imagewebp'));
        } else if ((imagesx($image) > 8192) || (imagesy($image) > 8192)) {
          throw new \Exception(sprintf('Image is larger than maximum 8K resolution: "%s".', $path));
        } else if (!$success = imagewebp($image, $path, $quality)) {
          throw new \Exception(sprintf('Error occurred while converting image: "%s".', $path));
        }
        imagedestroy($image);

        return [
          'success' => true,
          'data'    => WEBP_IMAGES_URL.'/'.basename($path)
        ];
      } catch (\Exception $e) {
        return [
          'success' => false,
          'message' => $e->getMessage(),
        ];
      }

    }



    public function checkIfFileExists($path)
    {
      if (is_readable($path)) return true;
      else if (!file_exists($path)) return sprintf('File "%s" does not exist. Please check file path using.', $path);
      else return sprintf('File "%s" is unreadable. Please check file permissions.', $path);
    }


  }