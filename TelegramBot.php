<?php

class TelegramBot
{
	public $configs = [];
	public $token = '';
	public $id = false;
	public $response = [
		'ok' => false
	];
	public $update = [];
	
	public function __construct ($configs) {
		$this->configs = $configs;
		$this->token = $this->configs['token'];
		$this->id = explode(':', $this->token, 2)[0];
		if (!is_numeric($this->id)) {
			$this->response = ['ok' => false, 'error_code' => 500, 'description' => 'Internal Server Error: invalid token'];
		} else {
			$this->response = ['ok' => 1];
		}
	}
	
	# Edit NeleBot X configs ONLY for the current process
	public function editConfigs ($key, $value) {
		$this->configs[$key] = $value;
	}
	
	# Get the current NeleBot X configs
	public function getConfigs () {
		if (isset($this->configs)) return $this->configs;
		require('configs.php');
		return $this->configs = $configs;
	}
	
	# Send logs to Telegram chat
	public function sendLog ($message) {
		if (!$this->configs['log_chat']) return;
		$this->sendMessage($this->configs['log_chat'], $message);
	}
	
	# Requests
	public function request ($url, $args = [], $post = 'def', $response = 'def', $timeout = 'def') {
		if ($post === 'def')		$post = $this->configs['post'];
		if ($response === 'def')	$response = $this->configs['response'];
		if ($timeout === 'def')		$timeout = $this->configs['timeout'];
		if (!isset($this->curl))	$this->curl = curl_init();
		curl_setopt_array($this->curl, [
			CURLOPT_URL				=> $url,
			CURLOPT_POST			=> $post,
			CURLOPT_POSTFIELDS		=> $args,
			CURLOPT_TIMEOUT			=> $timeout,
			CURLOPT_RETURNTRANSFER	=> $response
		]);
		$output = curl_exec($this->curl);
		if ($json_output = json_decode($output, 1)) return $json_output;
		if ($output) return $output;
		if ($error = curl_error($this->curl)) return ['ok' => false, 'error_code' => 500, 'description' => 'CURL Error: ' . $error];
		return;
	}
	
	# Get the current Telegram Update
	public function getUpdate () {
		$this->update = [];
		if ($update = file_get_contents('php://input')) {
			if ($update = json_decode($update, 1)) {
				$this->update = $update;
			}
		}
		return $this->update;
	}
	
	# Create the Telegram Bot API url
	public function api ($method) {
		return $this->configs['telegram_bot_api'] . '/bot' . $this->token . '/' . $method;
	}
	
	// Telegram methods
	// Search the method name here to edit a function
	
	# getMe
	public function getMe () {
		return $this->request($this->api('getMe'), false, 'def', 1);
	}
	
	# logOut
	public function logOut () {
		return $this->request($this->api('logOut'), false, 'def', 1);
	}
	
	# close
	public function close () {
		return $this->request($this->api('close'), false, 'def', 1);
	}
	
	# sendMessage
	public function sendMessage ($chat_id, $text, $buttons = false, $parse = 'def', $preview = 'def', $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'text'		=> $text
		];
		if (is_array($parse) and !empty($parse)) {
			$args['entities'] = json_encode($parse);
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($preview === 'def') $preview = $this->configs['disable_web_page_preview'];
		if ($preview) $args['disable_web_page_preview'] = true;
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendMessage'), $args, 'def', $response);
	}

