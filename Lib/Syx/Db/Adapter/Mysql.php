<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Db/Adapter.php';

/**
 * Syx_Db_Adapter_Mysql
 *
 * @category   Syx
 * @package    Syx_Db
 * @subpackage Adapter
 */
class Syx_Db_Adapter_Mysql extends Syx_Db_Adapter
{

	/**
	 * Keys are UPPERCASE SQL datatypes or the constants
	 * Syx_Db::INT_TYPE, Syx_Db::BIGINT_TYPE, or Syx_Db::FLOAT_TYPE.
	 *
	 * Values are:
	 * 0 = 32-bit integer
	 * 1 = 64-bit integer
	 * 2 = float or decimal
	 *
	 * @var array Associative array of datatypes to values 0, 1, or 2.
	 */
	protected $_numericDataTypes = array(
		Syx_Db::INT_TYPE    => Syx_Db::INT_TYPE,
		Syx_Db::BIGINT_TYPE => Syx_Db::BIGINT_TYPE,
		Syx_Db::FLOAT_TYPE  => Syx_Db::FLOAT_TYPE,
		'INT'               => Syx_Db::INT_TYPE,
		'INTEGER'           => Syx_Db::INT_TYPE,
		'MEDIUMINT'         => Syx_Db::INT_TYPE,
		'SMALLINT'          => Syx_Db::INT_TYPE,
		'TINYINT'           => Syx_Db::INT_TYPE,
		'BIGINT'            => Syx_Db::BIGINT_TYPE,
		'SERIAL'            => Syx_Db::BIGINT_TYPE,
		'DEC'               => Syx_Db::FLOAT_TYPE,
		'DECIMAL'           => Syx_Db::FLOAT_TYPE,
		'DOUBLE'            => Syx_Db::FLOAT_TYPE,
		'DOUBLE PRECISION'  => Syx_Db::FLOAT_TYPE,
		'FIXED'             => Syx_Db::FLOAT_TYPE,
		'FLOAT'             => Syx_Db::FLOAT_TYPE
	);

	protected $_driver = 'mysql';

	protected $_savePointSupport = false;

	protected $_savedPoints = array();

	protected $_transCount = 0;

	/**
	 * Returns the symbol the adapter uses for delimiting identifiers.
	 *
	 * @return string
	 */
	public function getQuoteIdentifierSymbol()
	{
		return "`";
	}

	/**
	 * Returns the column descriptions for a table.
	 *
	 * The return value is an associative array keyed by the column name,
	 * as returned by the RDBMS.
	 *
	 * The value of each array element is an associative array
	 * with the following keys:
	 *
	 * COLUMN_NAME    => string; column name
	 * COLUMN_POS     => number; ordinal position of column in table
	 * DATA_TYPE      => string; SQL datatype name of column
	 * DEFAULT        => string; default expression of column, null if none
	 * NULLABLE       => boolean; true if column can have nulls
	 * LENGTH         => number; length of CHAR/VARCHAR
	 * UNSIGNED       => boolean; unsigned property of an integer type
	 * PRIMARY        => boolean; true if column is part of the primary key
	 * PRIMARY_POS    => integer; position of column in primary key
	 * IDENTITY       => integer; true if column isautoIncrement
	 *
	 * @param string $table
	 * @param string $schema
	 * @param bool   $hasQuoted
	 *
	 * @return array
	 */
	public function describeTable($table, $schema = null, $hasQuoted = false)
	{
		if (!$hasQuoted) {
			$table = $this->quoteTableAs($schema ? "$schema.$table" : $table, null, true);
		}
		$stmt = $this->query("DESCRIBE $table");

		$rowset = $stmt->fetchAll(Syx_Db::FETCH_NUM);

		$field = 0;
		$type = 1;
		$null = 2;
		$key = 3;
		$default = 4;
		$extra = 5;

		$desc = array();
		$i = 1;
		$p = 1;
		foreach ($rowset as $row) {
			list($length, $unsigned, $primary, $primaryPos, $identity)
				= array(null, null, false, null, false);
			if (preg_match('/unsigned/', $row[$type])) {
				$unsigned = true;
			}
			if (preg_match('/^((?:var)?char)\((\d+)\)/', $row[$type], $matches)) {
				$row[$type] = $matches[1];
				$length = $matches[2];
			} else {
				if (preg_match('/^(decimal|float)/', $row[$type], $matches)) {
					$row[$type] = $matches[1];
				} else {
					if (preg_match('/^((?:big|medium|small|tiny)?int)\(\d+\)/',
						$row[$type], $matches)
					) {
						$row[$type] = $matches[1];
					}
				}
			}
			if (strtoupper($row[$key]) == 'PRI') {
				$primary = true;
				$primaryPos = $p;
				if ($row[$extra] == 'auto_increment') {
					$identity = true;
				} else {
					$identity = false;
				}
				++$p;
			}
			$columnName = $row[$field];
			$desc[$columnName] = array(
				'COLUMN_NAME' => $columnName,
				'COLUMN_POS'  => $i,
				'DATA_TYPE'   => $row[$type],
				'DEFAULT'     => $row[$default],
				'NULLABLE'    => (bool)($row[$null] == 'YES'),
				'LENGTH'      => $length,
				'UNSIGNED'    => $unsigned,
				'PRIMARY'     => $primary,
				'PRIMARY_POS' => $primaryPos,
				'IDENTITY'    => $identity
			);
			++$i;
		}
		switch ($this->_caseFolding) {
		case Syx_Db::CASE_LOWER:
			$desc = array_change_key_case($desc, CASE_LOWER);
			break;
		case Syx_Db::CASE_UPPER:
			$desc = array_change_key_case($desc, CASE_UPPER);
			break;
		}
		return $desc;
	}

