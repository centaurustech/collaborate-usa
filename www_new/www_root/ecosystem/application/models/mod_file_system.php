<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_File_System extends Mod_Utilities {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_File_System loaded.");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function create_file($config = array(), $resize = false, $width = 100, $height = 100){
        
        // set result for return
        $result = array("status" => false, "upload_data" => array(), "error" => "");
        
        // load ci media upload library
		$this->load->library('upload', $config);                
        
        if( ! $this->upload->do_upload() ){
            
            $result["error"] = strip_tags($this->upload->display_errors());
            
        }
        else{
            $upload_data = $this->upload->data();
            
            if($resize){
                //resize:
                $config['image_library'] = 'gd2';
                $config['source_image'] = $upload_data['full_path'];
                $config['maintain_ratio'] = TRUE;
                $config['width']     = $width;
                $config['height']   = $height;
        
                $this->load->library('image_lib', $config);
                $this->image_lib->resize(); 
            }            
            
            $result["status"] = true;
            $result["upload_data"] = (object)$upload_data;                                    
        }
        
        return $result;
    }        
    
    public function create_folder($path = '', $folder_name = 'folder', $overwrite = false){
        
        // set return result
        $result = array("status" => false, "message" => "");
        
        // path is exists
        if(is_dir($path)){
            
            // check overwrite is true
            if($overwrite){
                
                // make folder
                @mkdir($path . '/' . $folder_name, 0777);
                
                // check folder successfully created
                if(is_dir($path . '/' . $folder_name)){
                    $result["status"] = true;
                    $result["message"] = "$folder_name successfully created in $path.";       
                }
                
                // folder can't created
                else{
                    $result["message"] = "$folder_name not created.";
                }
            }
            
            // else check shouldnot be overwrite.
            else{
                
                // check folder is exists
                if(is_dir($path . '/' . $folder_name)){
                    $result["status"] = true;
                    $result["message"] = "$folder_name already exists in $path.";                    
                }
                
                // else create folder
                else{
                    
                    // make folder
                    @mkdir($path . '/' . $folder_name, 0777);
                    
                    // check folder successfully created
                    if(is_dir($path . '/' . $folder_name)){
                        $result["status"] = true;
                        $result["message"] = "$folder_name successfully created in $path.";       
                    }
                    
                    // folder can't created
                    else{
                        $result["message"] = "$folder_name not created.";
                    }
                }
            }
        }
        
        // else path not exists
        else{
            $result["message"] = "Path not exists or invalid.";
        }
        
        return $result;
    }
}
