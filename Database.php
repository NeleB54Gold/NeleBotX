<?php

class Database
{
	public $configs = [];
	public $redis = [];
	public $PDO = [];
	public $response = [
		'ok' => 0
	];
	public $tables = [
		'users',
		'groups',
		'channels'
	];
	
	public function __construct ($configs) {
		$this->configs = $configs;
		if ($this->configs['redis']['status']) {
			$this->response = $this->redisConnect();
			if (!$this->response['ok']) {
				return $this->response;
			}
		}
		if ($this->configs['database']['status']) {
			$this->response = $this->dbConnect();
			if (!$this->response['ok']) {
				return $this->response;
			}
		}
		return $this->response = ['ok' => 1];
	}
	
	# Redis Connection
	public function redisConnect () {
		if (class_exists('Redis')) {
			try {
				$this->redis = new Redis();
				$this->redis->connect($this->configs['redis']['host'], $this->configs['redis']['port']);
				if ($this->configs['redis']['password']) $this->redis->auth($this->configs['redis']['password']);
				if ($this->configs['redis']['database']) $this->redis->select($this->configs['redis']['database']);
				return ['ok' => 1];
			} catch (Exception $e) {
				return ['ok' => 0, 'error_code' => 500, 'description' => 'Redis Error: ' . $e->getMessage()];
			}
		} else {
			return ['ok' => 0, 'error_code' => 500, 'description' => 'Internal Server Error: Redis class not exists'];
		}
	}
	
	# Redis get
	public function rget ($key) {
		if ($this->redis) {
			try {
				return $this->redis->get($key);
			} catch (Exception $e) {
			}
		}
		return 0;
	}
	
	# Redis set
	public function rset ($key, $value, $time = 0) {
		if ($this->redis) {
			try {
				return $this->redis->set($key, $value, $time);
			} catch (Exception $e) {
			}
		}
		return 0;
	}
	
	# Redis del
	public function rdel ($key) {
		if ($this->redis) {
			try {
				return $this->redis->del($key);
			} catch (Exception $e) {
			}
		}
		return 0;
	}
	
	# Redis keys
	public function rkeys ($key) {
		if ($this->redis) {
			try {
				return $this->redis->keys($key);
			} catch (Exception $e) {
			}
		}
		return 0;
	}
	
	# Redis lpush
	public function rladd ($key, $value) {
		if ($this->redis) {
			try {
				return $this->redis->lPush($key, $value);
			} catch (Exception $e) {
			}
		}
		return 0;
	}
	
	# Redis lrange
	public function rlget ($key, $offset = 0, $limit = 50) {
		if ($this->redis) {
			try {
				return $this->redis->lRange($key, $offset, $limit);
			} catch (Exception $e) {
			}
		}
		return 0;
	}
	
	# Redis lrem
	public function rldel ($key, $value = 0, $count = 50) {
		if ($this->redis) {
			try {
				return $this->redis->lRem($key, $value, $count);
			} catch (Exception $e) {
			}
		}
		return 0;
	}
	
	# Database Connection
	public function dbConnect () {
		if (class_exists('PDO')) {
			$this->configs['database']['type'] = strtolower($this->configs['database']['type']);
			if ($this->configs['database']['type'] == 'sqlite') {
				return $this->sqliteConnect();
			} elseif ($this->configs['database']['type'] == 'mysql') {
				return $this->mysqlConnect();
			} elseif ($this->configs['database']['type'] == 'pgsql') {
				return $this->pgsqlConnect();
			} else {
				return ['ok' => 0, 'error_code' => 500, 'description' => 'Internal Server Error: unknown database type'];
			}
		} else {
			return ['ok' => 0, 'error_code' => 500, 'description' => 'Internal Server Error: PDO class not found'];
		}
	}
	
	# PDO Connection with SQLite
	private function sqliteConnect () {
		try {
			$this->PDO = new PDO('sqlite:' . $this->configs['database']['database_name']);
			return ['ok' => 1];
		} catch (PDOException $e) {
			return ['ok' => 0, 'error_code' => 500, 'description' => 'PDO Error: ' . $e->getMessage()];
		}
	}
	
	# PDO Connection with MySQL
	private function mysqlConnect () {
		try {
			$this->PDO = new PDO('mysql:host=' . $this->configs['database']['host'] . ';dbname=' . $this->configs['database']['database_name'] . ';charset=utf8mb4', $this->configs['database']['user'], $this->configs['database']['password']);
			return ['ok' => 1];
		} catch (PDOException $e) {
			return ['ok' => 0, 'error_code' => 500, 'description' => 'PDO Error: ' . $e->getMessage()];
		}
	}
	
