<?php

class Variables
{
	public $configs = [];
	public $update = [];
	public $response = [
		'ok' => 0
	];
	
	public function __construct ($configs, $update) {
		$this->configs = $configs;
		if (empty($update)) return $this->response = ['ok' => 1];
		$this->update = $update;
		if (!isset($this->update['update_id'])) {
			return $this->response = ['ok' => 0, 'error_code' => 400, 'description' => 'Bad Request: update_id not found'];
		} elseif ($this->update['message']) {
			$this->varMessage($this->update['message']);
		} elseif ($this->update['edited_message']) {
			$this->varMessage($this->update['edited_message']);
		} elseif ($this->update['channel_post']) {
			$this->varMessage($this->update['channel_post']);
		} elseif ($this->update['edited_channel_post']) {
			$this->varMessage($this->update['edited_channel_post']);
		} elseif ($this->update['inline_query']) {
			$this->varInlineQuery($this->update['inline_query']);
		} elseif ($this->update['chosen_inline_result']) {
			$this->varChosenInlineResult($this->update['chosen_inline_result']);
		} elseif ($this->update['callback_query']) {
			$this->varCallbackQuery($this->update['callback_query']);
		} elseif ($this->update['shipping_query']) {
			$this->varShippingQuery($this->update['shipping_query']);
		} elseif ($this->update['pre_checkout_query']) {
			$this->varPreCheckoutQuery($this->update['pre_checkout_query']);
		} elseif ($this->update['poll']) {
			$this->varPoll($this->update['poll']);
		} elseif ($this->update['poll_answer']) {
			$this->varPollAnswer($this->update['poll_answer']);
		} elseif ($this->update['my_chat_member']) {
			$this->varChatMemberUpdated($this->update['my_chat_member']);
		} elseif ($this->update['chat_member']) {
			$this->varChatMemberUpdated($this->update['chat_member']);
		} elseif ($this->update['chat_join_request']) {
			$this->varChatJoinRequest($this->update['chat_join_request']);
		} else {
			return $this->response = ['ok' => 0, 'error_code' => 404, 'description' => 'Not Found: unknown update type'];
		}
		return $this->response = ['ok' => 1];
	}

	public function isAdmin($id = 'def') {
		if ($id === 'def') {
			return in_array($this->user_id, $this->configs['admins']);
		} elseif (!is_numeric($id)) {
			return 0;
		} else {
			return in_array($id, $this->configs['admins']);
		}
	}
	
	public function isOwner($id = 'def') {
		if ($id === 'def' and isset($this->administrators[$this->user_id]) and $this->administrators[$this->user_id]['status'] == 'creator') {
			return 1;
		} elseif (isset($this->administrators[$id]) and $this->administrators[$id]['status'] == 'creator') {
			return 1;
		} else {
			return 0;
		}
	}
	
	public function isStaff($id = 'def') {
		if ($id === 'def' and isset($this->administrators[$this->user_id])) {
			return 1;
		} elseif (isset($this->administrators[$id])) {
			return 1;
		} else {
			return 0;
		}
	}
	
	public function varChatAdministrators ($ChatAdministrators) {
		if (empty($ChatAdministrators)) return;
		foreach ($ChatAdministrators as $Administrator) {
			$this->administrators[$Administrator['user']['id']] = $Administrator;
		}
	}
	
	public function varUser ($User) {
		if (empty($User)) return;
		$this->user_id = $User['id'];
		$this->user_is_bot = $User['is_bot'];
		$this->user_first_name = $User['first_name'];
		$this->user_last_name = $User['last_name'];
		$this->user_username = $User['username'];
		$this->user_language_code = $User['language_code'];
		$this->user_is_premium = $User['is_premium'];
	}
	