	protected function _dsn()
	{
		// baseline of DSN parts
		$dsn = $this->_config;

		// don't pass the username, password, charset, persistent and driver_options in the DSN
		unset($dsn['username']);
		unset($dsn['password']);
		unset($dsn['prefix']);
		unset($dsn['persistent']);
		unset($dsn['driver_options']);

		// use all remaining parts in the DSN
		foreach ($dsn as $key => $val) {
			$dsn[$key] = "$key=$val";
		}

		$init = "SET SQL_MODE=''";
		if (!empty($this->_config['charset'])) {
			$init .= ", NAMES '" . $this->_config['charset'] . "';";
		}
		$this->_config['driver_options'][1002] = $init;

		return 'mysql:' . implode(';', $dsn);
	}

	/**
	 * Creates a connection to the database.
	 *
	 * @return void
	 * @throws Syx_Db_Adapter_Exception
	 */
	protected function _connect()
	{
		if ($this->_connection) {
			return;
		}

		parent::_connect();

		$version = $this->_connection->getAttribute(PDO::ATTR_SERVER_INFO);
		if (version_compare($version, '5.0', '>=')) {
			$this->_savePointSupport = true;
		}
	}

	/**
	 * Adds an adapter-specific LIMIT clause to the SELECT statement.
	 *
	 * @param string $sql
	 * @param int    $count
	 * @param int    $offset OPTIONAL
	 *
	 * @throws Syx_Db_Adapter_Exception
	 * @return string
	 */
	public function limit($sql, $count, $offset = 0)
	{
		$count = intval($count);
		if ($count <= 0) {
			require_once 'Syx/Db/Adapter/Exception.php';
			throw new Syx_Db_Adapter_Exception("LIMIT argument count=$count is not valid");
		}

		$offset = intval($offset);
		if ($offset < 0) {
			require_once 'Syx/Db/Adapter/Exception.php';
			throw new Syx_Db_Adapter_Exception("LIMIT argument offset=$offset is not valid");
		}

		$sql .= " LIMIT $count";
		if ($offset > 0) {
			$sql .= " OFFSET $offset";
		}

		return $sql;
	}

	/**
	 * Leave autocommit mode and begin a transaction.
	 *
	 * @return bool
	 */
	public function beginTransaction()
	{
		$conn = $this->getConnection();
		if ($this->_transCount < 1) {
			$conn->exec('START TRANSACTION');
		} elseif ($this->_savePointSupport) {
			$point = 'POINT_' . $this->_transCount;
			$conn->exec("SAVEPOINT `{$point}`");
			array_push($this->_savedPoints, $point);
		}
		++$this->_transCount;
		return true;
	}

	/**
	 * Roll back a transaction and return to autocommit mode.
	 *
	 * @return bool
	 */
	public function rollBack()
	{
		$conn = $this->getConnection();
		if ($this->_transCount < 1) {
			$this->_transCount = 0;
			return true;
		}
		if (--$this->_transCount == 0) {
			$conn->exec('ROLLBACK');
		} elseif ($this->_savePointSupport) {
			$point = array_pop($this->_savedPoints);
			$conn->exec("ROLLBACK TO SAVEPOINT `{$point}`");
		}
		return true;
	}

	/**
	 * Commit a transaction and return to autocommit mode.
	 *
	 * @return bool
	 */
	public function commit()
	{
		$conn = $this->getConnection();
		if ($this->_transCount < 1) {
			$this->_transCount = 0;
			return true;
		}
		if (--$this->_transCount == 0) {
			$conn->exec('COMMIT');
		} elseif ($this->_savePointSupport) {
			array_pop($this->_savedPoints);
		}
		return true;
	}
}