<?php
/**
 * @author Bart Visscher <bartv@thisnet.nl>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Robin Appelman <icewind@owncloud.com>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\DB;

class OracleConnection extends Connection {
	/**
	 * Quote the keys of the array
	 */
	private function quoteKeys(array $data) {
		$return = array();
		foreach($data as $key => $value) {
			$return[$this->quoteIdentifier($key)] = $value;
		}
		return $return;
	}

	/**
	 * {@inheritDoc}
	 */
	public function insert($tableName, array $data, array $types = array()) {
		$tableName = $this->quoteIdentifier($tableName);
		$data = $this->quoteKeys($data);
		return parent::insert($tableName, $data, $types);
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($tableName, array $data, array $identifier, array $types = array()) {
		$tableName = $this->quoteIdentifier($tableName);
		$data = $this->quoteKeys($data);
		$identifier = $this->quoteKeys($identifier);
		return parent::update($tableName, $data, $identifier, $types);
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($tableExpression, array $identifier, array $types = array()) {
		$tableName = $this->quoteIdentifier($tableExpression);
		$identifier = $this->quoteKeys($identifier);
		return parent::delete($tableName, $identifier);
	}

	/**
	 * Drop a table from the database if it exists
	 *
	 * @param string $table table name without the prefix
	 */
	public function dropTable($table) {
		$table = $this->tablePrefix . trim($table);
		$table = $this->quoteIdentifier($table);
		$schema = $this->getSchemaManager();
		if($schema->tablesExist(array($table))) {
			$schema->dropTable($table);
		}
	}

	/**
	 * Check if a table exists
	 *
	 * @param string $table table name without the prefix
	 * @return bool
	 */
	public function tableExists($table){
		$table = $this->tablePrefix . trim($table);
		$table = $this->quoteIdentifier($table);
		$schema = $this->getSchemaManager();
		return $schema->tablesExist(array($table));
	}
}