	public function varForwardUser ($User) {
		if (empty($User)) return;
		$this->f_user_id = $User['id'];
		$this->f_user_is_bot = $User['is_bot'];
		$this->f_user_first_name = $User['first_name'];
		$this->f_user_last_name = $User['last_name'];
		$this->f_user_username = $User['username'];
		$this->f_user_language_code = $User['language_code'];
	}
	
	public function varChat ($Chat) {
		if (empty($Chat)) return;
		$this->chat_id = $Chat['id'];
		$this->chat_type = $Chat['type'];
		$this->chat_title = $Chat['title'];
		$this->chat_username = $Chat['username'];
		$this->chat_is_forum = $Chat['is_forum'];
		$this->chat_first_name = $Chat['first_name'];
		$this->chat_last_name = $Chat['last_name'];
	}
	
	public function varForwardChat ($Chat) {
		if (empty($Chat)) return;
		$this->f_chat_id = $Chat['id'];
		$this->f_chat_type = $Chat['type'];
		$this->f_chat_title = $Chat['title'];
		$this->f_chat_username = $Chat['username'];
		$this->f_chat_first_name = $Chat['first_name'];
		$this->f_chat_last_name = $Chat['last_name'];
	}
	
	public function varMessage ($Message) {
		if (empty($Message)) return;
		$this->message_id = $Message['message_id'];
		$this->message_thread_id  = $Message['message_thread_id '];
		$this->varUser($Message['from']);
		$this->sender_chat = $Message['sender_chat'];
		$this->date = $Message['date'];
		$this->varChat($Message['chat']);
		$this->varForwardUser($Message['forward_from']);
		$this->varForwardChat($Message['forward_chat']);
		$this->forward_signature = $Message['forward_signature'];
		$this->forward_sender_chat = $Message['forward_sender_chat'];
		$this->is_automatic_forward	= $Message['is_automatic_forward'];
		$this->forward_date = $Message['forward_date'];
		$this->is_topic_message = $Message['is_topic_message'];
		$this->is_automatic_forward = $Message['is_automatic_forward'];
		$this->reply_to_message = $Message['reply_to_message'];
		$this->via_bot = $Message['via_bot'];
		$this->edit_date = $Message['edit_date'];
		$this->has_protected_content = $Message['has_protected_content'];
		$this->media_group_id = $Message['media_group_id'];
		$this->author_signature = $Message['author_signature'];
		$this->text = $Message['text'];
		if (empty($this->configs['commands_alias'])) $this->configs['commands_alias'] = ['/'];
		if (in_array($this->text[0], $this->configs['commands_alias'])) {
			if (strpos($this->text, '@')) {
				$this->command = substr(explode('@', $this->text, 2)[0], 1);
				if (strpos($this->text, ' ')) {
					$this->command .= ' ' . explode(' ', $this->text, 2)[1];
				}
			} else {
				$this->command = substr($this->text, 1);
			}
		}
		$this->entities = $Message['entities'];
		$this->varAnimation($Message['animation']);
		$this->varAudio($Message['audio']);
		$this->varDocument($Message['document']);
		$this->varPhoto($Message['photo']);
		$this->varSticker($Message['sticker']);
		$this->varStory($Message['story']);
		$this->varVideo($Message['video']);
		$this->varVideoNote($Message['video_note']);
		$this->varVoice($Message['voice']);
		$this->caption = $Message['caption'];
		$this->caption_entities = $Message['caption_entities'];
		$this->has_media_spoiler = $Message['has_media_spoiler'];
		$this->varContact($Message['contact']);
		$this->varDice($Message['dice']);
		$this->varGame($Message['game']);
		$this->varPoll($Message['poll']);
		$this->varVenue($Message['venue']);
		$this->varLocation($Message['location']);
		# Comment these 2 lines if you have enabled the arrival of chat_member updates
		# $this->varNewMembers($Message['new_chat_members']);
		# $this->varLeftMember($Message['left_chat_members']);
		$this->new_chat_title = $Message['new_chat_title'];
		$this->new_chat_photo = $Message['new_chat_photo'];
		$this->delete_chat_photo = $Message['delete_chat_photo'];
		$this->group_chat_created = $Message['group_chat_created'];
		$this->supergroup_chat_created = $Message['supergroup_chat_created'];
		$this->channel_chat_created = $Message['channel_chat_created'];
		$this->varMessageAutoDeleteTimerChanged($Message['message_auto_delete_timer_changed']);
		$this->migrate_to_chat_id = $Message['migrate_to_chat_id'];
		$this->migrate_from_chat_id = $Message['migrate_from_chat_id'];
		$this->pinned_message = $Message['pinned_message'];
		$this->invoice = $Message['invoice'];
		$this->successful_payment = $Message['successful_payment'];
		$this->varUserShared($Message['user_shared']);
		$this->varChatShared($Message['chat_shared']);
		$this->connected_website = $Message['connected_website'];
		$this->varWriteAccessAllowed($Message['write_access_allowed']);
		$this->passport_data = $Message['passport_data'];
		$this->proximity_alert_triggered = $Message['proximity_alert_triggered'];
		$this->varForumTopicCreated($Message['forum_topic_created']);
		$this->varForumTopicEdited($Message['forum_topic_edited']);
		$this->varForumTopicClosed($Message['forum_topic_closed']);
		$this->varForumTopicReopened($Message['forum_topic_reopened']);
		$this->varGeneralForumTopicHidden($Message['general_forum_topic_hidden']);
		$this->varGeneralForumTopicUnhidden($Message['general_forum_topic_unhidden']);
		$this->varVideoChatScheduled($Message['video_chat_scheduled']);
		$this->varVideoChatStarted($Message['video_chat_started']);
		$this->video_chat_ended = $Message['video_chat_ended'];
		$this->video_chat_participants_invited = $Message['video_chat_participants_invited'];
		$this->varWebAppData($Message['web_app_data']);
		$this->varInlineKeyboardMarkup($Message['reply_markup']);
	}

