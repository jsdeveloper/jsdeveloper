<?php

/**
	SQL database plugin for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2011 F3::Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package DB
		@version 2.0.9
**/

//! SQL data access layer
class DB extends Base {

	//@{ Locale-specific error/exception messages
	const
		TEXT_ExecFail='Unable to execute prepared statement: %s',
		TEXT_DBEngine='Database engine is not supported',
		TEXT_Schema='Schema for %s table is not available';
	//@}

	public
		//! Exposed data object properties
		$dbname,$backend,$pdo,$result;
	private
		//! Connection parameters
		$dsn,$user,$pw,$opt,
		//! Transaction tracker
		$trans=FALSE,
		//! Auto-commit mode
		$auto=TRUE,
		//! Number of rows affected by query
		$rows=0;

	/**
		Force PDO instantiation
			@public
	**/
	function instantiate() {
		$this->pdo=new PDO($this->dsn,$this->user,$this->pw,$this->opt);
	}

	/**
		Begin SQL transaction
			@param $auto boolean
			@public
	**/
	function begin($auto=FALSE) {
		if (!$this->pdo)
			self::instantiate();
		$this->pdo->beginTransaction();
		$this->trans=TRUE;
		$this->auto=$auto;
	}

	/**
		Rollback SQL transaction
			@public
	**/
	function rollback() {
		if (!$this->pdo)
			self::instantiate();
		$this->pdo->rollback();
		$this->trans=FALSE;
		$this->auto=TRUE;
	}

	/**
		Commit SQL transaction
			@public
	**/
	function commit() {
		if (!$this->pdo)
			self::instantiate();
		$this->pdo->commit();
		$this->trans=FALSE;
		$this->auto=TRUE;
	}

	/**
		Process SQL statement(s)
			@return array
			@param $cmds mixed
			@param $args array
			@param $ttl int
			@public
	**/
	function exec($cmds,array $args=NULL,$ttl=0) {
		if (!$this->pdo)
			self::instantiate();
		$stats=&self::ref('STATS');
		if (!isset($stats[$this->dsn]))
			$stats[$this->dsn]=array(
				'cache'=>array(),
				'queries'=>array()
			);
		$batch=is_array($cmds);
		if ($batch) {
			if (!$this->trans && $this->auto)
				$this->begin(TRUE);
			if (is_null($args)) {
				$args=array();
				for ($i=0;$i<count($cmds);$i++)
					$args[]=NULL;
			}
		}
		else {
			$cmds=array($cmds);
			$args=array($args);
		}
		foreach (array_combine($cmds,$args) as $cmd=>$arg) {
			$hash='sql.'.self::hash($cmd.var_export($args,TRUE));
			$cached=Cache::cached($hash);
			if ($ttl && $cached && $_SERVER['REQUEST_TIME']-$cached<$ttl) {
				// Gather cached queries for profiler
				if (!isset($stats[$this->dsn]['cache'][$cmd]))
					$stats[$this->dsn]['cache'][$cmd]=0;
				$stats[$this->dsn]['cache'][$cmd]++;
				$this->result=Cache::get($hash);
			}
			else {
				if (is_null($arg))
					$query=$this->pdo->query($cmd);
				else {
					$query=$this->pdo->prepare($cmd);
					if (is_object($query)) {
						foreach ($arg as $key=>$value)
							if (!(is_array($value)?
								$query->bindvalue($key,$value[0],$value[1]):
								$query->bindvalue($key,$value,
									$this->type($value))))
								break;
						$query->execute();
					}
				}
				// Check SQLSTATE
				foreach (array($this->pdo,$query) as $obj)
					if ($obj->errorCode()!=PDO::ERR_NONE) {
						if ($this->trans && $this->auto)
							$this->rollback();
						$error=$obj->errorinfo();
						trigger_error($error[2]);
						return FALSE;
					}
				if (preg_match(
					'/^\s*(?:SELECT|PRAGMA|SHOW|EXPLAIN)\s/i',$cmd)) {
					$this->result=$query->fetchall(PDO::FETCH_ASSOC);
					$this->rows=$query->rowcount();
				}
				else
					$this->rows=$this->result=$query->rowCount();
				if ($ttl)
					Cache::set($hash,$this->result,$ttl);
				// Gather real queries for profiler
				if (!isset($stats[$this->dsn]['queries'][$cmd]))
					$stats[$this->dsn]['queries'][$cmd]=0;
				$stats[$this->dsn]['queries'][$cmd]++;
			}
		}
		if ($batch || $this->trans && $this->auto)
			$this->commit();
		return $this->result;
	}

