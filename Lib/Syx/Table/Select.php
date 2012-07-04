<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Db/Select.php';

/**
 * Syx_Table_Select
 *
 * @category   Syx
 * @package    Db
 */
class Syx_Table_Select extends Syx_Db_Select
{
	/**
	 * Syx_Table Object
	 *
	 * @var Syx_Table
	 */
	protected $_table;

	/**
	 * The schema name (default null means current schema)
	 *
	 * @var array
	 */
	protected $_schema = null;

	/**
	 * The table name.
	 *
	 * @var array
	 */
	protected $_name = null;

	protected $_aliasName = null;

	/**
	 * Syx_Table_Paginator Object
	 *
	 * @var Syx_Table_Paginator
	 */
	protected $_pagination = null;

	public function __construct(Syx_Table $table)
	{
		parent::__construct($table->getAdapter());
		$info = $table->info();
		$this->_table = $table;
		$this->_schema = $info[Syx_Table::SCHEMA];
		$this->_name = $info[Syx_Table::NAME];
		$this->_aliasName = $info[Syx_Table::ALIAS_NAME];
	}

	/**
	 * get Table instance
	 *
	 * @return Syx_Table
	 */
	public function getTable()
	{
		return $this->_table;
	}

	/**
	 * page query
	 *
	 * @param Syx_Table_Paginator $pagination
	 *
	 * @return Syx_Table_Select
	 */
	public function page(Syx_Table_Paginator $pagination)
	{
		$this->_pagination = $pagination;
		return $this;
	}

	/**
	 * query data
	 *
	 * @return array
	 */
	public function fetch()
	{
		if ($this->_pagination != null) {
			$order = $this->getPart(self::ORDER);
			$columns = $this->getPart(self::COLUMNS);
			$this->reset(self::ORDER)
				->reset(self::COLUMNS)
				->reset(self::LIMIT_COUNT)
				->reset(self::LIMIT_OFFSET);

			$c_expr = new Syx_Db_Expr('COUNT(*)');
			$forms = $this->getPart(self::FROM);
			if (empty($forms)) {
				$this->from(array($this->_name, $this->_schema, $this->_aliasName), $c_expr);
				$stmt = $this->query();
				$this->reset(self::FROM);
			} else {
				$this->columns($c_expr);
				$stmt = $this->query();
			}
			$count = (int)$stmt->fetchColumn();
			$this->setPart(self::ORDER, $order);
			$this->setPart(self::COLUMNS, $columns);
			if ($count) {
				$this->_pagination->init($count);
				$size = $this->_pagination->getPageSize();
				$currentPage = $this->_pagination->getCurrentPage();
				$this->limit($size, ($currentPage - 1) * $size);
			}
			$this->_pagination = null;
		}
		$stmt = $this->query();
		$this->reset();
		return $stmt->fetchAll(Syx_Db::FETCH_ASSOC);
	}

	/**
	 * query data
	 *
	 * @param int|string|array|Syx_Db_Expr $where
	 * @param string|array                 $order
	 * @param null|int                     $count
	 * @param null|int                     $offset
	 *
	 * @return array
	 */
	public function fetchAll($where = null, $order = null, $count = null, $offset = null)
	{
		if ($where !== null) {
			$this->where($where);
		}
		if ($order !== null) {
			$this->order($order);
		}
		if ($count !== null || $offset !== null) {
			$this->limit($count, $offset);
		}

		return $this->fetch();
	}

	/**
	 * query data
	 *
	 * @param int|string|array|Syx_Db_Expr $where
	 * @param string|array                 $order
	 * @param null|int                     $offset
	 *
	 * @return array
	 */
	public function fetchOne($where = null, $order = null, $offset = null)
	{
		$data = $this->fetchAll($where, $order, 1, $offset ? $offset : $this->getPart(self::LIMIT_OFFSET));
		if (empty($data)) {
			return false;
		}
		reset($data);
		return current($data);
	}

	/**
	 * @return string
	 */
	public function assemble()
	{
		if (!count($this->getPart(self::COLUMNS))) {
			$this->from(array($this->_name, $this->_schema, $this->_aliasName));
		}
		return parent::assemble();
	}

	protected function _parseTable($table)
	{
		if ($table instanceof Syx_Table) {
			$info = $table->info();
			return array(
				$info[Syx_Table::NAME],
				$info[Syx_Table::SCHEMA],
				$info[Syx_Table::ALIAS_NAME]
			);
		}
		return parent::_parseTable($table);
	}
}