	public function varNewMembers ($Members) {
		if (empty($Members)) return;
		foreach ($Members as $ID => $User) {
			$this->new_members[$ID]['user_id'] = $User['id'];
			$this->new_members[$ID]['is_bot'] = $User['is_bot'];
			$this->new_members[$ID]['first_name'] = $User['first_name'];
			$this->new_members[$ID]['last_name'] = $User['last_name'];
			$this->new_members[$ID]['username'] = $User['username'];
			$this->new_members[$ID]['language_code'] = $User['language_code'];
		}
	}

	public function varLeftMember ($User) {
		if (empty($User)) return;
		$this->left_user_id = $User['id'];
		$this->left_is_bot = $User['is_bot'];
		$this->left_first_name = $User['first_name'];
		$this->left_last_name = $User['last_name'];
		$this->left_username = $User['username'];
		$this->left_language = $User['language_code'];
	}

	public function varPhoto ($Photo) {
		if (!is_array($Photo) or empty($Photo)) return;
		foreach ($Photo as $PhotoSize) {
			$this->varPhotoSize($PhotoSize);
		}
	}

	public function varPhotoSize ($PhotoSize) {
		if (empty($PhotoSize)) return;
		$this->photo[] = [
			'photo_id'	=> $PhotoSize['file_id'],
			'photo_uid'	=> $PhotoSize['file_unique_id'],
			'width'		=> $PhotoSize['width'],
			'height'	=> $PhotoSize['height'],
			'size'		=> $PhotoSize['file_size']
		];
	}

	public function varAnimation ($Animation) {
		if (empty($Animation)) return;
		$this->animation_id = $Animation['file_id'];
		$this->animation_uid = $Animation['file_unique_id'];
		$this->animation_width = $Animation['width'];
		$this->animation_height = $Animation['height'];
		$this->animation_duration = $Animation['duration'];
		$this->animation_thumb = $Animation['thumb'];
		$this->animation_name = $Animation['file_name'];
		$this->animation_mime = $Animation['mime_type'];
		$this->animation_size = $Animation['file_size'];
	}