	/**
		Return number of rows affected by latest query
			@return int
	**/
	function rows() {
		return $this->rows;
	}

	/**
		Return auto-detected PDO data type of specified value
			@return int
			@param $val mixed
			@public
	**/
	function type($val) {
		foreach (
			array(
				'null'=>'NULL',
				'bool'=>'BOOL',
				'string'=>'STR',
				'int'=>'INT',
				'float'=>'STR'
			) as $php=>$pdo)
			if (call_user_func('is_'.$php,$val))
				return constant('PDO::PARAM_'.$pdo);
		return PDO::PARAM_LOB;
	}

	/**
		Convenience method for direct SQL queries (static call)
			@return array
			@param $cmds mixed
			@param $args mixed
			@param $ttl int
			@param $db string
			@public
	**/
	static function sql($cmds,array $args=NULL,$ttl=0,$db='DB') {
		return self::$vars[$db]->exec($cmds,$args,$ttl);
	}

	/**
		Return schema of specified table
			@return array
			@param $table string
			@param $ttl int
			@public
	**/
	function schema($table,$ttl) {
		// Support these engines
		$cmd=array(
			'sqlite2?'=>array(
				'PRAGMA table_info('.$table.');',
				'name','pk',1,'type'),
			'mysql'=>array(
				'SHOW columns FROM `'.$this->dbname.'`.'.$table.';',
				'Field','Key','PRI','Type'),
			'mssql|sybase|dblib|pgsql|ibm|odbc'=>array(
				'SELECT c.column_name AS field,'.
				'c.data_type AS type,t.constraint_type AS pkey '.
				'FROM information_schema.columns AS c '.
				'LEFT OUTER JOIN '.
					'information_schema.key_column_usage AS k ON '.
						'c.table_name=k.table_name AND '.
						'c.column_name=k.column_name '.
						($this->dbname?
							('AND '.
							(preg_match('/^pgsql$/',$this->backend)?
								'c.table_catalog=k.table_catalog':
								'c.table_schema=k.table_schema').' '):'').
				'LEFT OUTER JOIN '.
					'information_schema.table_constraints AS t ON '.
						'k.table_name=t.table_name AND '.
						'k.constraint_name=t.constraint_name '.
						($this->dbname?
							('AND '.
							(preg_match('/pgsql/',$this->backend)?
								'k.table_catalog=t.table_catalog':
								'k.table_schema=t.table_schema').' '):'').
				'WHERE '.
					'c.table_name=\''.$table.'\''.
					($this->dbname?
						('AND '.
						(preg_match('/pgsql/',$this->backend)?
							'c.table_catalog':'c.table_schema').
							'=\''.$this->dbname.'\''):'').
				';',
				'field','pkey','PRIMARY KEY','type')
		);
		$match=FALSE;
		foreach ($cmd as $backend=>$val)
			if (preg_match('/'.$backend.'/',$this->backend)) {
				$match=TRUE;
				break;
			}
		if (!$match) {
			trigger_error(self::TEXT_DBEngine);
			return FALSE;
		}
		$result=$this->exec($val[0],NULL,$ttl);
		if (!$result) {
			trigger_error(sprintf(self::TEXT_Schema,$table));
			return FALSE;
		}
		return array(
			'result'=>$result,
			'field'=>$val[1],
			'pkname'=>$val[2],
			'pkval'=>$val[3],
			'type'=>$val[4]
		);
	}

