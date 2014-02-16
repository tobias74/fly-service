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
      
  protected function getFlySpecForSize($size,$format)
  {
    $flySpec = new FlyImageSpecification();
    
    switch ($format)
    {
      case 'original':
        $flySpec->setMode(FlyImageSpecification::TOUCH_BOX_FROM_INSIDE);
        break;
        
      case 'square':
        $flySpec->setMode(FlyImageSpecification::TOUCH_BOX_FROM_OUTSIDE);
        break;
        
      default:
        throw new \ErrorException('no format given');
        
    }
    
    
    switch ($size)
    {
      case "small": 
        $flySpec->setMaximumWidth(100);
        $flySpec->setMaximumHeight(100);
        break;
        
      case "medium": 
        $flySpec->setMaximumWidth(300);
        $flySpec->setMaximumHeight(300);
        break;
        
      case "big": 
        $flySpec->setMaximumWidth(800);
        $flySpec->setMaximumHeight(800);
        break;
        
      default:
        throw new ErrorException('Coding problem in zeitafrden fadcede');
    }
    
    return $flySpec;
  }
    
  
  public function getFlyImageAction()
  {
    $imageUrl = $this->_request->getParam('imageUrl','');
    $imageSize = $this->_request->getParam('imageSize','medium');
    $format =  $this->_request->getParam('format','original');
    
    $gridFile = $this->getFlyImageService()->getFlyGridFile($imageUrl, $this->getFlySpecForSize($imageSize,$format));
    $fileTime = $gridFile->file['uploadDate']->sec;

    $this->_response->addHeader('Content-type: image/jpeg');
    $this->_response->addHeader('Content-Length: '.$gridFile->getSize());
    $this->_response->addHeader('Cache-Control: maxage='.(60*60*24*31));
    $this->_response->addHeader('Last-Modified: '.gmdate('D, d M Y H:i:s',$fileTime).' GMT',true,200);
    $this->_response->addHeader('Expires: '.gmdate('D, d M Y H:i:s',time()+60*60*24*31).' GMT',true,200);
    $this->_response->setStream($gridFile->getResource());
        
  }        
    
    
  public function getFlyImageIdAction()
  {
    $imageUrl = $this->_request->getParam('imageUrl','');
    $imageSize = $this->_request->getParam('imageSize','medium');
    $format =  $this->_request->getParam('format','original');

    try
    {
      $gridFile = $this->getFlyImageService()->getFlyGridFile($imageUrl, $this->getFlySpecForSize($imageSize,$format));
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



        
        
        
        
        
        
        
        
        
        