	public function varAudio ($Audio) {
		if (empty($Audio)) return;
		$this->audio_id = $Audio['file_id'];
		$this->audio_uid = $Audio['file_unique_id'];
		$this->audio_duration = $Audio['duration'];
		$this->audio_performer = $Audio['performer'];
		$this->audio_title = $Audio['title'];
		$this->audio_name = $Audio['file_name'];
		$this->audio_mime = $Audio['mime_type'];
		$this->audio_size = $Audio['file_size'];
		$this->audio_thumb = $Audio['thumb'];
	}

	public function varDocument ($Document) {
		if (empty($Document)) return;
		$this->document_id = $Document['file_id'];
		$this->document_uid = $Document['file_unique_id'];
		$this->document_thumb = $Document['thumb'];
		$this->document_name = $Document['file_name'];
		$this->document_mime = $Document['mime_type'];
		$this->document_size = $Document['file_size'];
	}

	public function varVideo ($Video) {
		if (empty($Video)) return;
		$this->video_id = $Video['file_id'];
		$this->video_uid = $Video['file_unique_id'];
		$this->video_width = $Video['width'];
		$this->video_height = $Video['height'];
		$this->video_duration = $Video['duration'];
		$this->video_thumb = $Video['thumb'];
		$this->video_name = $Video['file_name'];
		$this->video_mime = $Video['mime_type'];
		$this->video_size = $Video['file_size'];
	}

	public function varVideoNote ($VideoNote) {
		if (empty($VideoNote)) return;
		$this->video_note_id = $Video['file_id'];
		$this->video_note_uid = $Video['file_unique_id'];
		$this->video_note_length = $Video['length'];
		$this->video_note_duration = $Video['duration'];
		$this->video_note_thumb = $Video['thumb'];
		$this->video_note_size = $Video['file_size'];
	}

	public function varVoice ($Voice) {
		if (empty($Voice)) return;
		$this->voice_id = $Voice['file_id'];
		$this->voice_uid = $Voice['file_unique_id'];
		$this->voice_duration = $Voice['duration'];
		$this->voice_mime = $Voice['mime_type'];
		$this->voice_size = $Voice['file_size'];
	}

	public function varContact ($Contact) {
		if (empty($Contact)) return;
		$this->c_number = $Contact['phone_number'];
		$this->c_first_name = $Contact['first_name'];
		$this->c_last_name = $Contact['last_name'];
		$this->c_user_id = $Contact['user_id'];
		$this->c_vcard = $Contact['vcard'];
	}

	public function varDice ($Dice) {
		if (empty($Dice)) return;
		$this->dice_emoji = $Dice['emoji'];
		$this->dice_value = $Dice['value'];
	}

	public function varPollOption ($PollOption) {
		if (empty($PollOption)) return;
		$this->option_name = $PollOption['text'];
		$this->option_count = $PollOption['voter_count'];
	}

	public function varPollAnswer ($PollAnswer) {
		if (empty($PollAnswer)) return;
		$this->poll_id = $PollAnswer['text'];
		$this->varChat($PollAnswer['voter_chat']);
		$this->varUser($PollAnswer['user']);
		$this->poll_options = $PollAnswer['option_ids'];
	}

	public function varPoll ($Poll) {
		if (empty($Poll)) return;
		$this->poll_id = $Poll['id'];
		$this->poll_question = $Poll['question'];
		$this->poll_options = $Poll['options'];
		$this->poll_counter = $Poll['total_voter_counter'];
		$this->poll_closed = $Poll['is_closed'];
		$this->poll_anonymous = $Poll['is_anonymous'];
		$this->poll_type = $Poll['type'];
		$this->poll_multi = $Poll['allows_multiple_answers'];
		$this->poll_correct = $Poll['correct_option_id'];
		$this->poll_explanation = $Poll['explanation'];
		$this->poll_explanation_entities = $Poll['explanation_entities'];
		$this->poll_open = $Poll['open_period'];
		$this->poll_close_date = $Poll['close_date'];
	}

