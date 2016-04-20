<?php

namespace App\Presenters;

use Nette;
use App\Model;


class PhpPresenter extends BasePresenter
{

	public function renderInfo()
	{
        phpinfo();
        $this->terminate();
	}

}
