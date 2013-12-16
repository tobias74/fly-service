<?php 

class ZeitfadenFlyImageService 
{
	

  public function __construct()
  {
    $this->collectionName = 'fly_images';
    $this->mongoConnection = new \MongoClient();
    $this->mongoDb = $this->mongoConnection->fly_service;
    
    $name = $this->collectionName;
    $this->collection = $this->mongoDb->$name;
    
    $this->collection->ensureIndex(array('serialized_specification' => 1));
        
  }
	
  
  public function setProfiler($profiler)
  {
    if (!is_object($profiler))
    {
      throw new \ErrorException('profiler is not an object?'.$profiler);
    }
    $this->profiler = $profiler;
  }
  
	protected function createAndMergeFly($imageIdUrl, $flySpec)
	{
	  //
    $timer = $this->profiler->startTimer('creating new fly-images');
    $flyDocument = $this->createFly($imageIdUrl, $flySpec);
    $timer->stop();
    return $flyDocument;
	  	  
	}
		
  public function getFlyGridFile($imageIdUrl, $flySpec)
	{
	  $flyDocument = $this->getFly($imageIdUrl, $flySpec);
    $gridFS = $this->mongoDb->getGridFS();
    return $gridFS->findOne(array('metadata.fly_id' => $flyDocument['_id']));
	  	  
	}			
		
	public function getFly($imageIdUrl, $flySpec)
	{
		
		$serializedSpec = $flySpec->serialize();
		
		$flyDocument = $this->collection->findOne(array('image_id_url'=> $imageIdUrl, 'serialized_specification' => $serializedSpec));
    
		if (!$flyDocument)
		{
		  $flyDocument = $this->createAndMergeFly($imageIdUrl, $flySpec);
		}
		
		return $flyDocument;
	}
	
	public function getResourceForFly($flyDocument)
	{
    $gridFS = $this->mongoDb->getGridFS();
    $file = $gridFS->findOne(array('metadata.fly_id' => $flyDocument['_id']));
    return $file->getResource();
	}
	
  public function getBytesForFly($flyDocument)
  {
    $gridFS = $this->mongoDb->getGridFS();
    $file = $gridFS->findOne(array('metadata.fly_id' => $flyDocument['_id']));
    return $file->getBytes();
  }
	
	protected function createFly($imageIdUrl, $flySpec)
	{
		$timer = $this->profiler->startTimer('creating new fly-images');
		
    try
    {
			$origi = imagecreatefromstring(file_get_contents($imageIdUrl));
    }
    catch (Exception $e)
    {
      error_log('copuld not find image '.$imageIdUrl);	
      die();
      $origi=ImageCreate(150,150);
      $bgc=ImageColorAllocate($origi,255,255,255);
      $tc=ImageColorAllocate($origi,0,0,0);
      ImageFilledRectangle($origi,0,0,150,150,$bgc);
      ImageString($origi,1,5,10,"Error loading Image ".$imageIdUrl,$tc);
    }
        
	    	    
    switch ($flySpec->getMode())
    {
    	case FlyImageSpecification::TOUCH_BOX_FROM_INSIDE:
    		
		    $originalWidth = imagesx($origi);
		    $originalHeight = imagesy($origi);
		    
		    $newWidth = $flySpec->getMaximumWidth();
		    $newHeight = (int) (($flySpec->getMaximumWidth() / $originalWidth) * $originalHeight);
		    if ($newHeight > $flySpec->getMaximumHeight())
		    {
		        $newHeight = $flySpec->getMaximumHeight();
		        $newWidth = (int) (($flySpec->getMaximumHeight() / $originalHeight) * $originalWidth);
		    }
	
		    $im = imagecreatetruecolor($newWidth, $newHeight);
		    imagecopyresampled($im,$origi,0,0,0,0, $newWidth, $newHeight, $originalWidth ,$originalHeight);
    		
		    
    		break;
    		
    	default:
    		throw new Exception("no fly image mode chosen?");
    }
	    
    
    $document = array(
      'image_id_url' => $imageIdUrl,
      'newWidth' => $newWidth,
      'newHeight' => $newHeight,
      'specification' => $flySpec->getHash(),
      'serialized_specification' => $flySpec->serialize()
    );
    
    $this->collection->insert($document);
	    		

	  $fileName = tempnam('/tmp','flyfiles');  
    if (!imagepng($im, $fileName ))
    {
      throw new ErrorException("we could not save the image fly file.");
    }
        
    $gridFS = $this->mongoDb->getGridFS();
    $hash = array();
    $hash['fly_id'] = $document['_id'];
    $hash['fly_collection_name'] = $this->collectionName;
    $hash['fly_content_type'] = 'image/png';
    $hash['type'] = 'image/png';

    $gridFS->storeFile($fileName,array("metadata" => $hash));
	  
	  
	  return $document;  						
	}

	
	
}





class FlyImageSpecification
{
	const TOUCH_BOX_FROM_INSIDE = "touch from inside";
	
	protected $maximumWidth;
	protected $maximumHeight;
	protected $mode;
	
	public function getMode()
	{
		return $this->mode;
	}
	
	public function setMode($val)
	{
		$this->mode = $val;
	}
	
	public function getMaximumWidth()
	{
		return $this->maximumWidth;
	}
	
	public function setMaximumWidth($val)
	{
		$this->maximumWidth = $val;
	}
	
	public function getMaximumHeight()
	{
		return $this->maximumHeight;
	}
	
	public function setMaximumHeight($val)
	{
		$this->maximumHeight = $val;
	}
	
	public function getHash()
	{
	  return array(
      'maximumWidth' => $this->maximumWidth,
      'maximumHeight' => $this->maximumHeight,
      'mode' => $this->mode
    );
	}
	public function serialize()
	{
		return json_encode($this->getHash());
	}
	
}