	public function varLocation ($Location) {
		if (empty($Location)) return;
		$this->loc_longitude = $Location['longitude'];
		$this->loc_latitude = $Location['latitude'];
		$this->loc_live_period = $Location['live_period'];
		$this->heading = $Location['heading'];
		$this->proximity_alert_radius = $Location['proximity_alert_radius'];
	}

	public function varVenue ($Venue) {
		if (empty($Venue)) return;
		$this->varLocation($Venue['location']);
		$this->venue_title = $Venue['title'];
		$this->venue_address = $Venue['address'];
		$this->foursquare_id = $Venue['foursquare_id'];
		$this->foursquare_type = $Venue['foursquare_type'];
		$this->google_place_id = $Venue['google_place_id'];
		$this->google_place_type = $Venue['google_place_type'];
	}

	public function varWebAppData ($WebAppData) {
		if (empty($WebAppData)) return;
		$this->webapp_data = $WebAppData['data'];
		$this->webapp_text = $WebAppData['button_text'];
	}

	public function varProximityAlertTriggered ($ProximityAlertTriggered) {
		if (empty($ProximityAlertTriggered)) return;
		$this->proximity_traveler = $ProximityAlertTriggered['traveler'];
		$this->proximity_watcher = $ProximityAlertTriggered['watcher'];
		$this->proximity_distance = $ProximityAlertTriggered['distance'];
	}

	public function varMessageAutoDeleteTimerChanged ($MessageAutoDeleteTimerChanged) {
		if (empty($MessageAutoDeleteTimerChanged)) return;
		$this->message_auto_delete_time = $MessageAutoDeleteTimerChanged['message_auto_delete_time'];
	}

	public function varForumTopicCreated ($ForumTopicCreated) {
		if (empty($ForumTopicCreated)) return;
		$this->forum_topic_created = true;
		$this->forum_topic_name = $ForumTopicCreated['name'];
		$this->forum_topic_icon = $ForumTopicCreated['icon_color'];
		$this->forum_topic_emoji_id = $ForumTopicCreated['icon_custom_emoji_id'];
	}

	public function varForumTopicClosed ($ForumTopicClosed) {
		if (!is_null($ForumTopicClosed)) return;
		$this->forum_topic_closed = true;
	}

	public function varForumTopicEdited ($ForumTopicEdited) {
		if (empty($ForumTopicEdited)) return;
		$this->forum_topic_edited = true;
		$this->forum_topic_name = $ForumTopicEdited['name'];
		$this->forum_topic_emoji_id = $ForumTopicEdited['icon_custom_emoji_id'];
	}

	public function varForumTopicReopened ($ForumTopicReopened) {
		if (!is_null($ForumTopicReopened)) return;
		$this->forum_topic_reopened = true;
	}

	public function varGeneralForumTopicHidden ($GeneralForumTopicHidden) {
		if (!is_null($GeneralForumTopicHidden)) return;
		$this->forum_topic_hidden = true;
	}


	public function varGeneralForumTopicUnhidden ($GeneralForumTopicUnhidden) {
		if (!is_null($GeneralForumTopicUnhidden)) return;
		$this->forum_topic_unhidden = true;
	}

	public function varUserShared ($UserShared) {
		if (!is_null($UserShared)) return;
		$this->ushared_request_id = $UserShared['request_id'];
		$this->shared_user_id = $UserShared['user_id'];
	}

	public function varChatShared ($ChatShared) {
		if (!is_null($ChatShared)) return;
		$this->cshared_request_id = $ChatShared['request_id'];
		$this->shared_chat_id = $ChatShared['chat_id'];
	}

