<?php
class ViewModel {    
    public function set($name,$val) {
        $this->$name = $val;
    }
    public function get($name) {
        if (isset($this->{$name})) {
            return $this->{$name};
        } else {
            return null;
        }
    }
}

?>