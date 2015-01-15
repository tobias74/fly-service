<?php 


// http://flyservice.butterfurz.de/image/getFlyImages/imageSize/small?imageUrl=http://idlelive.com/wp-content/uploads/2013/06/1dd45_celebrity_incredible-images-from-national-geographics-traveler-photo-contest.jpg


class ImageController extends AbstractZeitfadenController
{
            
  protected function declareDependencies()
  {
    return array_merge(array(
      'OAuth2Service' => 'oAuth2Service',
      'StationOrderer' => 'stationOrderer',
      'SearchHelperProvider' => 'searchHelperProvider',
      'DatabaseShard' => 'dbShard',
      'FlyImageService' => 'flyImageService',
      'AttachmentHelperProvider' => 'attachmentHelperProvider'
    ), parent::declareDependencies());  
  }

  protected function getCacheOptions($timeToLive)
  {
    $cacheOptions = new ImageCacheOptions();
    $cacheOptions->setTimeToLive($timeToLive);
    return $cacheOptions;
  }
      
  protected function getFlySpecForSize($size,$format)
  {
    $flySpec = new FlyImageSpecification();
    
    switch ($format)
    {
      case 'original':
        $flySpec->setMode(FlyImageSpecification::TOUCH_BOX_FROM_INSIDE);
        $faktor = 1;
        break;
        
      case 'square':
        $flySpec->setMode(FlyImageSpecification::TOUCH_BOX_FROM_OUTSIDE);
        $faktor = 1;
        break;

      case '4by3':
        $flySpec->setMode(FlyImageSpecification::TOUCH_BOX_FROM_OUTSIDE);
        $faktor = 4/3;
        break;

      case '9by6':
        $flySpec->setMode(FlyImageSpecification::TOUCH_BOX_FROM_OUTSIDE);
        $faktor = 9/6;
        break;
        
      default:
        throw new \ErrorException('no format given');
        
    }
    
    if (is_string($size))
	{
	    switch ($size)
	    {
	      case "small": 
	        $flySpec->setMaximumWidth(100*$faktor);
	        $flySpec->setMaximumHeight(100);
	        break;
	        
	      case "medium": 
	        $flySpec->setMaximumWidth(300*$faktor);
	        $flySpec->setMaximumHeight(300);
	        break;
	        
	      case "big": 
	        $flySpec->setMaximumWidth(800*$faktor);
	        $flySpec->setMaximumHeight(800);
	        break;
	        
	      default:
	        throw new ErrorException('Coding problem in zeitafrden fadcede');
	    }
	}
	else
	{
        $flySpec->setMaximumWidth($size['width']*$faktor);
        $flySpec->setMaximumHeight($size['height']);
	}
    
    return $flySpec;
  }
  
  protected function getImageSize()
  {
    $imageSize = $this->_request->getParam('imageSize','medium');


	if ($imageSize === 'custom')
	{
		$width = $this->_request->getParam('width',100);		
		$height = $this->_request->getParam('height',100);		
		$imageSize = array(
			'height' => $height,
			'width' => $width
		);
	}
	
	return $imageSize;
	  	
  }
  
  public function getFlyImageAction()
  {
    error_log('111111112222gut hier.');
  	
    
    
    $format =  $this->_request->getParam('format','original');
    $imageUrl = $this->_request->getParam('imageUrl','');
    $imageSize = $this->getImageSize();
    $timeToLive = $this->_request->getParam('timeToLive',3600);
    
    $gridFile = $this->getFlyImageService()->getFlyGridFile($imageUrl, $this->getFlySpecForSize($imageSize,$format), $this->getCacheOptions($timeToLive));
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
    $timeToLive = $this->_request->getParam('timeToLive',3600);

    
    try
    {
      $gridFile = $this->getFlyImageService()->getFlyGridFile($imageUrl, $this->getFlySpecForSize($imageSize,$format), $this->getCacheOptions($timeToLive));
      $name='$id';
      $this->_response->appendValue('gridFileId', $gridFile->file['_id']->$name);
      $this->_response->appendValue('collectionName','fly_service');
      $this->_response->appendValue('mongoServerIp',$_SERVER['SERVER_NAME']);
      $this->_response->appendValue('done',1);
    }
    catch (\Exception $e)
    {
      $this->_response->appendValue('done',0);
      error_log('send back default video file with message to wait: '.$e->getMessage());
    }
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



        
        
        
        
        
        
        
        
        
        