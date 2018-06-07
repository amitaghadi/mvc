<?php
class BaseModel {
    protected $viewModel;
    public function __construct()
    {
        $this->viewModel = new ViewModel();
		$this->load = new Load();
	$this->commonViewData();
    }
    protected function commonViewData() {
    }
}

?>
