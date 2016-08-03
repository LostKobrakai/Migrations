<?php
namespace tests\dummies;

use Migration;

class Dummy extends Migration{

	public function update ()
	{
		echo 'upgrade';
	}

	public function downgrade ()
	{
		echo 'downgrade';
	}

	public function publicGetField ()
	{
		return call_user_func_array([$this, 'getField'], func_get_args());
	}

	public function publicGetTemplate ()
	{
		return call_user_func_array([$this, 'getTemplate'], func_get_args());
	}

	public function publicEachPageUncache ()
	{
		return call_user_func_array([$this, 'eachPageUncache'], func_get_args());
	}

	public function publicEditInTemplateContext ()
	{
		return call_user_func_array([$this, 'editInTemplateContext'], func_get_args());
	}
}