<?php

class actions extends connectDb{

    protected $_now;
    
	public function __construct() {
		parent::__construct();
		$this->_now = date("Y-m-d H:i:s");
	}
	
	public function __destruct() {
		parent::__destruct();
	}

    /*
    public function getJsonNbClient($cross){
	//$t = ($cross)?'callback':'';
    	header("Content-type: text/json; charset=utf-8");
		if($cross){
			header('Access-Control-Allow-Origin: *');
			echo 'response('.json_encode($this->getNbClient(), JSON_NUMERIC_CHECK).');';
		}else{
			echo json_encode($this->getNbClient(), JSON_NUMERIC_CHECK);
		}
    }
    */
    
   	/**
	* Send response to client. Any array will be transform into json
	*
	* @param Array $t Any array will be accepted
	*/
	/* ok*/
	private function sendResponse($t){
		//header("Access-Control-Allow-Headers: Content-Type");
        header('Access-Control-Allow-Origin: *');
        //header('Access-Control-Allow-Origin: http://www.krolanta.fr');
        header('Access-Control-Allow-Methods: GET');
        header("Content-type: text/json; charset=utf-8");
        
		echo json_encode($t, JSON_NUMERIC_CHECK);
		
    }
    
    /* ok */
    private function hasPreview($file, $ext = 'jpg'){
		$file = $file.'.'.$ext;
		return file_exists($file)?true:false;
	}
	
	/* ok */
	private function cleanPath($path){
		$this->log->debug("Class ".__CLASS__." | ".__FUNCTION__." | PATH SRC | ".$path);
		
		$final = preg_replace('(^[\/])','',str_replace(PATH_GED,'',$path));
		$this->log->debug("Class ".__CLASS__." | ".__FUNCTION__." | PATH FINAL | ".$final);
		
		return $final;
	}
    
    
    public function explainErrors($type){
		
		$message = Array (
			'INVALIDE_USER' 	=> 'Vous devez etre identifie',
			'WRONG_PATH' 		=> 'Le chemin n\'existe pas !',
			'WRONG_FORMAT' 		=> 'Le format que vous envoye n\'est pas correct.',	
			'FILE_DOESNT_EXIST' => 'Le fichier n\'existe pas.',
			'MUST_BE_A_DIR' 	=> 'Le chemin doit etre un repertoire',
			'PREVIEW_UNKNOWNED'	=> 'Type de preview inconnu.',
			'MUST_BE_NUMERIC'	=> 'Le parametre doit etre une nombre.',
			'MUST_BE_A_FILE' 	=> 'Vous devez demander un fichier, pas un dossier.'		
			);
		
		
		$this->SendResponse(
				['ERROR' => array(
						'code'  => $type,
						'msg'	=> $message[$type])
				]);
    }	
    
    
    public function getFile($file){
		
		if(!file_exists(PATH_GED.'/'.$this->cleanPath($file))) {
			return $this->explainErrors('FILE_DOESNT_EXIST');
		}
		
		if(is_dir(PATH_GED.'/'.$this->cleanPath($file))){
			return $this->explainErrors('MUST_BE_A_FILE');
		}
		
		
		$this->sendResponse(
			array (	'file' => basename($file),
					'url' => EXTERNE_URL.'/'.$this->cleanPath($file),
					'DetailTvShow' => $this->getDetailTvShow($file)
				)
			);
	}
	/*
	public function getPreview($cmd){
		
		if(!array_key_exists('nameFile', $cmd) || !array_key_exists('typePreview', $cmd)){
			return $this->explainErrors('WRONG_FORMAT');
		}
		
		Switch (strtolower($cmd['typePreview'])){
			case 'flv':
				$ext = '.flv';
				break;
			case 'jpg':
				$ext = '.jpg';
				break;
			default:
				return $this->explainErrors('PREVIEW_UNKNOWNED');		
		}
		
		//return fonctions::getFile($cmd['nameFile'].$ext);
		return array (	'file' => basename($cmd['nameFile'].$ext),
						'url' => EXTERNE_URL.'/'.$cmd['nameFile'].$ext
				);
	
	}
	*/
	private function getDetailTvShow($file){
		
		$this->log->debug("Class ".__CLASS__." | ".__FUNCTION__." | ".$file);
		preg_match('#(.*?)[.\s][sS](\d{2})[eE](\d{2}).*#',basename($file),$detail);
		
		return array ( 'TvShow' => isset($detail[1])?$detail[1]:'',
					   'Saison' => isset($detail[2])?$detail[2]:'',
					   'Episode' => isset($detail[3])?$detail[3]:''
				);
		
	}
    
    
	/* ok */	
    public function getLastDownload($dir){
    	
    	$skipDots	  = true;
    	$list	      = [];
        $directory	  = PATH_GED.'/'.$this->cleanPath($dir);
        
		if(!file_exists($directory)) {
			return $this->explainErrors('WRONG_PATH');
		}
		if(!is_dir($directory)){
			return $this->explainErrors('MUST_BE_A_DIR');
		}
		
		$cmd ="ls -t ".$directory;
		exec($cmd, $output);
		
		foreach ($output as $k=>$file){
			$this->log->debug("Class ".__CLASS__." | ".__FUNCTION__." | FILE | ".$file);
		if ((
                    $file != "." 
                    && $file != ".." 
                    && substr($file,0,1)!="." 
                 ) || $skipDots == false 
             ){
                $file = $directory.'/'.$file;
                 
                if(@is_dir($file))
                {
                    array_push(
                        $list,
                        Array(
                        	'folder'   => true,
                            'name'     => basename($file)
                        )
                    );
                
                } else{
                    //test si le fichier est un ScreenCapt/flv/soustitre zip
                    if( file_exists(substr($file,0,-4))) continue;
                    
                    array_push(
                        $list,
                        Array(
                        	'folder'   	=> false,	
                            'name'     	=> basename($file),
                            'url' 		=> EXTERNE_URL.'/'.$this->cleanPath($file),
                        	'mosaic' 	=> $this->hasPreview($file,'jpg'),
                        	'sample'   	=> $this->hasPreview($file,'mp4')
                            ) //,'DetailTvShow'	   	=> $this->getDetailTvShow($file)
                     );
                }
                
             }
                
        }
      
        $this->sendResponse(
        	['path'	=>	$dir,'list'	=>	$list]
        );
    	
    	
    }
    
}