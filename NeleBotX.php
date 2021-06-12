<?php

class NeleBotX
{
	public $configs = [];
	public $api = [];
	public $v = [];
	public $db = [];
	public $user = [];
	public $group = [];
	public $channel = [];
	public $response = [
		'ok'	=> 0
	];
	
	# Initialization
	public function __construct ($configs) {
		$this->configs = $configs;
		if (empty($this->configs)) {
			return $this->response = ['ok' => 0, 'error_code' => 500, 'description' => "Internal Server Error: missing configurations"];
		}
		$this->api = new TelegramBot($this->configs);
		if (!$this->api->response['ok']) {
			return $this->response = $this->api->response;
		}
		$update = $this->api->getUpdate();
		$this->v = new Variables($this->configs, $update);
		if (!$this->v->response['ok']) {
			return $this->response = $this->v->response;
		}
		$this->db = new Database($this->configs);
		if (!$this->db->response['ok']) {
			return $this->response = $this->db->response;
		}
		$this->user = $this->loadUser();
		$this->group = $this->loadGroup();
		$this->channel = $this->loadChannel();
		
		# Stop script for banned chats
		if (!$this->v->isAdmin() and ($this->user['ban'] or $this->group['ban'] or $this->channel['ban'])) return $this->response = ['ok' => false, 'error_code' => 429, 'description' => 'Banned from the Bot'];
		
		# AntiFlood System
		if ($this->configs['redis']['status'] and $this->configs['redis']['antiflood']['status']) {
			if (isset($this->user['id'])) {
				$af = new AntiFlood($this->db, $this->user['id']);
				if ($af->banned) {
					if ($af->send_notice and $this->configs['redis']['antiflood']['notice']) $this->api->sendMessage($this->user['id'], $this->configs['redis']['antiflood']['notice']);
					return $this->response = ['ok' => false, 'error_code' => 429, 'description' => 'Banned user ' . $this->user['id']];
				}
			}
			if (isset($this->group['id'])) {
				$af = new AntiFlood($this->db, $this->group['id']);
				if ($af->banned) {
					if ($af->send_notice and $this->configs['redis']['antiflood']['notice']) $this->api->sendMessage($this->group['id'], $this->configs['redis']['antiflood']['notice']);
					return $this->response = ['ok' => false, 'error_code' => 429, 'description' => 'Banned group ' . $this->group['id']];
				}
			} elseif (isset($this->channel['id'])) {
				$af = new AntiFlood($this->db, $this->channel['id']);
				if ($af->banned) {
					if ($af->send_notice and $this->configs['redis']['antiflood']['notice']) $this->api->sendMessage($this->channel['id'], $this->configs['redis']['antiflood']['notice']);
					return $this->response = ['ok' => false, 'error_code' => 429, 'description' => 'Banned channel ' . $this->channel['id']];
				}
			}
		}
		return $this->response = ['ok' => 1];
	}
	
	public function loadUser () {
		if ($this->configs['database']['status']) return $this->db->getUser($this->v->getUser());
		return $this->v->getUser();
	}
	
	public function loadGroup () {
		if ($this->configs['database']['status']) {
			$q = $this->db->getGroup($this->v->getGroup());
			if (isset($q['id'])) {
				if (!is_array($q['permissions']) or empty($q['permissions']) or $q['last_seen'] <= (time() - 60 * 60)) {
					$q['permissions'] = [];
					$chat = $this->api->getChat($q['id']);
					if ($chat['ok'] and isset($chat['result'])) {
						$q['description'] = $chat['result']['description'];
						$q['permissions'] = $chat['result']['permissions'];
					}
					$this->db->query("UPDATE groups SET description = ?, permissions = ?, last_seen = ? WHERE id = ?", [$q['description'], json_encode($q['permissions']), $q['last_seen'], $q['id']]);
				}
				if (!is_array($q['admins']) or empty($q['admins']) or $q['last_seen'] <= (time() - 60 * 60)) {
					$q['admins'] = [];
					$admins = $this->api->getAdministrators($q['id']);
					if ($admins['ok'] and isset($admins['result'])) {
						$q['admins'] = $admins['result'];
					}
					$q['last_seen'] = time();
					$this->db->query("UPDATE groups SET admins = ?, last_seen = ? WHERE id = ?", [json_encode($q['admins']), $q['last_seen'], $q['id']]);
				}
			}
			$this->v->varChatAdministrators($q['admins']);
			return $q;
		}
		return $this->v->getGroup();
	}
	
	public function loadChannel() {
		if ($this->configs['database']['status']) {
			$q = $this->db->getChannel($this->v->getChannel());
			if (isset($q['id'])) {
				if (empty($q['admins']) or $q['last_seen'] <= (time() - 60 * 60)) {
					$q['admins'] = [];
					$admins = $this->api->getAdministrators($q['id']);
					if ($admins['ok'] and isset($admins['result'])) {
						$q['admins'] = $admins['result'];
					}
					$q['last_seen'] = time();
					$this->db->query("UPDATE channels SET admins = ?, last_seen = ? WHERE id = ?", [json_encode($q['admins']), $q['last_seen'], $q['id']]);
				}
			}
			$this->v->varChatAdministrators($q['admins']);
			return $q;
		}
		return $this->v->getChannel();
	}
}

?>
