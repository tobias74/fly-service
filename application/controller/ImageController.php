<?php 


// http://flyservice.butterfurz.de/image/getFlyImages/imageSize/small?imageUrl=http://idlelive.com/wp-content/uploads/2013/06/1dd45_celebrity_incredible-images-from-national-geographics-traveler-photo-contest.jpg


class ImageController extends AbstractZeitfadenController
{
            
  protected function declareDependencies()
  {
    return array_merge(array(
      'FlyImageService' => 'flyImageService',
    ), parent::declareDependencies());  
  }

  protected function getCacheOptions($request)
  {
    $cacheOptions = new ImageCacheOptions();

    if ($request->hasParam('expirationTimestamp'))
    {
      $cacheOptions->setExpirationTimestamp($request->getParam('expirationTimestamp',0));
    }
    else 
    {
      $timeToLive = $request->getParam('timeToLive',3600);
      $cacheOptions->setTimeToLive($timeToLive);
    }
    
    return $cacheOptions;
  }
      
  
  protected function getImageSize()
  {
    $imageSize = $this->_request->getParam('imageSize','original');


  	if ($imageSize === 'custom')
  	{
  		$width = $this->_request->getParam('maxWidth',100);		
  		$height = $this->_request->getParam('maxHeight',100);		
  		$imageSize = array(
  			'height' => $height,
  			'width' => $width
  		);
  	}
  	
    
  	return $imageSize;
	  	
  }
  
  public function getCachedImageAction()
  {
    
    $format =  $this->_request->getParam('format','original');
    $imageUrl = $this->_request->getParam('imageUrl','');
    $imageSize = $this->getImageSize();
    
    $gridFile = $this->getFlyImageService()->getFlyGridFile($imageUrl, $this->getFlyImageService()->getFlySpecForSize($imageSize,$format), $this->getCacheOptions($this->_request));
    $fileTime = $gridFile->file['uploadDate']->sec;

    
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
    {
      error_log('we did get the http if modiefed...');
      if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $fileTime)
      {
        error_log('and we answered, not modified');
        header('HTTP/1.0 304 Not Modified');
        exit;
      }
      else
      {
        error_log('and we answered, yes modified, continue loading.');
      }
    }  


    $this->_response->addHeader('Content-type: image/jpeg');
    $this->_response->addHeader('Content-Length: '.$gridFile->getSize());
    $this->_response->addHeader('Cache-Control: maxage='.(60*60*24*31));
    $this->_response->addHeader('Last-Modified: '.gmdate('D, d M Y H:i:s',$fileTime).' GMT',true,200);
    $this->_response->addHeader('Expires: '.gmdate('D, d M Y H:i:s',time()+60*60*24*31).' GMT',true,200);
    $this->_response->setStream($gridFile->getResource());
        
  }        
    
  public function removeExpiredImagesAction()
  {
    error_log('removing expired images');
    $this->getFlyImageService()->removeExpiredImages();
  }  
    
  public function getFlyImageIdAction()
  {
    $format =  $this->_request->getParam('format','original');
    $imageUrl = $this->_request->getParam('imageUrl','');
    $imageSize = $this->getImageSize();

    $hash = $this->getFlyImageService()->getCachedImageData($imageUrl, $this->getFlyImageService()->getFlySpecForSize($imageSize,$format), $this->getCacheOptions($this->_request));
    
    $this->_response->setHash($hash);
  }        

  
  public function serveFileByIdAction()
  {
    
    $userId = $this->_request->getParam('userId',0);
    $fileId = $this->_request->getParam('fileId',0);

    $file = $this->sessionFacade->getFileById($fileId, $userId);
    $fileContent = $this->sessionFacade->getFileContent($file);
    $this->_response->disable();
    
    header("Content-Disposition: attachment; filename=".$file->getFileName());
    header("Content-type: ".$file->getFileType());
    //print_r($this->attachment);

    echo $fileContent;
    
  }
  
  public function clearAllAction()
  {
    $imageUrl = $this->_request->getParam('imageUrl','');
    $this->getFlyImageService()->deleteAllFlys($imageUrl);
  	
  }
  
}



        
        
        
        
        
        
        
        
        
        