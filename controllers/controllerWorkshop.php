<?php

	class controllerWorkshop extends componentController
	{
		private $model;

		public function __construct($route)
		{
			parent::__construct($route);
			$this->model = new modelWorkshop();
		}

		//workshop
		public function actionWorkshop()
		{
			$this->onlyForLogged();
			$this->view->render('Camagru: workshop');
			return true;
		}

		public function actionGetPreviewWebcam() {
			$this->onlyForLogged();
			$data = (array)json_decode($_POST['box']);
			$base64 = str_replace(' ', '+', array_pop($data));
			$base64 = str_replace('data:image/png;base64,', '', $base64);
			$preview = $this->model->getPreviewFromWebCam($base64, $data);
			echo $preview;
			return true;
		}

		public function actionUsersPicValidate() {
			$this->onlyForLogged();
			if (($result = $this->model->validateUsersPic()) !== true) {
				echo json_encode($result);
				return true;
			}
			echo json_encode($result);
			return true;
		}

}
