<?php 


// http://flyservice.butterfurz.de/image/getFlyImages/imageSize/small?imageUrl=http://idlelive.com/wp-content/uploads/2013/06/1dd45_celebrity_incredible-images-from-national-geographics-traveler-photo-contest.jpg


class VideoController extends AbstractZeitfadenController
{
      
  public function setVideoCacheServiceProvider($val)
  {
    $this->videoCacheServiceProvider = $val;
  }
  
  protected function getVideoCacheService()
  {
    if (!isset($this->videoCacheService))
    {
      $this->videoCacheService = $this->videoCacheServiceProvider->provide(array(
        'mongo_db_host' => 'services.zeitfaden.com'
      ));
    }
    return $this->videoCacheService;
  }
      
  protected function getFlySpecForVideo($quality,$format)
  {
    $flySpec = new \CachedImageService\FlyVideoSpecification();
    $flySpec->format = $format;
    $flySpec->quality = $quality;
    return $flySpec;
  }
    
  public function transcodeAction()
  {
    $flyId = $this->_request->getParam('flyId','');

    error_log('perform transcoding...');
    $this->getVideoCacheService()->performTranscoding($flyId);
    
  }
  

  public function getFlyVideoIdAction()
  {
    $videoUrl = $this->_request->getParam('videoUrl','');
    $quality = $this->_request->getParam('quality','medium');
    $format = $this->_request->getParam('format','webm');
    //$imageUrl = 'http://goldenageofgaia.com/wp-content/uploads/2012/12/Field-flowers-image8.jpg';
    
    $hash = $this->getVideoCacheService()->getCachedVideoData($videoUrl, $this->getFlySpecForVideo($quality,$format));
    $this->_response->setHash($hash);
  }        


  
  public function getFlyVideoAction()
  {
    $videoUrl = $this->_request->getParam('videoUrl','');
    $quality = $this->_request->getParam('quality','medium');
    $format = $this->_request->getParam('format','webm');
    //$imageUrl = 'http://goldenageofgaia.com/wp-content/uploads/2012/12/Field-flowers-image8.jpg';
    
    try
    {
      
      $gridFile = $this->getVideoCacheService()->getFlyGridFile($videoUrl, $this->getFlySpecForVideo($quality,$format));
      $fileTime = $gridFile->file['uploadDate']->sec;
      $this->_response->addHeader('Content-Length: '.$gridFile->getSize());
      $this->_response->addHeader('Last-Modified: '.gmdate('D, d M Y H:i:s',$fileTime).' GMT',true,200);
      $this->_response->setStream($gridFile->getResource());
      $this->_response->addHeader('Cache-Control: maxage='.(60*60*24*31));
      $this->_response->addHeader('Expires: '.gmdate('D, d M Y H:i:s',time()+60*60*24*31).' GMT',true,200);
    }
    catch (\Exception $e)
    {
      die('send back default video file with message to wait: '.$e->getMessage());
    }
    
    $this->_response->addHeader('Content-type: video/'.$format);
        
  }        
      
  
  public function helloAction()
  {
    die('hello');
  }  
  
  
}



        
        
        
        
        
        
        
        
        
        