	public function varWriteAccessAllowed () {
		if (!is_null($WriteAccessAllowed)) return;
		$this->write_access_allowed = $WriteAccessAllowed['web_app_name'];
	}

	public function varVideoChatScheduled ($VideoChatScheduled) {
		if (empty($VideoChatScheduled)) return;
		$this->video_chat_sceduled = $VideoChatScheduled['start_date'];
	}

	public function varVideoChatStarted ($VideoChatStarted) {
		if (!is_null($VideoChatStarted)) return;
		$this->video_chat_started = true;
	}

	public function varVideoChatEnded ($VideoChatEnded) {
		if (empty($VideoChatEnded)) return;
		$this->video_chat_ended = $VideoChatEnded['duration'];
	}

	public function varVideoChatParticipantsInvited ($VideoChatParticipantsInvited) {
		if (empty($VideoChatParticipantsInvited)) return;
		$this->voice_chat_invited = $VideoChatParticipantsInvited['users'];
	}

	public function varUserProfilePhotos ($UserProfilePhotos) {
		if (empty($UserProfilePhotos)) return;
		$this->user_photos_count = $UserProfilePhotos['total_count'];
		$this->user_photos = $UserProfilePhotos['photos'];
	}

	public function varFile ($File) {
		if (empty($File)) return;
		$this->file_id = $File['file_id'];
		$this->file_uid = $File['file_unique_id'];
		$this->file_mime = $File['mime_type'];
		$this->file_size = $File['file_size'];
		$this->file_path = $File['file_path'];
	}

	public function varReplyKeyboardMarkup ($ReplyKeyboardMarkup) {
		if (empty($ReplyKeyboardMarkup)) return;
		$this->keyboard = $ReplyKeyboardMarkup['keyboard'];
		$this->resize_keyboard = $ReplyKeyboardMarkup['resize_keyboard'];
		$this->one_time_keyboard = $ReplyKeyboardMarkup['one_time_keyboard'];
		$this->selective_keyboard = $ReplyKeyboardMarkup['selective'];
	}

	public function varKeyboardButton ($KeyboardButton) {
		if (empty($KeyboardButton)) return;
		$this->buttons[] = [
			'text'				=> $KeyboardButton['text'],
			'request_chat'		=> $KeyboardButton['request_chat'],
			'request_user'		=> $KeyboardButton['request_user'],
			'request_contact'	=> $KeyboardButton['request_contact'],
			'request_location'	=> $KeyboardButton['request_location'],
			'request_poll'		=> $KeyboardButton['request_poll'],
			'web_app'			=> $KeyboardButton['web_app']
		];
	}

	public function varKeyboardButtonPollType ($KeyboardButtonPollType) {
		if (empty($KeyboardButtonPollType)) return;
		$this->keyboard_poll_type = $KeyboardButtonPollType['type'];
	}

	public function varReplyKeyboardRemove ($ReplyKeyboardRemove) {
		if (empty($ReplyKeyboardRemove)) return;
		$this->remove_keyboard = $ReplyKeyboardRemove['remove_keyboard'];
		$this->selective_keyboard = $ReplyKeyboardRemove['selective'];
	}

	public function varInlineKeyboardMarkup($InlineKeyboardMarkup) {
		if (empty($InlineKeyboardMarkup)) return;
		$this->inline_keyboard = $InlineKeyboardMarkup['inline_keyboard'];
	}
	
	public function varInlineKeyboardButton ($InlineKeyboardButton) {
		if (empty($InlineKeyboardButton)) return;
		$this->inline_buttons[] = [
			'text'								=> $InlineKeyboardButton['text'],
			'url'								=> $InlineKeyboardButton['url'],
			'login_url'							=> $InlineKeyboardButton['login_url'],
			'callback_data'						=> $InlineKeyboardButton['callback_data'],
			'web_app'							=> $InlineKeyboardButton['web_app'],
			'switch_inline_query'				=> $InlineKeyboardButton['switch_inline_query'],
			'switch_inline_query_current_chat'	=> $InlineKeyboardButton['switch_inline_query_current_chat'],
			'callback_game'						=> $InlineKeyboardButton['callback_game'],
			'pay'								=> $InlineKeyboardButton['pay']
		];
	}