	/**
		Custom session handler
			@param $table string
			@public
	**/
	function session($table='sessions') {
		$self=$this;
		session_set_save_handler(
			// Open
			function($path,$name) use($self,$table) {
				// Support these engines
				$cmd=array(
					'sqlite2?'=>
						'SELECT name FROM sqlite_master '.
						'WHERE type=\'table\' AND name=\''.$table.'\';',
					'mysql|mssql|sybase|dblib|pgsql'=>
						'SELECT table_name FROM information_schema.tables '.
						'WHERE '.
							(preg_match('/pgsql/',$self->backend)?
								'table_catalog':'table_schema').
								'=\''.$self->dbname.'\' AND '.
							'table_name=\''.$table.'\''
				);
				foreach ($cmd as $backend=>$val)
					if (preg_match('/'.$backend.'/',$self->backend))
						break;
				$result=$self->exec($val,NULL);
				if (!$result)
					// Create SQL table
					$self->exec(
						'CREATE TABLE '.
							(preg_match('/sqlite2?/',$self->backend)?
								'':($self->dbname.'.')).$table.' ('.
							'id VARCHAR(40),'.
							'data LONGTEXT,'.
							'stamp INTEGER'.
						');'
					);
				register_shutdown_function('session_commit');
				return TRUE;
			},
			// Close
			function() {
				return TRUE;
			},
			// Read
			function($id) use($table) {
				$axon=new Axon($table);
				$axon->load(array('id=:id',array(':id'=>$id)));
				return $axon->dry()?FALSE:$axon->data;
			},
			// Write
			function($id,$data) use($table) {
				$axon=new Axon($table);
				$axon->load(array('id=:id',array(':id'=>$id)));
				$axon->id=$id;
				$axon->data=$data;
				$axon->stamp=time();
				$axon->save();
				return TRUE;
			},
			// Delete
			function($id) use($table) {
				$axon=new Axon($table);
				$axon->erase(array('id=:id',array(':id'=>$id)));
				return TRUE;
			},
			// Cleanup
			function($max) use($table) {
				$axon=new Axon($table);
				$axon->erase('stamp+'.$max.'<'.time());
				return TRUE;
			}
		);
	}

	/**
		Class destructor
			@public
	**/
	function __destruct() {
		unset($this->pdo);
	}

	/**
		Class constructor
			@param $dsn string
			@param $user string
			@param $pw string
			@param $opt array
			@param $force boolean
			@public
	**/
	function __construct($dsn,$user=NULL,$pw=NULL,$opt=NULL,$force=FALSE) {
		if (!isset(self::$vars['MYSQL']))
			// Default MySQL character set
			self::$vars['MYSQL']='utf8';
		if (!$opt)
			// Append other default options
			$opt=array(PDO::ATTR_EMULATE_PREPARES=>FALSE)+(
				extension_loaded('pdo_mysql') &&
				preg_match('/^mysql:/',$dsn)?
					array(PDO::MYSQL_ATTR_INIT_COMMAND=>
						'SET NAMES '.self::$vars['MYSQL']):array()
			);
		list($this->dsn,$this->user,$this->pw,$this->opt)=
			array($this->resolve($dsn),$user,$pw,$opt);
		$this->backend=strstr($this->dsn,':',TRUE);
		preg_match('/dbname=([^;$]+)/',$this->dsn,$match);
		if ($match)
			$this->dbname=$match[1];
		if (!isset(self::$vars['DB']))
			self::$vars['DB']=$this;
		if ($force)
			$this->pdo=new PDO($this->dsn,$this->user,$this->pw,$this->opt);
	}

}

//! Axon ORM