	# PDO Connection with PostgreSQL
	private function pgsqlConnect () {
		try {
			$this->PDO = new PDO('pgsql:host=' . $this->configs['database']['host'] . ';dbname=' . strtolower($this->configs['database']['database_name']), strtolower($this->configs['database']['user']), $this->configs['database']['password']);
			return ['ok' => 1];
		} catch (PDOException $e) {
			return ['ok' => 0, 'error_code' => 500, 'description' => 'PDO Error: ' . $e->getMessage()];
		}
	}

	# General PDO custom query
	public function query ($query, $args = 0, $return = 0) {
		if (!is_string($query)) return ['error' => 'First parameter must be a string'];
		if (!$this->PDO) return ['error' => 'PDO class not found'];
		try {
			$q = $this->PDO->prepare($query);
			if ($err = $this->PDO->errorInfo() and $err[0] !== '00000') return ['error' => $err];
			if (is_array($args) and !empty($args)) {
				$q->execute($args);
			} else {
				$q->execute();
			}
			if ($err = $q->errorInfo() and $err[1] !== null) return ['error' => $err];
			if (!$return) {
				return 1;
			} elseif ($return === 1) {
				return $q->fetch(\PDO::FETCH_ASSOC);
			} else {
				return $q->fetchAll();
			}
		} catch (PDOException $e) {
			return ['error' => 'PDO Exception: ' . $e];
		}
	}

	# Create LIMIT with OFFSET
	public function limit ($limit = 'ALL', $offset = 0) {
		if ($this->configs['database']['type'] == 'sqlite') {
			return 'LIMIT ' . $limit . ' OFFSET ' . round($offset);
		} elseif ($this->configs['database']['type'] == 'mysql') {
			return 'LIMIT ' . round($offset) . ', ' . $limit;
		} elseif ($this->configs['database']['type'] == 'pgsql') {
			return 'LIMIT ' . $limit . ' OFFSET ' . round($offset);
		}
		return;
	}

	# Tables Setup for NeleBot X
	public function setup() {
		$tables = [];
		foreach ($this->tables as $table) {
			$tables[$table] = $this->createTemplateTable($table);
		}
		return $tables;
	}
	
	# Templates of users, groups and channels table
	public function createTemplateTable ($table) {
		if ($table == 'users') {
			return $this->query('CREATE TABLE IF NOT EXISTS users (
				id				BIGINT			PRIMARY KEY,
				name			VARCHAR(64)		NOT NULL,
				surname			VARCHAR(64),
				username		VARCHAR(32),
				lang			VARCHAR(16)		NOT NULL	DEFAULT \'en\',
				settings		TEXT			NOT NULL	DEFAULT \'{}\',
				registration	INT				NOT NULL,
				last_seen		INT				NOT NULL,
				ban				INT				NOT NULL	DEFAULT \'0\',
				status			VARCHAR(64)		NOT NULL	DEFAULT \'Unknown\'
			);');
		} elseif ($table == 'groups') {
			return $this->query('CREATE TABLE IF NOT EXISTS groups (
				id				BIGINT			PRIMARY KEY,
				title			VARCHAR(64)		NOT NULL,
				description		VARCHAR(256),
				username		VARCHAR(32),
				admins			TEXT			NOT NULL	DEFAULT \'{}\',
				permissions		TEXT			NOT NULL	DEFAULT \'{}\',
				settings		TEXT			NOT NULL	DEFAULT \'{}\',
				registration	INT				NOT NULL,
				last_seen		INT				NOT NULL,
				ban				INT				NOT NULL	DEFAULT \'0\',
				status			VARCHAR(64)		NOT NULL	DEFAULT \'Unknown\'
			);');
		} elseif ($table == 'channels') {
			return $this->query('CREATE TABLE IF NOT EXISTS channels (
				id				BIGINT			PRIMARY KEY,
				title			VARCHAR(64)		NOT NULL,
				description		VARCHAR(256),
				username		VARCHAR(32),
				admins			TEXT			NOT NULL	DEFAULT \'{}\',
				settings		TEXT			NOT NULL	DEFAULT \'{}\',
				registration	INT				NOT NULL,
				last_seen		INT				NOT NULL,
				ban				INT				NOT NULL	DEFAULT \'0\',
				status			VARCHAR(64)		NOT NULL	DEFAULT \'Unknown\'
			);');
		}
		return ['error' => 'Table template not found'];
	}