	# forwardMessage
	public function forwardMessage ($chat_id, $from_id, $id, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'from_chat_id'	=> $from_id,
			'message_id'	=> $id
		];
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('forwardMessage'), $args, 'def', $response);
	}

	# copyMessage
	public function copyMessage ($chat_id, $from_id, $id, $caption = false, $buttons = false, $parse = 'def', $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'from_chat_id'	=> $from_id,
			'message_id'	=> $id
		];
		if ($caption) {
			$args['caption'] = $caption;
			if (is_array($parse) and !empty($parse)) {
				$args['caption_entities'] = json_encode($parse);
			} else {
				if ($parse === 'def') $parse = $this->configs['parse_mode'];
				$args['parse_mode'] = $this->parseMode($parse);
			}
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = true;
		return $this->request($this->api('copyMessage'), $args, 'def', $response);
	}

	# sendPhoto
	public function sendPhoto ($chat_id, $document, $caption = false, $buttons = false, $parse = 'def', $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'photo'		=> $document
		];
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = json_encode($parse);
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendPhoto'), $args, 'def', $response);
	}

	# sendAudio
	public function sendAudio ($chat_id, $document, $caption = false, $buttons = false, $parse = 'def', $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'audio'		=> $document
		];
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = json_encode($parse);
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendAudio'), $args, 'def', $response);
	}

	# sendDocument
	public function sendDocument ($chat_id, $document, $caption = false, $buttons = false, $parse = 'def', $reply = false, $thumb = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'document'	=> $document
		];
		if ($thumb) $args['thumb'] = $thumb;
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = json_encode($parse);
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendDocument'), $args, 'def', $response);
	}

	# sendVideo
	public function sendVideo ($chat_id, $document, $caption = false, $buttons = false, $parse = 'def', $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'video'		=> $document
		];
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = json_encode($parse);
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendVideo'), $args, 'def', $response);
	}

	# sendAnimation
	public function sendAnimation ($chat_id, $document, $caption = false, $buttons = false, $parse = 'def', $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'animation'	=> $document
		];
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = json_encode($parse);
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendAnimation'), $args, 'def', $response);
	}

	# sendVoice
	public function sendVoice ($chat_id, $document, $caption = false, $buttons = false, $parse = 'def', $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'voice'		=> $document
		];
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = json_encode($parse);
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendVoice'), $args, 'def', $response);
	}

	# sendVideoNote
	public function sendVideoNote ($chat_id, $document, $buttons = false, $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'video_note'	=> $document
		];
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendVideoNote'), $args, 'def', $response);
	}

	# sendMediaGroup
	public function sendMediaGroup ($chat_id, $documents, $reply = false, $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'media'		=> json_encode($documents)
		];
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendMediaGroup'), $args, 'def', $response);
	}

	# sendLocation
	public function sendLocation ($chat_id, $lati, $long, $live = false, $buttons = false, $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'latitude'		=> $lati,
			'longitude'		=> $long
		];
		if ($live) {
			$args['live_period'] = $live;
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendLocation'), $args, 'def', $response);
	}

	# editMessageLiveLocation
	public function editLiveLocation($chat_id, $message_id, $lati, $long, $buttons = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'message_id'	=> $message_id,
			'latitude'		=> $lati,
			'longitude'		=> $long
		];
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		return $this->request($this->api('editMessageLiveLocation'), $args, 'def', $response);
	}

	# stopMessageLiveLocation
	public function stopLiveLocation($chat_id, $message_id, $buttons = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'message_id'	=> $message_id
		];
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		return $this->request($this->api('stopMessageLiveLocation'), $args, 'def', $response);
	}

	# sendVenue
	public function sendVenue ($chat_id, $lati, $long, $title, $address, $buttons = false, $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'latitude'		=> $lati,
			'longitude'		=> $long,
			'title'			=> $title,
			'address'		=> $address
		];
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendVenue'), $args, 'def', $response);
	}

	# sendContact
	public function sendContact ($chat_id, $number, $first_name, $last_name = false, $vcard = false, $buttons = false, $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'phone_number'	=> $number,
			'first_name'	=> $first_name
		];
		if ($last_name)  $args['last_name'] = $last_name;
		if ($vcard)  $args['vcard'] = $vcard;
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendContact'), $args, 'def', $response);
	}

	# sendPoll
	public function sendPoll ($chat_id, $question, $options, $is_anonymous = true, $type = "regular", $others = [], $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'question'		=> $question,
			'options'		=> $options,
			'is_anonymous'	=> $question,
			'type'			=> $type
		];
		if (!empty($others)) $args = array_merge($args, $others);
		return $this->request($this->api('sendPoll'), $args, 'def', $response);
	}

	# sendDice
	public function sendDice ($chat_id, $dice, $buttons = false, $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'emoji'		=> $dice
		];
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		return $this->request($this->api('sendDice'), $args, 'def', $response);
	}

	# sendChatAction
	public function sendAction ($chat_id, $action = 'typing', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'action'	=> $action
		];
		return $this->request($this->api('sendChatAction'), $args, 'def', $response);
	}

	# getUserProfilePhotos
	public function getUserPhotos ($user_id, $offset = false, $limit = 100) {
		$args = [
			'user_id'	=> $user_id,
			'offset'	=> $offset,
			'limit'		=> $limit
		];
		return $this->request($this->api('getUserProfilePhotos'), $args, 'def', 1);
	}

	# getFile
	public function getFile ($file_id) {
		$args = [
			'file_id'	=> $file_id
		];
		return $this->request($this->api('getFile'), $args, 'def', 1);
	}
	
	# kickChatMember
	public function kickMember ($chat_id, $user_id, $delete_all_from = 1, $until_date = false, $response = 'def') {
		$args = [
			'chat_id'			=> $chat_id,
			'user_id'			=> $user_id,
			'revoke_messages'	=> $delete_all_from,
			'until_date'		=> $until_date
		];
		return $this->request($this->api('kickChatMember'), $args, 'def', $response);
	}
	
	# unbanChatMember
	public function unbanMember ($chat_id, $user_id, $only_banned = 1, $response = 'def') {
		$args = [
			'chat_id'			=> $chat_id,
			'user_id'			=> $user_id,
			'only_if_banned'	=> $only_banned
		];
		return $this->request($this->api('unbanChatMember'), $args, 'def', $response);
	}
	
	# restrictChatMember
	public function restrictMember ($chat_id, $user_id, $permissions, $until_date = false, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'user_id'		=> $user_id,
			'permissions'	=> json_encode($permissions),
			'until_date'	=> $until_date
		];
		return $this->request($this->api('restrictChatMember'), $args, 'def', $response);
	}
	
	# promoteChatMember
	public function promoteMember ($chat_id, $user_id, $permissions, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'user_id'		=> $user_id
		];
		if (is_array($permissions)) {
			$args = array_merge($args, $permissions);
		} else {
			return ['ok' => false, 'error_code' => 400, 'description' => 'Bad Request: permissions must be an array'];
		}
		return $this->request($this->api('promoteChatMember'), $args, 'def', $response);
	}
	
	# setChatAdministratorCustomTitle
	public function setCustomTitle ($chat_id, $user_id, $custom_title, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'user_id'		=> $user_id,
			'custom_title'	=> $custom_title
		];
		return $this->request($this->api('setChatAdministratorCustomTitle'), $args, 'def', $response);
	}
	
	# banChatSenderChat
	public function banChatSender ($chat_id, $sender_id) {
		$args = [
			'chat_id'			=> $chat_id,
			'sender_chat_id'	=> $sender_id
		];
		return $this->request($this->api('banChatSenderChat'), $args, 'def', $response);
	}
	
	# unbanChatSenderChat
	public function unbanChatSender ($chat_id, $sender_id) {
		$args = [
			'chat_id'			=> $chat_id,
			'sender_chat_id'	=> $sender_id
		];
		return $this->request($this->api('unbanChatSenderChat'), $args, 'def', $response);
	}
	
	# setChatPermissions
	public function setPerms ($chat_id, $permissions, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'permissions'	=> json_encode($permissions)
		];
		return $this->request($this->api('setChatPermissions'), $args, 'def', $response);
	}
	
	# exportChatInviteLink
	public function getLink ($chat_id) {
		$args = [
			'chat_id'		=> $chat_id
		];
		return $this->request($this->api('exportChatInviteLink'), $args, 'def', 1);
	}
	
	# createChatInviteLink
	public function newLink ($chat_id, $expire = false, $members = false) {
		$args = [
			'chat_id'		=> $chat_id
		];
		if ($expire) $args['expire_date'] = $expire;
		if ($members) $args['member_limit'] = $members;
		return $this->request($this->api('createChatInviteLink'), $args, 'def', 1);
	}
	
	# editChatInviteLink
	public function editLink ($chat_id, $invite, $expire = false, $members = false, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'invite_link'	=> $invite
		];
		if ($expire) $args['expire_date'] = $expire;
		if ($members) $args['member_limit'] = $members;
		return $this->request($this->api('editChatInviteLink'), $args, 'def', $response);
	}
	
	# revokeChatInviteLink
	public function delLink ($chat_id, $invite, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'invite_link'	=> $invite
		];
		return $this->request($this->api('revokeChatInviteLink'), $args, 'def', $response);
	}
	
	# approveChatJoinRequest
	public function approveJoinRequest ($chat_id, $user_id) {
		$args = [
			'chat_id'			=> $chat_id,
			'user_id'			=> $user_id
		];
		return $this->request($this->api('approveChatJoinRequest'), $args, 'def', $response);
	}
	
	# declineChatJoinRequest
	public function declineJoinRequest ($chat_id, $user_id) {
		$args = [
			'chat_id'			=> $chat_id,
			'user_id'			=> $user_id
		];
		return $this->request($this->api('declineChatJoinRequest'), $args, 'def', $response);
	}
	
	# setChatPhoto
	public function setPhoto ($chat_id, $photo, $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'photo'		=> $photo
		];
		return $this->request($this->api('setChatPhoto'), $args, 'def', $response);
	}
	
	# deleteChatPhoto
	public function delPhoto ($chat_id, $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id
		];
		return $this->request($this->api('deleteChatPhoto'), $args, 'def', $response);
	}
	
	# setChatTitle
	public function setTitle ($chat_id, $title, $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'title'		=> $title
		];
		return $this->request($this->api('setChatTitle'), $args, 'def', $response);
	}
	
	# setChatDescription
	public function setDescription ($chat_id, $description, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'description'	=> $description
		];
		return $this->request($this->api('setChatDescription'), $args, 'def', $response);
	}
	
	# pinChatMessage
	public function pinMessage ($chat_id, $message_id, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'message_id'	=> $message_id
		];
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('pinChatMessage'), $args, 'def', $response);
	}
	
	# unpinChatMessage
	public function unpinMessage ($chat_id, $message_id = false, $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id
		];
		if ($message_id) $args['message_id'] = $message_id;
		return $this->request($this->api('unpinChatMessage'), $args, 'def', $response);
	}
	
	# unpinAllChatMessages
	public function unpinAll ($chat_id, $message_id = false, $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id
		];
		return $this->request($this->api('unpinAllChatMessages'), $args, 'def', $response);
	}
	
	# leaveChat
	public function leave ($chat_id, $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id
		];
		return $this->request($this->api('leaveChat'), $args, 'def', $response);
	}
	
	# getChat
	public function getChat ($chat_id) {
		$args = [
			'chat_id'	=> $chat_id
		];
		return $this->request($this->api('getChat'), $args, 'def', 1);
	}
	
	# getChatAdministrators
	public function getAdministrators ($chat_id) {
		$args = [
			'chat_id'	=> $chat_id
		];
		return $this->request($this->api('getChatAdministrators'), $args, 'def', 1);
	}
	
	# getChatMembersCount
	public function getMembersCount ($chat_id) {
		$args = [
			'chat_id'	=> $chat_id
		];
		return $this->request($this->api('getChatMembersCount'), $args, 'def', 1);
	}
	
	# getChatMember
	public function getMember ($chat_id, $user_id) {
		$args = [
			'chat_id'	=> $chat_id,
			'user_id'	=> $user_id
		];
		return $this->request($this->api('getChatMember'), $args, 'def', 1);
	}
	
	# setChatStickerSet
	public function setStickerSet ($chat_id, $sticker_set, $response = 'def') {
		$args = [
			'chat_id'			=> $chat_id,
			'sticker_set_name'	=> $sticker_set
		];
		return $this->request($this->api('setChatStickerSet'), $args, 'def', $response);
	}
	
	# deleteChatStickerSet
	public function delStickerSet ($chat_id, $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id
		];
		return $this->request($this->api('deleteChatStickerSet'), $args, 'def', $response);
	}
	
	# answerCallbackQuery
	public function answerCBQ ($cbq_id, $text = false, $alert = false, $url = false, $cache_time = false) {
		$args = [
			'callback_query_id'	=> $cbq_id
		];
		if ($text) {
			$args['text'] = $text;
			$args['show_alert'] = $alert;
		} elseif ($url) {
			$args['url'] = $url;
		}
		if ($cache_time) $args['cache_time'] = $cache_time;
		return $this->request($this->api('answerCallbackQuery'), $args, 'def', $response);
	}
	
	# setMyCommands
	public function setCommands ($commands = '[]', $scope = '{"type":"default"}', $lang = '', $response = 'def') {
		$args = [
			'commands'		=> $commands,
			'scope'			=> $scope,
			'language_code'	=> $lang
		];
		return $this->request($this->api('setMyCommands'), $args, 'def', $response);
	}
	
	# deleteMyCommands
	public function delCommand ($scope = '{"type":"default"}', $lang = '', $response = 'def') {
		$args = [
			'scope'			=> $scope,
			'language_code'	=> $lang
		];
		return $this->request($this->api('deleteMyCommands'), $args, 'def', $response);
	}
	
	# getMyCommands
	public function getCommands () {
		return $this->request($this->api('getMyCommands'), $args, 'def', 1);
	}
	
	# setChatMenuButton
	public function setChatButton ($chat_id, $button) {
		$args = [
			'chat_id'		=> $chat_id,
			'menu_button'	=> json_encode($button)
		];
		return $this->request($this->api('setChatMenuButton'), $args);
	}
	
	# getChatMenuButton
	public function getChatButton ($chat_id) {
		$args = [
			'chat_id'		=> $chat_id
		];
		return $this->request($this->api('getChatMenuButton'), $args, 'def', 1);
	}
	
	# setMyDefaultAdministratorRights
	public function setMyAdminRights ($rights = [], $for_channels = false) {
		$args = [
			'rights'		=> json_encode($rights)
		];
		if ($for_channels) $args['for_channels'] = true;
		return $this->request($this->api('setMyDefaultAdministratorRights'), $args);
	}
	
	# editMessageText
	public function editText ($chat_id, $message_id, $text, $buttons = false, $parse = 'def', $preview = 'def', $buttonsType = 'inline', $response = 'def') {
		if ($chat_id) {
			$args = [
				'chat_id'		=> $chat_id,
				'message_id'	=> $message_id
			];
		} else {
			$args['inline_message_id'] = $message_id;
		}
		$args['text'] = $text;
		if (is_array($parse) and !empty($parse)) {
			$args['entities'] = json_encode($parse);
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($preview === 'def') $preview = $this->configs['disable_web_page_preview'];
		if ($preview) $args['disable_web_page_preview'] = true;
		return $this->request($this->api('editMessageText'), $args, 'def', $response);
	}
	
	# editMessageCaption
	public function editCaption ($chat_id, $message_id, $caption = false, $buttons = false, $parse = 'def', $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'message_id'	=> $message_id
		];
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['entities'] = json_encode($parse);
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		return $this->request($this->api('editMessageCaption'), $args, 'def', $response);
	}
	
	# editMessageMedia
	public function editMedia ($chat_id, $message_id, $media, $buttons = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'message_id'	=> $message_id,
			'media'			=> json_encode($media)
		];
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		return $this->request($this->api('editMessageMedia'), $args, 'def', $response);
	}
	
	# editMessageReplyMarkup
	public function editReplyMarkup ($chat_id, $message_id, $buttons = [], $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'message_id'	=> $message_id
		];
		$args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		return $this->request($this->api('editMessageReplyMarkup'), $args, 'def', $response);
	}
	
	# deleteMessage
	public function deleteMessage ($chat_id, $message_id, $response = 'def') {
		$args = [
			'chat_id'		=> $chat_id,
			'message_id'	=> $message_id
		];
		return $this->request($this->api('deleteMessage'), $args, 'def', $response);
	}
	
	# sendSticker
	public function sendSticker ($chat_id, $document, $buttons = false, $reply = false, $buttonsType = 'inline', $response = 'def') {
		$args = [
			'chat_id'	=> $chat_id,
			'sticker'	=> $document
		];
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendSticker'), $args, 'def', $response);
	}

	# getStickerSet
	public function getStickers ($set) {
		$args = [
			'name'	=> $set
		];
		return $this->request($this->api('getStickerSet'), $args, 'def', 1);
	}
	
	# uploadStickerFile
	public function uploadSticker ($user_id, $document) {
		$args = [
			'user_id'		=> $user_id,
			'png_sticker'	=> $document
		];
		return $this->request($this->api('uploadStickerFile'), $args, 'def', 1);
	}
	
	# createNewStickerSet
	public function createStickers ($user_id, $set, $title, $sticker, $is_animated = false, $is_video = false, $emojis = false, $contains_masks = false, $mask_position = false, $response = 'def') {
		$args = [
			'user_id'		=> $user_id,
			'name'			=> $set,
			'title'			=> $title
		];
		if ($is_video) {
			$args['webm_sticker'] = $sticker;
		} elseif ($is_animated) {
			$args['tgs_sticker'] = $sticker;
		} else {
			$args['png_sticker'] = $sticker;
		}
		if ($emojis) $args['emojis'] = $emojis;
		if ($contains_masks) {
			$args['contains_masks'] = 1;
			if ($mask_position) $args['mask_position'] = json_encode($mask_position);
		}
		return $this->request($this->api('createNewStickerSet'), $args, 'def', $response);
	}
	
	# addStickerToSet
	public function addSticker ($user_id, $set, $sticker, $is_animated = false, $is_video = false, $emojis = false, $mask_position = false, $response = 'def') {
		$args = [
			'user_id'		=> $user_id,
			'name'			=> $set
		];
		if ($is_video) {
			$args['webm_sticker'] = $sticker;
		} elseif ($is_animated) {
			$args['tgs_sticker'] = $sticker;
		} else {
			$args['png_sticker'] = $sticker;
		}
		if ($emojis) $args['emojis'] = $emojis;
		if ($mask_position) $args['mask_position'] = json_encode($mask_position);
		return $this->request($this->api('addStickerToSet'), $args, 'def', $response);
	}
	
	# setStickerPositionInSet
	public function setStickerPos ($sticker, $position, $response = 'def') {
		$args = [
			'sticker'	=> $sticker,
			'position'	=> $positiont
		];
		return $this->request($this->api('setStickerPositionInSet'), $args, 'def', $response);
	}
	
	# deleteStickerFromSet
	public function delSticker ($sticker, $response = 'def') {
		$args = [
			'sticker'	=> $sticker
		];
		return $this->request($this->api('deleteStickerFromSet'), $args, 'def', $response);
	}
	
	# setStickerSetThumb
	public function setStickersThumb ($set, $user_id, $thumb, $response = 'def') {
		$args = [
			'name'		=> $set,
			'user_id'	=> $user_id,
			'thumb'		=> $thumb
		];
		return $this->request($this->api('setStickerSetThumb'), $args, 'def', $response);
	}
	
	# answerInlineQuery
	public function answerIQ ($inline_id, $results = [], $switch_pm_text = false, $switch_pm_parameter = false, $next_offset = false, $is_personal = false, $response = 'def') {
		$args = [
			'inline_query_id'	=> $inline_id,
			'results'			=> json_encode($results),
			'cache_time'		=> 0
		];
		if ($switch_pm_text and $switch_pm_parameter) {
			$args['switch_pm_text'] = $switch_pm_text;
			$args['switch_pm_parameter'] = $switch_pm_parameter;
		}
		if ($is_personal) $args['is_personal'] = 1;
		if ($next_offset) $args['next_offset'] = $next_offset;
		return $this->request($this->api('answerInlineQuery'), $args, 'def', $response);
	}

	# answerWebAppQuery
	public function answerWAQ ($web_app_query_id, $result = []) {
		$args = [
			'inline_query_id'	=> $inline_id,
			'result'			=> json_encode($result)
		];
		return $this->request($this->api('answerWebAppQuery'), $args, 'def', $response);
	}
	
	# sendInvoices 
	public function sendInvoice($chat_id, $title, $description, $payload, $provider_token, $currency, $prices, $reply, $buttons, $buttonsType = null) {
		$args = [
			'chat_id'        => $chat_id,
			'title'          => $title,
			'description'    => $description,
			'payload'        => $payload,
			'provider_token' => $provider_token,
			'currency'       => $currency,
			'prices'         => json_encode($prices)
		];
		if ($reply and is_numeric($reply)) {
			$args['reply_to_message_id'] = $reply;
			$args['allow_sending_without_reply'] = $this->configs['allow_sending_without_reply'];
		}
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($buttons) $args['reply_markup'] = json_encode($this->createButtons($buttons, $buttonsType));
		return $this->request($this->api('sendInvoice'), $args);
	}
	
	# AnswerShippingQuery
	public function answerSQ($shipping_query_id, $ok, $shipping_options = [], $error_message = false) {
		$args = [
			'shipping_query_id' => $shipping_query_id,
			'ok'                => $ok
		];
		if (!empty($shipping_options)) $args['shipping_options'] = json_encode($shipping_options);
		if ($error_message) $args['error_message'] = $error_message;
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendInvoice'), $args, 'def', $response);
	}
	
	# AnswerPreCheckoutQuery
	public function answerPCQ($pre_checkout_query_id, $ok, $error_message = false) {
		$args = [
			'pre_checkout_query_id' => $pre_checkout_query_id,
			'ok'                    => $ok
		];
		if ($error_message) $args['error_message'] = $error_message;
		if ($this->configs['protect_content']) $args['protect_content'] = 1;
		if ($this->configs['disable_notification']) $args['disable_notification'] = 1;
		return $this->request($this->api('sendInvoice'), $args, 'def', $response);
	}

	/*		Create Bot API types		*/
	
	# ReplyKeyboardMarkup
	public function createButtons ($buttons = [], $buttonsType = 'inline') {
		if ($buttonsType == 'reply') {
			return $this->createForceReply(1);
		} elseif ($buttonsType == 'remove') {
			return $this->createReplyKeyboardRemove(1);
		} elseif ($buttonsType == 'inline') {
			return [
				'inline_keyboard' => $buttons
			];
		} else {
			return [
				'keyboard' => $buttons,
				'resize_keyboard' => 1
			];
		}
	}

	# KeyboardButton
	public function createButton ($text, $contact = false, $location = false, $poll = false) {
		$args = [
			'text'	=> $text
		];
		if ($contact) {
			$args['request_contact'] = true;
		} elseif ($location) {
			$args['request_location'] = true;
		} elseif ($poll) {
			$args['request_poll'] = $poll;
		}
		return $args;
	}

	# ReplyKeyboardRemove
	public function createReplyKeyboardRemove ($selective = false) {
		$args = [
			'remove_keyboard'	=> 1
		];
		if ($selective) $args['selective'] = true;
		return $args;
	}

	# InlineKeyboardButton
	public function createInlineButton ($text, $data, $type = 'callback_data') {
		$args = [
			'text'	=> $text
		];
		if (in_array($type, ['url', 'switch_inline_query', 'switch_inline_query_current_chat', 'callback_game', 'pay'])) {
			$args[$type] = $data;
		} else {
			$args['callback_data'] = $data;
		}
		return $args;
	}

	# LoginUrl
	public function createLoginButton ($url, $text = false, $request_write = false, $bot_username = false) {
		$args = [
			'url'	=> $url
		];
		if ($text) $args['forward_text'] = $text;
		if ($bot_username) $args['bot_username'] = $bot_username;
		if ($request_write) $args['request_write_access'] = $request_write;
		return $args;
	}

	# ForceReply
	public function createForceReply ($selective = false) {
		$args = [
			'force_reply'	=> 1
		];
		if ($selective) $args['selective'];
		return $args;
	}

	# ChatPermissions
	public function createChatPermissions ($messages = false, $media = 'null', $polls = 'null', $other = 'null', $previews = 'null', $change_info = 'null', $invite = 'null', $pin = 'null') {
		$args['can_send_messages'] = $messages;
		if ($messages) {
			if ($media !== 'null') $args['can_send_media_messages'] = $media;
			if ($polls !== 'null') $args['can_send_polls'] = $polls;
			if ($args['can_send_media_messages']) {
				if ($other !== 'null') $args['can_send_other_messages'] = $other;
				if ($previews !== 'null') $args['can_add_web_page_previews'] = $previews;
			}
		}
		if ($change_info !== 'null') $args['can_change_info'] = $change_info;
		if ($invite !== 'null') $args['can_invite_users'] = $invite;
		if ($pin !== 'null') $args['can_pin_messages'] = $pin;
		return $args;
	}

	# botCommand
	public function createBotCommand ($command, $description) {
		return [
			'command'		=> $command,
			'description'	=> $description
		];
	}

	# InputMediaAnimation
	public function createAnimationInput($animation, $caption = false, $parse = 'def') {
		$args = [
			'type'		=> 'animation',
			'media'		=> $animation
		];
		if ($caption) {
			$args['caption'] = $caption;
			if (is_array($parse) and !empty($parse)) {
				$args['caption_entities'] = json_encode($parse);
			} else {
				if ($parse === 'def') $parse = $this->configs['parse_mode'];
				$args['parse_mode'] = $this->parseMode($parse);
			}
		}
		return $args;
	}

	# InputMediaDocument
	public function createDocumentInput($document, $caption = false, $parse = 'def', $thumb = false, $disable_content_type_detection = false) {
		$args = [
			'type'		=> 'document',
			'media'		=> $document
		];
		if ($caption) {
			$args['caption'] = $caption;
			if (is_array($parse) and !empty($parse)) {
				$args['caption_entities'] = json_encode($parse);
			} else {
				if ($parse === 'def') $parse = $this->configs['parse_mode'];
				$args['parse_mode'] = $this->parseMode($parse);
			}
		}
		if ($thumb) $args['thumb'] = $thumb;
		if ($disable_content_type_detection) $disable_content_type_detection = true;
		return $args;
	}

	# InputMediaAudio
	public function createAudioInput($audio, $caption = false, $parse = 'def', $title = false) {
		$args = [
			'type'		=> 'audio',
			'media'		=> $audio
		];
		if ($caption) {
			$args['caption'] = $caption;
			if (is_array($parse) and !empty($parse)) {
				$args['caption_entities'] = json_encode($parse);
			} else {
				if ($parse === 'def') $parse = $this->configs['parse_mode'];
				$args['parse_mode'] = $this->parseMode($parse);
			}
		}
		if ($title) $args['title'] = $title;
		return $args;
	}

	# InputMediaPhoto
	public function createPhotoInput($photo, $caption = false, $parse = 'def') {
		$args = [
			'type'		=> 'photo',
			'media'		=> $photo
		];
		if ($caption) {
			$args['caption'] = $caption;
			if (is_array($parse) and !empty($parse)) {
				$args['caption_entities'] = json_encode($parse);
			} else {
				if ($parse === 'def') $parse = $this->configs['parse_mode'];
				$args['parse_mode'] = $this->parseMode($parse);
			}
		}
		return $args;
	}

	# InputFile
	public function createFileInput($file_name, $mime_content_type = null, $rename = null) {
		if (function_exists('curl_file_create')) return curl_file_create($file_name, $mime_content_type, $rename);
		return false;
	}

	# InlineQueryResultArticle
	public function createInlineArticle ($id, $title, $description = false, $input, $buttons = false, $url = false, $hide_url = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'article',
			'id'					=> $id,
			'title'					=> $title,
			'input_message_content'	=> $input
		];
		if ($description) $args['description'] = $description;
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		if ($url) $args['url'] = $url;
		if ($hide_url) $args['hide_url'] = 1;
		if ($thumb) $args['thumb_url'] = $thumb;
		return $args;
	}
	
	# InlineQueryResultPhoto
	public function createInlinePhoto ($id, $title, $description = false, $document, $caption = false, $parse = 'def', $buttons = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'photo',
			'id'					=> $id,
			'photo_url'				=> $document,
			'title'					=> $title
		];
		if ($description) $args['description'] = $description;
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = $parse;
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if ($thumb) {
			$args['thumb_url'] = $thumb;
		} else {
			$args['thumb_url'] = $document;
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InlineQueryResultGif
	public function createInlineGif ($id, $title, $description = false, $document, $caption = false, $parse = 'def', $buttons = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'gif',
			'id'					=> $id,
			'gif_url'				=> $document,
			'title'					=> $title
		];
		if ($description) $args['description'] = $description;
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = $parse;
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if ($thumb) {
			$args['thumb_url'] = $thumb;
		} else {
			$args['thumb_url'] = $document;
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InlineQueryResultVideo
	public function createInlineVideo ($id, $title, $description = false, $document, $caption = false, $parse = 'def', $buttons = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'video',
			'id'					=> $id,
			'video_url'				=> $document,
			'title'					=> $title
		];
		if ($description) $args['description'] = $description;
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = $parse;
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if ($thumb) {
			$args['thumb_url'] = $thumb;
		} else {
			$args['thumb_url'] = $document;
		}
		$args['mime_type'] = 'video/mp4';
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InlineQueryResultAudio
	public function createInlineAudio ($id, $title, $description = false, $document, $caption = false, $parse = 'def', $buttons = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'audio',
			'id'					=> $id,
			'audio_url'				=> $document,
			'title'					=> $title
		];
		if ($description) $args['description'] = $description;
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = $parse;
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InlineQueryResultVoice
	public function createInlineVoice ($id, $title, $description = false, $document, $caption = false, $parse = 'def', $buttons = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'voice',
			'id'					=> $id,
			'voice_url'				=> $document,
			'title'					=> $title
		];
		if ($description) $args['description'] = $description;
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = $parse;
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InlineQueryResultDocument
	public function createInlineDocument ($id, $title, $description = false, $document, $caption = false, $parse = 'def', $buttons = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'document',
			'id'					=> $id,
			'document_url'			=> $document,
			'title'					=> $title
		];
		if ($description) $args['description'] = $description;
		if ($caption) $args['caption'] = $caption;
		if (is_array($parse) and !empty($parse)) {
			$args['caption_entities'] = $parse;
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		$args['mime_type'] = 'application/pdf';
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InlineQueryResultLocation
	public function createInlineLocation ($id, $title, $lati, $long, $live = false, $buttons = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'location',
			'id'					=> $id,
			'latitude'				=> $lati,
			'longitude'				=> $long,
			'title'					=> $title
		];
		if ($live) $args['live_period'] = $live;
		if ($thumb) $args['thumb_url'] = $thumb;
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InlineQueryResultVenue
	public function createInlineVenue ($id, $title, $address, $lati, $long, $buttons = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'venue',
			'id'					=> $id,
			'latitude'				=> $lati,
			'longitude'				=> $long,
			'title'					=> $title,
			'address'				=> $address
		];
		if ($thumb) $args['thumb_url'] = $thumb;
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InlineQueryResultContact
	public function createInlineContact ($id, $number, $first_name, $last_name = false, $vcard = false, $buttons = false, $thumb = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'contact',
			'id'					=> $id,
			'phone_number'			=> $number,
			'first_name'			=> $first_name
		];
		if ($last_name) $args['last_name'] = $last_name;
		if ($vcard) $args['last_name'] = $vcard;
		if ($thumb) $args['thumb_url'] = $thumb;
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InlineQueryResultGame
	public function createInlineGame ($id, $title, $buttons = false, $buttonsType = 'inline') {
		$args = [
			'type'					=> 'game',
			'id'					=> $id,
			'game_short_name'		=> $title
		];
		if (!empty($buttons) and is_array($buttons)) $args['reply_markup'] = $this->createButtons($buttons, $buttonsType);
		return $args;
	}
	
	# InputTextMessageContent
	public function createTextInput($text, $parse = 'def', $preview = 'def') {
		$args = [
			'message_text'	=> $text
		];
		if (is_array($parse) and !empty($parse)) {
			$args['entities'] = $parse;
		} else {
			if ($parse === 'def') $parse = $this->configs['parse_mode'];
			$args['parse_mode'] = $this->parseMode($parse);
		}
		if ($preview === 'def') $preview = $this->configs['disable_web_page_preview'];
		if ($preview) $args['disable_web_page_preview'] = true;
		return $args;
	}
	
	# InputLocationMessageContent
	public function createLocationInput($lati, $long, $live = false) {
		$args = [
			'latitude'	=> $lati,
			'longitude'	=> $long
		];
		if ($live) $args['live_period'] = $live;
		return $args;
	}
	
	# InputVenueMessageContent
	public function createVenueInput($lati, $long, $title, $address) {
		$args = [
			'latitude'	=> $lati,
			'longitude'	=> $long,
			'title'		=> $title,
			'address'	=> $address
		];
		return $args;
	}
	
	# InputContactMessageContent
	public function createContactInput($number, $first_name, $last_name = false, $vcard = false) {
		$args = [
			'phone_number'	=> $number,
			'first_name'	=> $first_name
		];
		if ($last_name) $args['last_name'] = $last_name;
		if ($vcard) $args['vcard'] = $vcard;
		return $args;
	}
	
	# SentWebAppMessage
	public function createWebAppMessage($inline_message_id) {
		return [
			'inline_message_id'	=> $inline_message_id
		];
	}

	/*		Secondary Bot API function		*/
	
	# Valid Parse Mode
	public function parseMode ($parse) {
		if (in_array(strtolower($parse), ['html', 'markdown', 'markdownv2'])) {
			return $parse;
		} else {
			return '';
		}
	}
	
	# Special characters
	public function specialchars ($text, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($parse == 'html') {
			return htmlspecialchars($text);
		} elseif ($parse == 'markdown') {
			return $this->mdspecialchars($text);
		} elseif ($parse == 'markdownv2') {
			return $this->md2specialchars($text);
		} else {
			return $text;
		}
	}
	
	# Markdown special chars
	public function mdspecialchars ($text) {
		# To Do
		return $text;
	}
	
	# MarkdownV2 special chars
	public function md2specialchars ($text) {
		# To Do
		return $text;
	}
	
	# Bold
	public function bold ($text, $specialchars = false, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($specialchars) $text = $this->specialchars($text, $parse);
		if ($parse == 'html') {
			return '<b>' . $text . '</b>';
		} elseif ($parse == 'markdown' or $parse == 'markdownv2') {
			return '*' . $text . '*';
		} else {
			return $text;
		}
	}
	
	# Italic
	public function italic ($text, $specialchars = false, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($specialchars) $text = $this->specialchars($text, $parse);
		if ($parse == 'html') {
			return '<i>' . $text . '</i>';
		} elseif ($parse == 'markdown' or $parse == 'markdownv2') {
			return '_' . $text . '_';
		} else {
			return $text;
		}
	}
	
	# Underline
	public function underline ($text, $specialchars = false, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($specialchars) $text = $this->specialchars($text, $parse);
		if ($parse == 'html') {
			return '<u>' . $text . '</u>';
		} elseif ($parse == 'markdownv2') {
			return '__' . $text . '__';
		} else {
			return $text;
		}
	}
	
	# Strikethrough
	public function strikethrough ($text, $specialchars = false, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($specialchars) $text = $this->specialchars($text, $parse);
		if ($parse == 'html') {
			return '<s>' . $text . '</s>';
		} elseif ($parse == 'markdownv2') {
			return '~' . $text . '~';
		} else {
			return $text;
		}
	}
	
	# Spoiler
	public function spoiler ($text, $specialchars = false, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($specialchars) $text = $this->specialchars($text, $parse);
		if ($parse == 'html') {
			return '<tg-spoiler>' . $text . '</tg-spoiler>';
		} elseif ($parse == 'markdownv2') {
			return '||' . $text . '||';
		} else {
			return $text;
		}
	}
	
	# Text Link
	public function text_link ($text, $link, $specialchars = false, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($specialchars) $text = $this->specialchars($text, $parse);
		if ($parse == 'html') {
			return '<a href="' . $this->specialchars($link) . '">' . $text . '</a>';
		} elseif ($parse == 'markdown' or $parse == 'markdownv2') {
			return '[' . $text . '](' . $this->specialchars($link) . ')';
		} else {
			return $text . '(' . $link . ')';
		}
	}
	
	# Tag
	public function tag ($id, $text, $specialchars = false, $parse = 'def') {
		return $this->text_link($text, 'tg://user?id=' . $id, $specialchars, $parse);
	}
	
	# Inline fixed-width code
	public function code ($text, $specialchars = false, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($specialchars) $text = $this->specialchars($text, $parse);
		if ($parse == 'html') {
			return '<code>' . $text . '</code>';
		} elseif ($parse == 'markdown' or $parse == 'markdownv2') {
			return '`' . $text . '`';
		} else {
			return $text;
		}
	}
	
	# Pre-formatted fixed-width code block
	public function pre ($text, $specialchars = false, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($specialchars) $text = $this->specialchars($text, $parse);
		if ($parse == 'html') {
			return '<pre>' . $text . '</pre>';
		} elseif ($parse == 'markdown' or $parse == 'markdownv2') {
			return '```' . PHP_EOL . $text . PHP_EOL . '```';
		} else {
			return $text;
		}
	}
	
	# Pre-formatted fixed-width code block written in the Python programming language
	public function prepy ($text, $specialchars = false, $parse = 'def') {
		if ($parse === 'def') $parse = $this->configs['parse_mode'];
		$parse = $this->parseMode($parse);
		if ($specialchars) $text = $this->specialchars($text, $parse);
		if ($parse == 'html') {
			return '<pre><code class="language-python">' . $text . '</code></pre>';
		} elseif ($parse == 'markdown' or $parse == 'markdownv2') {
			return '```python' . PHP_EOL . $text . PHP_EOL . '```';
		} else {
			return $text;
		}
	}
}

?>