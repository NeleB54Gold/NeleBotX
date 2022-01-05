<?php

# Commands for Bot administrators
if ($v->chat_type == 'private' and $v->isAdmin()) {
	if ($v->command == 'setup') {
		if ($db) {
			$r = $db->setup();
			$t = $bot->bold('Result: ') . $bot->code(json_encode($r, JSON_PRETTY_PRINT), true);
		} else {
			$t = 'No database configured in config.php';
		}
		$bot->sendMessage($v->chat_id, $t);
		die;
	} elseif ($v->command == 'broadcast' or strpos($v->query_data, 'broadcast') === 0) {
		if ($configs['redis']['status']) {
			if ($rget = $db->rget('NBXBC-' . $v->user_id)) {
				$settings = json_decode($rget, 1);
			} else {
				$settings = [
					'tables'					=> [
						'users'		=> 1,
						'groups'	=> 1,
						'channels'	=> 1
					],
					'disable_notification'		=>	0,
					'disable_web_page_preview'	=>	0
				];
			}
			$reverse = [1, 0];
			$notifemoji = ['🔔', '🔕'];
			$webpremoji = ['🖇', '❌'];
			$databemoji = ['❌', '📨'];
			if (isset($v->query_data) and strpos($v->query_data, 'broadcast-') === 0) {
				$e = explode('-', $v->query_data);
				if ($e[1] == 1) {
					$table = array_keys($settings['tables'])[$e[2]];
					$settings['tables'][$table] = $reverse[round($settings['tables'][$table])];
				} elseif ($e[1] == 2) {
					$settings['disable_notification'] = $reverse[round($settings['disable_notification'])];
				} elseif ($e[1] == 3) {
					$settings['disable_web_page_preview'] = $reverse[round($settings['disable_web_page_preview'])];
				}
			}
			$db->rset('NBXBC-' . $v->user_id, json_encode($settings), (60 * 5));
			if (!$t) {
				$notification = '';
				$t = $bot->bold('📨 Broadcast') . PHP_EOL . $bot->italic('Send your message to broadcast on the bot...');
				$buttons = [
					[$bot->createInlineButton($databemoji[round($settings['tables']['users'])] . ' Users', 'broadcast-1-0'), $bot->createInlineButton($databemoji[round($settings['tables']['groups'])] . ' Groups', 'broadcast-1-1'), $bot->createInlineButton($databemoji[round($settings['tables']['channels'])] . ' Channels', 'broadcast-1-2')],
					[$bot->createInlineButton($notifemoji[round($settings['disable_notification'])] . ' Notification sound', 'broadcast-2'), $bot->createInlineButton($webpremoji[round($settings['disable_web_page_preview'])] . ' Link Preview', 'broadcast-3')],
					[$bot->createInlineButton('❎ Close', 'management-1')]
				];
			}
		} else {
			$t = 'Broadcast is available with redis only!';
		}
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons);
			$bot->answerCBQ($v->query_id, '', false);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons);
			$bot->deleteMessage($v->chat_id, $v->message_id);
		}
		die;
	} elseif ($rget = $db->rget('NBXBC-' . $v->user_id) and !$v->query_data and !$v->command) {
		$settings = json_decode($rget, 1);
		$bot->editConfigs('response', 1);
		$bot->editConfigs('disable_notification', $settings['disable_notification']);
		$bot->editConfigs('disable_web_page_preview', $settings['disable_web_page_preview']);
		if ($v->text) {
			$m = $bot->sendMessage($v->chat_id, $v->text, $v->update['message']['reply_markup']['inline_keyboard'], $v->entities);
		} else {
			$m = $bot->copyMessage($v->chat_id, $v->chat_id, $v->message_id, 0, $v->update['message']['reply_markup']['inline_keyboard']);
		}
		if ($m['ok']) {
			$bm = $bot->sendMessage($v->chat_id, 'Checking databases...');
			$chats = [];
			foreach ($settings['tables'] as $table => $status) {
				if ($status) {
					$fromt = $db->query('SELECT id FROM ' . $table, [], 2);
					if ($fromt[0]['id']) $chats = array_merge($chats, $fromt);
				}
			}
			$bot->editConfigs('response', 0);
			fastcgi_finish_request();
			if (empty($chats)) {
				$bot->editText($v->chat_id, $bm['result']['message_id'], 'There is no chat for broadcast!');
			} else {
				$db->rdel('NBXBC-' . $v->user_id);
				$bot->editText($v->chat_id, $bm['result']['message_id'], 'Forwarded to ' . 0 . '/' . round(count($chats)) . ' chats...');
				$start_time = time();
				$xtime = $start_time + 2;
				foreach ($chats as $chat) {
					if ($v->text) {
						$bot->sendMessage($chat['id'], $v->text, $v->update['message']['reply_markup']['inline_keyboard'], $v->entities);
					} else {
						$bot->copyMessage($chat['id'], $v->chat_id, $v->message_id, 0, $v->update['message']['reply_markup']['inline_keyboard']);
					}
					$chatcount += 1;
					if ($xtime <= time()) {
						$bot->editText($v->chat_id, $bm['result']['message_id'], 'Forwarded to ' . round($chatcount) . '/' . round(count($chats)) . ' chats...');
						$xtime = time() + 5;
					}
				}
				$total_time = time() - $start_time;
				sleep(1);
				if ($total_time < 60) {
					$time_total = round($total_time) . ' seconds';
				} else {
					$minutes = explode('.', round($total_time / 60, 1))[0];
					$seconds = $total_time - ($minutes * 60);
					$time_total = $minutes . ' minutes and ' . $seconds . ' seconds';
				}
				$bot->editText($v->chat_id, $bm['result']['message_id'], 'Forwarded to ' . round($chatcount) . ' chats in ' . $time_total . '!');
			}
		} else {
			$bot->sendMessage($v->chat_id, 'Telegram Error: ' . $bot->code($m['description'], 1));
		}
		die;
	} elseif ($v->command == 'management' or strpos($v->query_data, 'management') === 0) {
		if (isset($v->query_data) and strpos($v->query_data, 'management-') === 0) {
			$e = explode('-', $v->query_data);
			if ($e[1] == 1) {
				# Close management panel
				if ($configs['redis']['status'] and $db->rget('NBXBC-' . $v->user_id)) $db->rdel('NBXBC-' . $v->user_id);
				$bot->deleteMessage($v->chat_id, $v->message_id);
				$bot->answerCBQ($v->query_id);
				die;
			} elseif ($e[1] == 2 and isset($e[2])) {
				# Databases info
				$table = $db->tables[$e[2]];
				$e[1] = 3;
				if ($table == 'users') {
					$tuser = $db->query('SELECT id, name, surname, username, lang, status, ban FROM users WHERE id = ? LIMIT 1', [$e[3]], 1);
					if (!isset($tuser['id'])) {
						$bot->answerCBQ($v->query_id, '😔 User not found...');
						die;
					}
					if (!$tuser['name']) $tuser['name'] = 'Deactived user';
					if (!$tuser['surname']) $tuser['surname'] = '❌';
					if (!$tuser['username']) {
						$tuser['username'] = '❌';
					} else {
						$tuser['username'] = '@' . $tuser['username'];
					}
					if (!$tuser['lang']) $tuser['lang'] = 'en';
					$tuser['status'][0] = strtoupper($tuser['status'][0]);
					if ($tuser['ban']) {
						if ($e[4] == 'unban') {
							$db->unban($tuser['id']);
							$tuser['ban'] = '✅ Not banned';
							if (!$v->isAdmin($tuser['id'])) $febuttons[] = $bot->createInlineButton('🚫 Ban', 'management-2-' . $e[2] . '-' . $e[3] . '-ban');
						} else {
							$tuser['ban'] = '🚫 Banned';
							$febuttons[] = $bot->createInlineButton('✅ Unban', 'management-2-' . $e[2] . '-' . $e[3] . '-unban');
						}
					} else {
						if ($e[4] == 'ban') {
							if ($v->isAdmin($tuser['id'])) {
								$bot->answerCBQ($v->query_id, 'You can\'t ban a Bot administrator');
								$tuser['ban'] = '✅ Not banned';
							} else {
								$db->ban($tuser['id']);
								$tuser['ban'] = '🚫 Banned';
								$febuttons[] = $bot->createInlineButton('✅ Unban', 'management-2-' . $e[2] . '-' . $e[3] . '-unban');
							}
						} else {
							$tuser['ban'] = '✅ Not banned';
							if (!$v->isAdmin($tuser['id'])) {
								$febuttons[] = $bot->createInlineButton('🚫 Ban', 'management-2-' . $e[2] . '-' . $e[3] . '-ban');
							}
						}
					}
					$formenu = 2;
					$mcount = 0;
					foreach ($febuttons as $febutton) {
						if (isset($buttons[$mcount]) and count($buttons[$mcount]) >= $formenu) $mcount += 1;
						$buttons[$mcount][] = $febutton;
					}
					$t = $bot->bold('ℹ️ User Management') . PHP_EOL . 'Name: ' . $bot->specialchars($tuser['name']) . PHP_EOL . 'Surname: ' . $bot->specialchars($tuser['surname']) . PHP_EOL . 'ID: ' . $tuser['id'] . PHP_EOL . 'Username: ' . $tuser['username'] . PHP_EOL . 'Language: ' . $tuser['lang'] . PHP_EOL . 'Status: ' . $tuser['status'] . PHP_EOL . 'Ban status: ' . $tuser['ban'];
				} elseif ($table == 'groups') {
					if ($e[4] == 'admins') {
						$tchat = $db->query('SELECT id, title, admins FROM groups WHERE id = ? or id = ? LIMIT 1', ['-100' . $e[3], '-' . $e[3]], 1);
						if (!isset($tchat['id'])) {
							$bot->answerCBQ($v->query_id, '😔 Chat not found...');
							die;
						}
						$list = '';
						$e[1] = 2;
						$e[2] .= '-' . $e[3];
						$tchat['admins'] = json_decode($tchat['admins'], 1);
						if (!is_array($tchat['admins']) or empty($tchat['admins'])) {
							$list = PHP_EOL . $bot->italic('Uohh... uuuoh.. upsi!' . PHP_EOL . 'This list is empty, it\'s the sound of nothing :D');
						} else {
							foreach ($tchat['admins'] as $admin) {
								if ($admin['status'] == 'creator' and !isset($e[5])) {
									$febuttons[] = $bot->createInlineButton('👑 ' . $admin['user']['first_name'], 'management-2-' . str_replace('-' . $e[3], '', $e[2]) . '-' . $e[3] . '-admins-' . $admin['user']['id']);
									if ($admin['user']['username']) $admin['user']['first_name'] = $bot->text_link($admin['user']['first_name'], 'https://t.me/' . $admin['user']['username']);
									$list .= PHP_EOL . '👑 ' . $admin['user']['first_name'] . ' [' . $bot->code($admin['user']['id']) . ']';
								}
								$cadmins[$admin['user']['id']] = $admin;
							}
							if ($e[5]) {
								$e[2] .= '-' . $e[4];
								$admin = $cadmins[$e[5]];
								if (isset($admin['custom_title'])) $ctitle = $bot->italic(' (' . $admin['custom_title'] . ')');
								$emoji = ['❌', '✅'];
								foreach ($v->getGroupsPerms() as $perm) {
									if (isset($admin[$perm]) or $admin['status'] !== 'creator') {
										$bool = round($admin[$perm]);
									} else {
										$bool = 1;
									}
									$perm[0] = strtoupper($perm[0]);
									$perms .= PHP_EOL . str_replace('_', ' ', $perm) . ': ' . $emoji[$bool];
								}
								$adtype = ['User', 'Bot'];
								$list = PHP_EOL . $bot->bold($adtype[round($admin['user']['is_bot'])] . ': ') . $bot->tag($admin['user']['id'], $admin['user']['first_name']) . $ctitle . PHP_EOL . $bot->code($admin['user']['id']) . PHP_EOL . $perms;
							} else {
								$num = 0;
								foreach ($cadmins as $admin) {
									if ($admin['status'] !== 'creator') {
										$num += 1;
										$list .= PHP_EOL . $num . '️⃣ ' . $bot->tag($admin['user']['id'], $admin['user']['first_name']) . ' [' . $bot->code($admin['user']['id']) . ']';
										$febuttons[] = $bot->createInlineButton($num . '️⃣ ' . $admin['user']['first_name'], 'management-2-' . str_replace('-' . $e[3], '', $e[2]) . '-' . $e[3] . '-admins-' . $admin['user']['id']);
									}
								}
							}
						}
						$formenu = 2;
						$mcount = 0;
						foreach ($febuttons as $febutton) {
							if (isset($buttons[$mcount]) and count($buttons[$mcount]) >= $formenu) $mcount += 1;
							$buttons[$mcount][] = $febutton;
						}
						$t = $bot->bold('👮🏻‍♂️ Administrators List of ' . $tchat['title'], 1) . PHP_EOL . $list;
					} elseif ($e[4] == 'permissions') {
						$tchat = $db->query('SELECT id, title, permissions FROM groups WHERE id = ? or id = ? LIMIT 1', ['-100' . $e[3], '-' . $e[3]], 1);
						if (!isset($tchat['id'])) {
							$bot->answerCBQ($v->query_id, '😔 Chat not found...');
							die;
						}
						$list = '';
						$e[1] = 2;
						$e[2] = $e[2] . '-' . $e[3];
						$tchat['permissions'] = json_decode($tchat['permissions'], 1);
						if (!is_array($tchat['permissions']) or empty($tchat['permissions'])) {
							$list = PHP_EOL . $bot->italic('Uohh... uuuoh.. upsi!' . PHP_EOL . 'This list is empty, it\'s the sound of nothing :D');
						} else {
							$emoji = ['❌', '✅'];
							foreach ($tchat['permissions'] as $perm => $bool) {
								$perm[0] = strtoupper($perm[0]);
								$list .= PHP_EOL . str_replace('_', ' ', $perm) . ': ' . $emoji[round($bool)];
							}
						}
						$t = $bot->bold('💬 Global permissions of ' . $tchat['title'], 1) . $list;
					} else {
						$tchat = $db->query('SELECT id, title, description, username, status, ban FROM groups WHERE id = ? or id = ? LIMIT 1', ['-100' . $e[3], '-' . $e[3]], 1);
						if (!isset($tchat['id'])) {
							$bot->answerCBQ($v->query_id, '😔 Chat not found...');
							die;
						}
						if (!$tchat['username']) {
							$tchat['username'] = '❌';
						} else {
							$tchat['username'] = '@' . $tchat['username'];
						}
						if (!$tchat['description']) {
							$tchat['description'] = '❌';
						}
						$tchat['status'][0] = strtoupper($tchat['status'][0]);
						$febuttons[] = $bot->createInlineButton('👮🏻‍♂️ Administrators', 'management-2-' . $e[2] . '-' . $e[3] . '-admins');
						$febuttons[] = $bot->createInlineButton('💬 Permissions', 'management-2-' . $e[2] . '-' . $e[3] . '-permissions');
						if ($tchat['ban']) {
							if ($e[4] == 'unban') {
								$db->unban($tchat['id']);
								$tchat['ban'] = '✅ Not banned';
								$febuttons[] = $bot->createInlineButton('🚫 Ban', 'management-2-' . $e[2] . '-' . $e[3] . '-ban');
							} else {
								$tchat['ban'] = '🚫 Banned';
								$febuttons[] = $bot->createInlineButton('✅ Unban', 'management-2-' . $e[2] . '-' . $e[3] . '-unban');
							}
						} else {
							if ($e[4] == 'ban') {
								if ($v->isAdmin($tchat['id'])) {
									$bot->answerCBQ($v->query_id, 'You can\'t ban a Bot administrator');
									$tchat['ban'] = '✅ Not banned';
								} else {
									$ok = $db->ban($tchat['id']);
									$tchat['ban'] = '🚫 Banned';
									$febuttons[] = $bot->createInlineButton('✅ Unban', 'management-2-' . $e[2] . '-' . $e[3] . '-unban');
								}
							} else {
								$tchat['ban'] = '✅ Not banned';
								if (!$v->isAdmin($tchat['id'])) {
									$febuttons[] = $bot->createInlineButton('🚫 Ban', 'management-2-' . $e[2] . '-' . $e[3] . '-ban');
								}
							}
						}
						$formenu = 2;
						$mcount = 0;
						foreach ($febuttons as $febutton) {
							if (isset($buttons[$mcount]) and count($buttons[$mcount]) >= $formenu) $mcount += 1;
							$buttons[$mcount][] = $febutton;
						}
						$t = $bot->bold('ℹ️ Group Management') . PHP_EOL . 'Title: ' . $bot->specialchars($tchat['title']) . PHP_EOL . 'ID: ' . $bot->code($tchat['id']) . PHP_EOL . 'Username: ' . $tchat['username'] . PHP_EOL . 'Description: ' . $bot->specialchars($tchat['description']) . PHP_EOL . 'Status: ' . $tchat['status'] . PHP_EOL . 'Ban staus: ' . $tchat['ban'];
					}
				} elseif ($table == 'channels') {
					if ($e[4] == 'admins') {
						$tchat = $db->query('SELECT id, title, admins FROM channels WHERE id = ? LIMIT 1', ['-100' . $e[3]], 1);
						if (!isset($tchat['id'])) {
							$bot->answerCBQ($v->query_id, '😔 Chat not found...');
							die;
						}
						$list = '';
						$e[1] = 2;
						$e[2] .= '-' . $e[3];
						$tchat['admins'] = json_decode($tchat['admins'], 1);
						if (!is_array($tchat['admins']) or empty($tchat['admins'])) {
							$list = PHP_EOL . $bot->italic('Uohh... uuuoh.. upsi!' . PHP_EOL . 'This list is empty, it\'s the sound of nothing :D');
						} else {
							foreach ($tchat['admins'] as $admin) {
								if ($admin['status'] == 'creator' and !isset($e[5])) {
									$febuttons[] = $bot->createInlineButton('👑 ' . $admin['user']['first_name'], 'management-2-' . str_replace('-' . $e[3], '', $e[2]) . '-' . $e[3] . '-admins-' . $admin['user']['id']);
									if ($admin['user']['username']) $admin['user']['first_name'] = $bot->text_link($admin['user']['first_name'], 'https://t.me/' . $admin['user']['username']);
									$list .= PHP_EOL . '👑 ' . $admin['user']['first_name'] . ' [' . $bot->code($admin['user']['id']) . ']';
								}
								$cadmins[$admin['user']['id']] = $admin;
							}
							if ($e[5]) {
								$e[2] .= '-' . $e[4];
								$admin = $cadmins[$e[5]];
								$emoji = ['❌', '✅'];
								foreach ($v->getChannelsPerms() as $perm) {
									if (isset($admin[$perm]) or $admin['status'] !== 'creator') {
										$bool = round($admin[$perm]);
									} else {
										$bool = 1;
									}
									$perm[0] = strtoupper($perm[0]);
									$perms .= PHP_EOL . str_replace('_', ' ', $perm) . ': ' . $emoji[$bool];
								}
								$adtype = ['User', 'Bot'];
								$list = PHP_EOL . $bot->bold($adtype[round($admin['user']['is_bot'])] . ': ') . $bot->tag($admin['user']['id'], $admin['user']['first_name']) . PHP_EOL . $bot->code($admin['user']['id']) . PHP_EOL . $perms;
							} else {
								$num = 0;
								foreach ($cadmins as $admin) {
									if ($admin['status'] !== 'creator') {
										$num += 1;
										$list .= PHP_EOL . $num . '️⃣ ' . $bot->tag($admin['user']['id'], $admin['user']['first_name']) . ' [' . $bot->code($admin['user']['id']) . ']';
										$febuttons[] = $bot->createInlineButton($num . '️⃣ ' . $admin['user']['first_name'], 'management-2-' . str_replace('-' . $e[3], '', $e[2]) . '-' . $e[3] . '-admins-' . $admin['user']['id']);
									}
								}
							}
						}
						$formenu = 2;
						$mcount = 0;
						foreach ($febuttons as $febutton) {
							if (isset($buttons[$mcount]) and count($buttons[$mcount]) >= $formenu) $mcount += 1;
							$buttons[$mcount][] = $febutton;
						}
						$t = $bot->bold('👮🏻‍♂️ Administrators List of ' . $tchat['title'], 1) . PHP_EOL . $list;
					} else {
						$tchat = $db->query('SELECT id, title, description, username, status, ban FROM channels WHERE id = ? LIMIT 1', ['-100' . $e[3]], 1);
						if (!isset($tchat['id'])) {
							$bot->answerCBQ($v->query_id, '😔 Chat not found...');
							die;
						}
						if (!$tchat['username']) {
							$tchat['username'] = '❌';
						} else {
							$tchat['username'] = '@' . $tchat['username'];
						}
						if (!$tchat['description']) {
							$tchat['description'] = '❌';
						}
						$tchat['status'][0] = strtoupper($tchat['status'][0]);
						$febuttons[] = $bot->createInlineButton('👮🏻‍♂️ Administrators', 'management-2-' . $e[2] . '-' . $e[3] . '-admins');
						if ($tchat['ban']) {
							if ($e[4] == 'unban') {
								$db->unban($tchat['id']);
								$tchat['ban'] = '✅ Not banned';
								$febuttons[] = $bot->createInlineButton('🚫 Ban', 'management-2-' . $e[2] . '-' . $e[3] . '-ban');
							} else {
								$tchat['ban'] = '🚫 Banned';
								$febuttons[] = $bot->createInlineButton('✅ Unban', 'management-2-' . $e[2] . '-' . $e[3] . '-unban');
							}
						} else {
							if ($e[4] == 'ban') {
								if ($v->isAdmin($tchat['id'])) {
									$bot->answerCBQ($v->query_id, 'You can\'t ban a Bot administrator');
									$tchat['ban'] = '✅ Not banned';
								} else {
									$ok = $db->ban($tchat['id']);
									$tchat['ban'] = '🚫 Banned';
									$febuttons[] = $bot->createInlineButton('✅ Unban', 'management-2-' . $e[2] . '-' . $e[3] . '-unban');
								}
							} else {
								$tchat['ban'] = '✅ Not banned';
								if (!$v->isAdmin($tchat['id'])) {
									$febuttons[] = $bot->createInlineButton('🚫 Ban', 'management-2-' . $e[2] . '-' . $e[3] . '-ban');
								}
							}
						}
						$formenu = 2;
						$mcount = 0;
						foreach ($febuttons as $febutton) {
							if (isset($buttons[$mcount]) and count($buttons[$mcount]) >= $formenu) $mcount += 1;
							$buttons[$mcount][] = $febutton;
						}
						$t = $bot->bold('ℹ️ Channel Management') . PHP_EOL . 'Title: ' . $bot->specialchars($tchat['title']) . PHP_EOL . 'ID: ' . $bot->code($tchat['id']) . PHP_EOL . 'Username: ' . $tchat['username'] . PHP_EOL . 'Description: ' . $bot->specialchars($tchat['description']) . PHP_EOL . 'Status: ' . $tchat['status'] . PHP_EOL . 'Ban staus: ' . $tchat['ban'];
					}
				}
				$buttons[][] = $bot->createInlineButton('◀️ Back', 'management-' . $e[1] . '-' . $e[2]);
			} elseif ($e[1] == 3) {
				# Database explorer
				if (!$configs['database']['status']) {
					$bot->answerCBQ($v->query_id, '❎ No database configured!');
					die;
				}
				if (isset($e[2])) {
					$table = $db->tables[$e[2]];
					if ($table == 'users') {
						$t = $bot->bold('👤 Users') . PHP_EOL;
						if (isset($e[3]) and is_numeric($e[3])) {
							$page = round($e[3]);
							if ($page > 1) $prevpage = true;
						} else {
							$page = 1;
						}
						$users = $db->query('SELECT id, name, surname, username FROM users ORDER BY id DESC ' . $db->limit(6, (($page * 5) - 5)), 0, 2);
						if (!empty($users) and !isset($users['error'])) {
							if (isset($users[5])) {
								$nextpage = true;
								unset($users[5]);
							}
							$num = 0;
							foreach ($users as $tuser) {
								$num += 1;
								$emo = $num . '️⃣';
								$febuttons[] = $bot->createInlineButton($emo . ' ' . $tuser['name'], 'management-2-0-' . $tuser['id']);
								if ($tuser['username']) $tuser['name'] = $bot->text_link($tuser['name'], 'https://t.me/' . $tuser['username'], 1);
								$t .= PHP_EOL . $emo . ' ' . $tuser['name'] . ' [' . $bot->code($tuser['id']) . ']';
							}
							$formenu = 2;
							$mcount = 0;
							foreach ($febuttons as $febutton) {
								if (isset($buttons[$mcount]) and count($buttons[$mcount]) >= $formenu) $mcount += 1;
								$buttons[$mcount][] = $febutton;
							}
						} else {
							$t .= PHP_EOL . $bot->italic('Uohh... uuuoh.. upsi!' . PHP_EOL . 'This list is empty, it\' the sound of nothing :D');
						}
					} elseif ($table == 'groups') {
						$t = $bot->bold('👥 Groups') . PHP_EOL;
						if (isset($e[3]) and is_numeric($e[3])) {
							$page = round($e[3]);
							if ($page > 1) {
								$prevpage = true;
							}
						} else {
							$page = 1;
						}
						$groups = $db->query('SELECT id, title, username FROM groups ORDER BY id DESC ' . $db->limit(6, (($page * 5) - 5)), 0, 2);
						if (!empty($groups)) {
							if (isset($groups[5])) {
								$nextpage = true;
								unset($groups[5]);
							}
							$num = 0;
							foreach ($groups as $tchat) {
								$num += 1;
								$emo = $num . '️⃣';
								$febuttons[] = $bot->createInlineButton($emo . ' ' . $tchat['title'], 'management-2-1-' . str_replace('-', '', str_replace('-100', '', $tchat['id'])));
								if ($tchat['username']) $tchat['title'] = $bot->text_link($tchat['title'], 'https://t.me/' . $tchat['username'], 1);
								$t .= PHP_EOL  . $emo . ' ' . $tchat['title'] . ' [' . $bot->code($tchat['id']) . ']';
							}
							$formenu = 2;
							$mcount = 0;
							foreach ($febuttons as $febutton) {
								if (isset($buttons[$mcount]) and count($buttons[$mcount]) >= $formenu) $mcount += 1;
								$buttons[$mcount][] = $febutton;
							}
						} else {
							$t .= PHP_EOL . $bot->italic('Uohh... uuuoh.. upsi!' . PHP_EOL . 'This list is empty, it\' the sound of nothing :D');
						}
					} elseif ($table == 'channels') {
						$t = $bot->bold('📢 Channels') . PHP_EOL;
						if (isset($e[3]) and is_numeric($e[3])) {
							$page = round($e[3]);
							if ($page > 1) {
								$prevpage = true;
							}
						} else {
							$page = 1;
						}
						$channels = $db->query('SELECT id, title, username FROM channels ORDER BY id DESC ' . $db->limit(6, (($page * 5) - 5)), 0, 2);
						if (!empty($channels)) {
							if (isset($channels[5])) {
								$nextpage = true;
								unset($channels[5]);
							}
							$num = 0;
							foreach ($channels as $tchat) {
								$num += 1;
								$emo = $num . '️⃣';
								$febuttons[] = $bot->createInlineButton($emo . ' ' . $tchat['title'], 'management-2-2-' . str_replace('-100', '', $tchat['id']));
								if ($tchat['username']) $tchat['title'] = $bot->text_link($tchat['title'], 'https://t.me/' . $tchat['username'], 1);
								$t .= PHP_EOL  . $emo . ' ' . $tchat['title'] . ' [' . $bot->code($tchat['id']) . ']';
							}
							$formenu = 2;
							$mcount = 0;
							foreach ($febuttons as $febutton) {
								if (isset($buttons[$mcount]) and count($buttos[$mcount]) >= $formenu) $mcount += 1;
								$buttons[$mcount][] = $febutton;
							}
						} else {
							$t .= PHP_EOL . $bot->italic('Uohh... uuuoh.. upsi!' . PHP_EOL . 'This list is empty, it\' the sound of nothing :D');
						}
					} else {
						$bot->answerCBQ($v->query_id, '⚠️ Unknown database!', true);
						die;
					}
					$pager = [];
					if ($prevpage) $pager[] = $bot->createInlineButton('⬅️', 'management-3-' . $e[2] . '-' . round($page - 1));
					if ($nextpage) $pager[] = $bot->createInlineButton('➡️', 'management-3-' . $e[2] . '-' . round($page + 1));
					if (!empty($pager)) $buttons[] = $pager;
					$buttons[] = [
						$bot->createInlineButton('◀️ Back', 'management-3')
					];
				} else {
					$t = '🗄 Databases';
					$buttons = [
						[$bot->createInlineButton('👤 Users', 'management-3-0'), $bot->createInlineButton('👥 Groups', 'management-3-1')],
						[$bot->createInlineButton('📢 Channels', 'management-3-2')],
						[$bot->createInlineButton('◀️ Back', 'management'), $bot->createInlineButton('❎ Close', 'management-1')]
					];
				}
			} elseif ($e[1] == 4) {
				# Subscribers count
				if (!$configs['database']['status']) {
					$bot->answerCBQ($v->query_id, '❎ No database configured!');
					die;
				}
				$count = [
					'users' 	=> 1,
					'groups' 	=> 0,
					'channels'	=> 0
				];
				$cu = $db->query('SELECT COUNT(id) FROM users', [], 1);
				if (isset($cu['count'])) {
					$count['users'] = round($cu['count']);
				} elseif (isset($cu['COUNT(id)'])) {
					$count['users'] = round($cu['COUNT(id)']);
				}
				$cg = $db->query('SELECT COUNT(id) FROM groups', [], 1);
				if (isset($cg['count'])) {
					$count['groups'] = round($cg['count']);
				} elseif (isset($cg['COUNT(id)'])) {
					$count['groups'] = round($cg['COUNT(id)']);
				}
				$cc = $db->query('SELECT COUNT(id) FROM channels', [], 1);
				if (isset($cc['count'])) {
					$count['channels'] = round($cc['count']);
				} elseif (isset($cc['COUNT(id)'])) {
					$count['channels'] = round($cc['COUNT(id)']);
				}
				$activityButtons[] = $bot->createInlineButton('⏰ Activity', 'management-4-2');
				if ($e[2] == 1) {
					$cau = $db->query('SELECT COUNT(id) FROM users WHERE status = ?', ['started'], 1);
					if (isset($cau['count'])) {
						$count['a_users'] = round($cau['count']);
					} elseif (isset($cu['COUNT(id)'])) {
						$count['a_users'] = round($cau['COUNT(id)']);
					}
					$cag = $db->query('SELECT COUNT(id) FROM groups WHERE status = ?', ['active'], 1);
					if (isset($cag['count'])) {
						$count['a_groups'] = round($cag['count']);
					} elseif (isset($cag['COUNT(id)'])) {
						$count['a_groups'] = round($cag['COUNT(id)']);
					}
					$cac = $db->query('SELECT COUNT(id) FROM channels WHERE status = ?', ['active'], 1);
					if (isset($cac['count'])) {
						$count['a_channels'] = round($cac['count']);
					} elseif (isset($cac['COUNT(id)'])) {
						$count['a_channels'] = round($cac['COUNT(id)']);
					}
					$list = $bot->italic('Active chats') . PHP_EOL . PHP_EOL . $bot->bold('👤 Users: ') . $count['users'] . '/' . $count['a_users'] . PHP_EOL . $bot->bold('👥 Groups: ') . $count['groups'] . '/' . $count['a_groups'] . PHP_EOL . $bot->bold('📢 Channels: ') . $count['channels'] . '/' . $count['a_channels'];
				} elseif ($e[2] == 2) {
					if ($e[3] == 1) {
						$last = 60 * 60 * 24 * 365;
						$last_time = 'year';
					} elseif ($e[3] == 2) {
						$last = 60 * 60 * 24 * 30;
						$last_time = 'month';
					} elseif ($e[3] == 3) {
						$last = 60 * 60 * 24;
						$last_time = 'day';
					} else {
						$last = 60 * 60;
						$last_time = 'hour';
					}
					$time = time() - $last;
					$activityButtons = [
						$bot->createInlineButton('Hour', 'management-4-2'),
						$bot->createInlineButton('Day', 'management-4-2-3'),
						$bot->createInlineButton('Month', 'management-4-2-2'),
						$bot->createInlineButton('Year', 'management-4-2-1'),
					];
					$cau = $db->query('SELECT COUNT(id) FROM users WHERE last_seen > ?', [$time], 1);
					if (isset($cau['count'])) {
						$count['a_users'] = round($cau['count']);
					} elseif (isset($cu['COUNT(id)'])) {
						$count['a_users'] = round($cau['COUNT(id)']);
					}
					$cag = $db->query('SELECT COUNT(id) FROM groups WHERE last_seen > ?', [$time], 1);
					if (isset($cag['count'])) {
						$count['a_groups'] = round($cag['count']);
					} elseif (isset($cag['COUNT(id)'])) {
						$count['a_groups'] = round($cag['COUNT(id)']);
					}
					$cac = $db->query('SELECT COUNT(id) FROM channels WHERE last_seen > ?', [$time], 1);
					if (isset($cac['count'])) {
						$count['a_channels'] = round($cac['count']);
					} elseif (isset($cac['COUNT(id)'])) {
						$count['a_channels'] = round($cac['COUNT(id)']);
					}
					$list = $bot->italic('Activity of the last ' . $last_time) . PHP_EOL . PHP_EOL . $bot->bold('👤 Users: ') . $count['users'] . '/' . $count['a_users'] . PHP_EOL . $bot->bold('👥 Groups: ') . $count['groups'] . '/' . $count['a_groups'] . PHP_EOL . $bot->bold('📢 Channels: ') . $count['channels'] . '/' . $count['a_channels'];
				} else {
					$list = PHP_EOL . $bot->bold('👤 Users: ') . $count['users'] . PHP_EOL . $bot->bold('👥 Groups: ') . $count['groups'] . PHP_EOL . $bot->bold('📢 Channels: ') . $count['channels'];
				}
				$t = '🗄 Bot Subscribers' . PHP_EOL . $list;
				$buttons = [
					[$bot->createInlineButton('🔄 Update', $v->query_data)],
					[$bot->createInlineButton('✅ Active Subscribers', 'management-4-1')],
					[],
				];
				$buttons[] = $activityButtons;
				$buttons[] = [$bot->createInlineButton('◀️ Back', 'management'), $bot->createInlineButton('❎ Close', 'management-1')];
			} elseif ($e[1] == 5) {
				$total_space = disk_total_space('/');
				$free_space = disk_free_space('/');
				$value = 100 - ($free_space / $total_space * 100);
				$mem = round(memory_get_usage(true) / 1024 / 1024);
				if ($phpinfo['PHP Core']['memory_limit']) {
					$mem .= '/' . round($phpinfo['PHP Core']['memory_limit'] / 1024 / 1024);
				}
				$load = sys_getloadavg()[0] . '%';
				$t = $bot->bold('⌨️ Server Management') . PHP_EOL . 'Disk usage: ' . round($value) . '%' . PHP_EOL . 'Script memory: ' . $mem . ' MB' . PHP_EOL . 'System load avg: ' . $load;
				$buttons = [
					[$bot->createInlineButton('🔄 Update', $v->query_data)],
					[$bot->createInlineButton('◀️ Back', 'management'), $bot->createInlineButton('❎ Close', 'management-1')]
				];
			}
		}
		if (!$t) {
			$t = $bot->bold('⚙️ Management') . PHP_EOL . $bot->italic('Here you can manage your Bot X informations...');
			$buttons = [
				[$bot->createInlineButton('🗄 Databases', 'management-3'), $bot->createInlineButton('👥 Subscribers', 'management-4')],
				[$bot->createInlineButton('⌨️ Server', 'management-5')],
				[$bot->createInlineButton('❎ Close', 'management-1')]
			];
		}
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons);
			$bot->answerCBQ($v->query_id, '', false);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons);
			$bot->deleteMessage($v->chat_id, $v->message_id);
		}
		die;
	} elseif (strpos($v->command, 'ban') === 0) {
		if (strpos($v->command, 'ban ') === 0) {
			$e = explode(' ', $v->command, 2);
			if (is_numeric($e[1])) {
				$args = ['id' => $e[1]];
			} else {
				$args = ['username' => str_replace('@', '', $e[1])];
			}
			if ($tchat = $db->getUser($args) and isset($tchat['id'])) {
				
			} elseif ($tchat = $db->getGroup($args) and isset($tchat['id'])) {
				
			} elseif ($tchat = $db->getChannel($args) and isset($tchat['id'])) {
				
			}
			if ($tchat['id']) {
				$ban = $db->ban($e[1]);
				if ($name = $tchat['name']) {
					
				} elseif ($name = $tchat['title']) {
					
				} else {
					$name = 'Unknown';
				}
				$t = $bot->bold('I\'ve banned ' . $bot->italic($name) . ' from this Bot!');
			} else {
				$t = $bot->bold('❌ Chat not found!') . PHP_EOL . $bot->italic('You can try to find it by databases management...');
			}
		} else {
			$t = $bot->bold('❌ Please, insert a Telegram ID to ban a chat!') . PHP_EOL . 'Example: ' . $bot->code('/ban 12345678');
		}
		$bot->sendMessage($v->chat_id, $t);
		die;
	} elseif (strpos($v->command, 'unban') === 0) {
		if (strpos($v->command, 'unban ') === 0) {
			$e = explode(' ', $v->command, 2);
			if (is_numeric($e[1])) {
				$args = ['id' => $e[1]];
			} else {
				$args = ['username' => str_replace('@', '', $e[1])];
			}
			if ($tchat = $db->getUser($args) and isset($tchat['id'])) {
				
			} elseif ($tchat = $db->getGroup($args) and isset($tchat['id'])) {
				
			} elseif ($tchat = $db->getChannel($args) and isset($tchat['id'])) {
				
			}
			if ($tchat['id']) {
				$ban = $db->unban($e[1]);
				if ($name = $tchat['name']) {
					
				} elseif ($name = $tchat['title']) {
					
				} else {
					$name = 'Unknown';
				}
				$t = $bot->bold('I\'ve unbanned ' . $bot->italic($name) . ' from this Bot!');
			} else {
				$t = $bot->bold('❌ Chat not found!') . PHP_EOL . $bot->italic('You can try to find it by databases management...');
			}
		} else {
			$t = $bot->bold('❌ Please, insert a Telegram ID to unban a chat!') . PHP_EOL . 'Example: ' . $bot->code('/unban 12345678');
		}
		$bot->sendMessage($v->chat_id, $t);
		die;
	}
}

?>