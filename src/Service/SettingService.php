<?php
namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SettingService {

    private $path;

    private $setting;

    const PAST_PAPER_HEADER = "past-paper-header";

    /**
     * SettingService constructor.
     * @param $path string
     */
    public function __construct($path = null)
    {
        $this->path = $path;
        $this->setting = json_decode(file_get_contents($this->path),true);
    }

    public function get($key){
        return $this->setting[$key]["value"];
    }

    public function set($key,$value){
        if(!isset($this->setting[$key]))
            return;
        $this->setting[$key]["value"] = $value;
        file_put_contents($this->path,$this->setting);
    }

}