<?php

class AntiFlood {
	public $messages = 5;
	public $seconds = 1;
	public $bantime = 0;
	public $banned = 0;
	public $send_notice = 0;
	public $key = 'NeleBotX-AF-0';
	public $updates = [];
	
	public function __construct ($db, $id) {
		if (!$db or in_array($id, $db->configs['admins'])) return;
		if (is_numeric($db->configs['redis']['antiflood']['messages'])) $this->messages = $db->configs['redis']['antiflood']['messages'];
		if (is_numeric($db->configs['redis']['antiflood']['seconds'])) $this->seconds = $db->configs['redis']['antiflood']['seconds'];
		if (is_numeric($db->configs['redis']['antiflood']['ban_time'])) $this->bantime = $db->configs['redis']['antiflood']['ban_time'];
		$this->rkey = 'NeleBotX-AF-' . $id;
		$this->rbkey = 'NeleBotX-AF-' . $id . '-ban';
		if ($db->rget($this->rbkey)) return $this->banned = 1;
		$db->rladd($this->rkey, time());
		$db->redis->expire($this->rkey, 60);
		if (!empty($this->updates = $db->rlget($this->rkey))) {
			foreach ($this->updates as $k => $time) {
				if ($time <= (time() - $this->seconds)) {
					unset($this->updates[$k]);
					$db->rldel($this->rkey, $time, $k);
				}
			}
			if (count($this->updates) >= $this->messages) {
				$db->rset($this->rbkey, 1, round($this->bantime));
				# $db->ban($id); // [Database required] Only if you want to automatically perma-ban users with this AntiFlood System
				$db->rdel($this->rkey);
				$this->banned = 1;
				return $this->send_notice = 1;
			}
		}
	}	
}

?>