	public function varLoginUrl ($LoginUrl) {
		if (empty($LoginUrl)) return;
		$this->login_url = $LoginUrl['url'];
		$this->login_forward_text = $LoginUrl['forward_text'];
		$this->login_bot_username = $LoginUrl['bot_username'];
		$this->login_request_write = $LoginUrl['request_write_access'];
	}
	
	public function varCallbackQuery ($CallbackQuery) {
		if (empty($CallbackQuery)) return;
		$this->query_id = $CallbackQuery['id'];
		$this->varMessage($CallbackQuery['message']);
		$this->varUser($CallbackQuery['from']);
		$this->inline_message_id = $CallbackQuery['inline_message_id'];
		$this->chat_instance = $CallbackQuery['chat_instance'];
		$this->query_data = $CallbackQuery['data'];
		$this->game_short_name = $CallbackQuery['game_short_name'];
	}

	public function varForceReply ($ForceReply) {
		if (empty($ForceReply)) return;
		$this->force_reply = $ForceReply['force_reply'];
		$this->selective_keyboard = $ForceReply['selective'];
	}

	public function varChatPhoto ($ChatPhoto) {
		if (empty($ChatPhoto)) return;
		$this->small_photo_id = $ChatPhoto['small_file_id'];
		$this->small_photo_uid = $ChatPhoto['small_file_unique_id'];
		$this->photo_id = $ChatPhoto['big_file_id'];
		$this->photo_uid = $ChatPhoto['big_file_unique_id'];
	}

	public function varChatInviteLink ($ChatInviteLink) {
		if (empty($ChatInviteLink)) return;
		$this->invite_link = $ChatInviteLink;
	}

	public function varChatAdministratorRights ($ChatAdministratorRights) {
		if (empty($ChatAdministratorRights)) return;
		$this->administrator_rights = $ChatAdministratorRights;
	}

	public function varChatMember ($ChatMember) {
		if (empty($ChatMember)) return;
		$this->chat_member = $ChatMember;
	}

	public function varChatMemberUpdated ($ChatMemberUpdated) {
		if (empty($ChatMemberUpdated)) return;
		$this->varUser($ChatMemberUpdated['from']);
		$this->varChat($ChatMemberUpdated['chat']);
		$this->date = $ChatMemberUpdated['date'];
		$this->varChatMember($ChatMemberUpdated['new_chat_member']);
		$this->invite_link = $ChatMemberUpdated['invite_link'];
		$this->via_chat_folder_invite_link = $ChatMemberUpdated['via_chat_folder_invite_link'];
	}

	public function varChatJoinRequest ($ChatJoinRequest) {
		if (empty($ChatJoinRequest)) return;
		$this->varChat($ChatJoinRequest['chat']);
		$this->varUser($ChatJoinRequest['from']);
		$this->user_chat_id = $ChatJoinRequest['user_chat_id'];
		$this->date = $ChatJoinRequest['date'];
		$this->user_bio = $ChatJoinRequest['bio'];
		$this->invite_link = $ChatJoinRequest['invite_link'];
	}