	# User on Database
	public function getUser ($user) {
		if (!is_array($user)) return ['error' => 'Bad Request: User must be an array'];
		if (isset($user['id']) or isset($user['username'])) {
			if (isset($user['id'])) {
				if ($user['id'] <= 0 or $user['id'] >= 18446744073709551615) return ['error' => 'Bad Request: id is out of range'];
				$q = $this->query('SELECT * FROM users WHERE id = ?', [round($user['id'])], 1);
			} else {
				if (strlen($user['username']) > 32) return ['error' => 'Bad Request: username has too many characters'];
				$q = $this->query('SELECT * FROM users WHERE LOWER(username) = LOWER(?)', [$user['username']], 1);
			}
			if (!isset($q['id']) and $user['first_name']) {
				if (!isset($user['language_code'])) $user['language_code'] = 'en';
				$args = [
					$user['id'],
					$user['first_name'],
					$user['last_name'],
					$user['username'],
					$user['language_code'],
					time(),
					time()
				];
				$i = $this->query('INSERT INTO users (id, name, surname, username, lang, registration, last_seen) VALUES (?,?,?,?,?,?,?)', $args);
				$q = $this->query('SELECT * FROM users WHERE id = ?', [$user['id']], 1);
				if (!isset($q['id'])) {
					return ['error' => 'Error to load user info', 'INSERT' => $i];
				}
			} elseif (isset($user['first_name']) and (($user['first_name'] !== $q['name'] or $user['last_name'] !== $q['surname'] or $user['username'] !== $q['username']) or $q['last_seen'] <= (time() - 60 * 60))) {
				$q['name'] = $user['first_name'];
				$q['surname'] = $user['last_name'];
				$q['username'] = $user['username'];
				$q['last_seen'] = time();
				$this->query('UPDATE users SET name = ?, surname = ?, username = ?, last_seen = ? WHERE id = ?', [$user['first_name'], $user['last_name'], $user['username'], $q['last_seen'], $user['id']]);
			}
			// Use this json_decode only if you need it!
			/*$q['settings'] = json_decode($q['settings'], 1);
			if (!is_array($q['settings'])) $q['settings'] = [];*/
			return $q;
		} else {
			return ['error' => 'There is no id or username'];
		}
	}

	# Group on Database
	public function getGroup ($chat) {
		if (!is_array($chat)) return ['error' => 'Bad Request: Chat must be an array'];
		if (isset($chat['id']) or isset($chat['username'])) {
			if (isset($chat['id'])) {
				if ($chat['id'] >= 0) return ['error' => 'Bad Request: id is out of range'];
				$q = $this->query('SELECT * FROM groups WHERE id = ?', [round($chat['id'])], 1);
			} else {
				if (strlen($chat['username']) > 32) return ['error' => 'Bad Request: username has too many characters'];
				$q = $this->query('SELECT * FROM groups WHERE LOWER(username) = LOWER(?)', [$chat['username']], 1);
			}
			if (!isset($q['id']) and $chat['title']) {
				$args = [
					$chat['id'],
					$chat['title'],
					$chat['username'],
					time(),
					time()
				];
				$i = $this->query('INSERT INTO groups (id, title, username, registration, last_seen) VALUES (?,?,?,?,?)', $args);
				$q = $this->query('SELECT * FROM groups WHERE id = ?', [$chat['id']], 1);
				if (!isset($q['id'])) {
					return ['error' => 'Error to load chat info', 'INSERT' => $i];
				}
			} elseif (isset($chat['title']) and ($user['title'] !== $q['title'] or $q['last_seen'] <= (time() - 60 * 60))) {
				$q['title'] = $chat['title'];
				$q['last_seen'] = time();
				$this->query('UPDATE groups SET title = ?, last_seen = ? WHERE id = ?', [$chat['title'], $q['last_seen'], $chat['id']]);
			}
			// Use this json_decode only if you need it! else comment these 2 strings
			$q['settings'] = json_decode($q['settings'], 1);
			if (!is_array($q['settings'])) $q['settings'] = [];
			$q['permissions'] = json_decode($q['permissions'], 1);
			if (!is_array($q['permissions'])) $q['permissions'] = [];
			$q['admins'] = json_decode($q['admins'], 1);
			if (!is_array($q['admins'])) $q['admins'] = [];
			return $q;
		} else {
			return ['error' => 'There is no id or username'];
		}
	}

