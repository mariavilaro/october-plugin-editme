<?php namespace Fw\EditMe\Controllers;

class Upload extends \Backend\Classes\Controller {

    public $requiredPermissions = ['rainlab.translate.manage_messages'];

	public function upload()
	{
		return 'ok';
	}

}