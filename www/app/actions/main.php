<?php

/**
 * Description of main
 *
 * @author Nuno Ferreira 
 */

class controller_main extends controller{

	public $start_time;

	public function initialize()
	{
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->start_time = $mtime;

		// Using PDO
		//
		$dbh = new PDO('mysql:host=localhost;dbname=winecatalogs', 'root', 'wiz');
		/*
		$sth = $dbh->prepare("SELECT * FROM auth_users");
		$sth->execute();
		$rows = $sth->fetchAll();
		*/
		debug::dump($this->getTimeElapsed());
		
	}

	
	public function index()
	{
		$this->tpl->display('main.tpl');
		
	}

	public function getTimeElapsed()
	{
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = ($endtime - $this->start_time);

		//fb::log($totaltime.' segundos.');
		return $totaltime;
	}


	public function finalize()
	{
		
		
	}

}