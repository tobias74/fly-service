<?php 


// http://flyservice.butterfurz.de/image/getFlyImages/imageSize/small?imageUrl=http://idlelive.com/wp-content/uploads/2013/06/1dd45_celebrity_incredible-images-from-national-geographics-traveler-photo-contest.jpg


class VideoController extends AbstractZeitfadenController
{
            
  protected function declareDependencies()
  {
    return array_merge(array(
      'FlyVideoService' => 'flyVideoService',
    ), parent::declareDependencies());  
  }
      
  protected function getFlySpecForVideo($quality,$format)
  {
    $flySpec = new FlyVideoSpecification();
    $flySpec->format = $format;
    $flySpec->quality = $quality;
    return $flySpec;
  }
    
  public function transcodeAction()
  {
    $flyId = $this->_request->getParam('flyId','');

    error_log('perform transcoding...');
    $this->getFlyVideoService()->performTranscoding($flyId);
    
  }
  

  public function getFlyVideoIdAction()
  {
    $videoUrl = $this->_request->getParam('videoUrl','');
    $quality = $this->_request->getParam('quality','medium');
    $format = $this->_request->getParam('format','webm');
    //$imageUrl = 'http://goldenageofgaia.com/wp-content/uploads/2012/12/Field-flowers-image8.jpg';
    try
    {
      $gridFile = $this->getFlyVideoService()->getFlyGridFile($videoUrl, $this->getFlySpecForVideo($quality,$format));
      $name='$id';
      $this->_response->appendValue('gridFileId', $gridFile->file['_id']->$name);
      //$this->_response->appendValue('hostName', $gridFile->file['_id']->getHostname());
      $this->_response->appendValue('collectionName','fly_service');
      $this->_response->appendValue('mongoServerIp','localhost');
      $this->_response->appendValue('done',1);
    }
    catch (\Exception $e)
    {
      $this->_response->appendValue('done',0);
      die('send back default video file with message to wait: '.$e->getMessage());
    }
  }        


  
  public function getFlyVideoAction()
  {
    $videoUrl = $this->_request->getParam('videoUrl','');
    $quality = $this->_request->getParam('quality','medium');
    $format = $this->_request->getParam('format','webm');
    //$imageUrl = 'http://goldenageofgaia.com/wp-content/uploads/2012/12/Field-flowers-image8.jpg';
    
    try
    {
      
      $gridFile = $this->getFlyVideoService()->getFlyGridFile($videoUrl, $this->getFlySpecForVideo($quality,$format));
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
  
}



        
        
        
        
        
        
        
        
        
        