	# Channel on Database
	public function getChannel ($chat) {
		if (!is_array($chat)) return ['error' => 'Bad Request: Chat must be an array'];
		if (isset($chat['id']) or isset($chat['username'])) {
			if (isset($chat['id'])) {
				if ($chat['id'] >= 0) return ['error' => 'Bad Request: id is out of range'];
				$q = $this->query('SELECT * FROM channels WHERE id = ?', [round($chat['id'])], 1);
			} else {
				if (strlen($chat['username']) > 32) return ['error' => 'Bad Request: username has too many characters'];
				$q = $this->query('SELECT * FROM channels WHERE LOWER(username) = LOWER(?)', [$chat['username']], 1);
			}
			if (!isset($q['id']) and $chat['title']) {
				$args = [
					$chat['id'],
					$chat['title'],
					$chat['username'],
					time(),
					time()
				];
				$i = $this->query('INSERT INTO channels (id, title, username, registration, last_seen) VALUES (?,?,?,?,?)', $args);
				$q = $this->query('SELECT * FROM channels WHERE id = ?', [$chat['id']], 1);
				if (!isset($q['id'])) {
					return ['error' => 'Error to load chat info', 'INSERT' => $i];
				}
			} elseif (isset($chat['title']) and ($user['title'] !== $q['title'] or $q['last_seen'] <= (time() - 60 * 60))) {
				$q['title'] = $chat['title'];
				$q['last_seen'] = time();
				$this->query('UPDATE channels SET title = ?, last_seen = ? WHERE id = ?', [$chat['title'], $q['last_seen'], $chat['id']]);
			}
			// Use this json_decode only if you need it! else comment these 2 strings
			$q['settings'] = json_decode($q['settings'], 1);
			if (!is_array($q['settings'])) $q['settings'] = [];
			$q['admins'] = json_decode($q['admins'], 1);
			if (!is_array($q['admins'])) $q['admins'] = [];
			return $q;
		} else {
			return ['error' => 'There is no id or username'];
		}
	}

	# Get Chats by Admin (PostgreSQL only)
	public function getChatsByAdmin ($user_id, $limit = 20) {
		if (!is_numeric($user_id)) return [];
		$chats = [];
		$groups = $this->query('SELECT * FROM groups WHERE admins @> \'[{"user":{"id": ' . $user_id . '}}]\' ' . $this->limit(round($limit / 2)), [], 2);
		if (isset($groups[0]['id'])) {
			$chats = array_merge($chats, $groups);
		}
		$channels = $this->query('SELECT * FROM channels WHERE admins @> \'[{"user":{"id": ' . $user_id . '}}]\' ' . $this->limit(round($limit / 2)), [], 2);
		if (isset($channels[0]['id'])) {
			$chats = array_merge($chats, $channels);
		}
		return $chats;
	}

	# Ban by id
	public function ban ($id) {
		return [
			$this->query('UPDATE users SET ban = ? WHERE id = ?', [1, $id]),
			$this->query('UPDATE groups SET ban = ? WHERE id = ?', [1, $id]),
			$this->query('UPDATE channels SET ban = ? WHERE id = ?', [1, $id])
		];
	}

	# UnBan by id
	public function unban ($id) {
		return [
			$this->query('UPDATE users SET ban = ? WHERE id = ?', [0, $id], 1),
			$this->query('UPDATE groups SET ban = ? WHERE id = ?', [0, $id], 1),
			$this->query('UPDATE channels SET ban = ? WHERE id = ?', [0, $id], 1)
		];
	}

	# Get User ban by user_id
	public function isBanned ($id) {
		$q = $this->query('SELECT ban FROM users WHERE id = ?', [$id], 1);
		if ($q['ban']) return 1;
		$q = $this->query('SELECT ban FROM groups WHERE id = ?', [$id], 1);
		if ($q['ban']) return 1;
		$q = $this->query('SELECT ban FROM channels WHERE id = ?', [$id], 1);
		if ($q['ban']) return 1;
		return 0;
	}

	# Get User language by user_id
	public function getLanguage ($id) {
		if (is_numeric($id) and isset($id) and $id > 0 and $id < 18446744073709551615) {
			$q = $this->query('SELECT lang FROM users WHERE id = ? LIMIT 1', [round($id)], 1);
			if (isset($q['lang'])) {
				return $q['lang'];
			}
		}
		return 'en';
	}

	# Set user Telegram status
	public function setStatus ($id, $status = 'Unknown', $table = 0) {
		return $this->query('UPDATE ' . $this->tables[$table] . ' SET status = ? WHERE id = ?', [$status, $id]);
	}
}

?>