	public function varSticker ($Sticker) {
		if (empty($Sticker)) return;
		$this->sticker_id = $Sticker['file_id'];
		$this->sticker_uid = $Sticker['file_unique_id'];
		$this->sticker_type = $Sticker['type'];
		$this->sticker_witdh = $Sticker['width'];
		$this->sticker_height = $Sticker['height'];
		$this->sticker_animated = $Sticker['is_animated'];
		$this->sticker_video = $Sticker['is_video'];
		$this->sticker_thumb = $Sticker['thumb'];
		$this->sticker_emoji = $Sticker['emoji'];
		$this->sticker_set = $Sticker['set_name'];
		$this->sticker_premium = $Sticker['premium_animation'];
		$this->sticker_mask = $Sticker['mask_position'];
		$this->sticker_custom_emoji_id = $Sticker['custom_emoji_id'];
		$this->sticker_needs_repainting = $Sticker['needs_repainting'];
		$this->sticker_size = $Sticker['file_size'];
	}
	
	public function varStory ($Story) {
		if (empty($Story)) return;
	}
	
	public function varStickerSet ($StickerSet) {
		if (empty($StickerSet)) return;
		$this->stickers = $StickerSet['name'];
		$this->stickers_name = $StickerSet['title'];
		$this->stickers_type = $StickerSet['sticker_type'];
		$this->stickers_animated = $StickerSet['is_animated'];
		$this->stickers_video = $StickerSet['is_video'];
		$this->stickers_thumb = $StickerSet['thumb'];
	}

	public function varInlineQuery ($InlineQuery) {
		if (empty($InlineQuery)) return;
		$this->id = $InlineQuery['id'];
		$this->varUser($InlineQuery['from']);
		$this->location = $InlineQuery['location'];
		$this->query = $InlineQuery['query'];
		$this->offset = $InlineQuery['offset'];
	}

	public function varChosenInlineResult ($ChosenInlineResult) {
		if (empty($ChosenInlineResult)) return;
		$this->id = $ChosenInlineResult['result_id'];
		$this->varUser($ChosenInlineResult['from']);
		$this->location = $ChosenInlineResult['location'];
		$this->message_id = $ChosenInlineResult['inline_message_id'];
		$this->query = $ChosenInlineResult['query'];
	}

	public function varGame ($Game) {
		if (empty($Game)) return;
		$this->game_name = $Game['title'];
		$this->game_description = $Game['description'];
		$this->varPhoto($Game['photo']);
		$this->text = $Game['text'];
		$this->entities = $Game['text_entities'];
		$this->animation = varAnimation($Game['animation']);
	}

	# Get variables
	
	public function getUser() {
		if (!isset($this->user_id)) return;
		return [
			'id'			=> $this->user_id,
			'is_bot'		=> $this->user_is_bot,
			'first_name'	=> $this->user_first_name,
			'last_name'		=> $this->user_last_name,
			'username'		=> $this->user_username,
			'language_code'	=> $this->user_language_code
		];
	}
	
	public function getGroup() {
		if (!isset($this->chat_type) or !in_array($this->chat_type, ['group', 'supergroup'])) return;
		return [
			'id'			=> $this->chat_id,
			'title'			=> $this->chat_title,
			'username'		=> $this->chat_username,
			'language_code'	=> $this->user_language_code
		];
	}
	
	public function getChannel() {
		if (!isset($this->chat_type) or $this->chat_type !== 'channel') return;
		return [
			'id'			=> $this->chat_id,
			'title'			=> $this->chat_title,
			'username'		=> $this->chat_username,
			'language_code'	=> $this->user_language_code
		];
	}

	public function getGroupsPerms () {
		return [
			'can_be_edited',
			'is_anonymous',
			'can_manage_chat',
			'can_change_info',
			'can_delete_messages',
			'can_manage_video_chats',
			'can_invite_users',
			'can_restrict_members',
			'can_pin_messages',
			'can_manage_topics',
			'can_promote_members'
		];
	}
	
	public function getChannelsPerms () {
		return [
			'can_be_edited',
			'is_anonymous',
			'can_manage_chat',
			'can_post_messages',
			'can_edit_messages',
			'can_delete_messages',
			'can_restrict_members',
			'can_promote_members',
			'can_change_info',
			'can_post_stories',
			'can_edit_stories',
			'can_delete_stories'
		];
	}
}

?>