<?php

# This is your Bot!
if ($v->update['message']) {
	# Here you will receive messages from private chats and groups
} elseif ($v->update['edited_message']) {
	# Here you will receive edited messages from private chats and groups
} elseif ($v->update['channel_post']) {
	# Here you will receive posts from channels
} elseif ($v->update['edited_channel_post']) {
	# Here you will receive edited posts from channels
} elseif ($v->update['inline_query']) {
	# Here you will receive inline query
} elseif ($v->update['chosen_inline_result']) {
	# Here you will receive chosen inline
} elseif ($v->update['callback_query']) {
	# Here you will receive
} elseif ($v->update['shipping_query']) {
	# Here you will receive shipping query
} elseif ($v->update['pre_checkout_query']) {
	# Here you will receive pre-checkout query
} elseif ($v->update['poll']) {
	# Here you will receive poll query
} elseif ($v->update['poll_answer']) {
	# Here you will receive poll_answer query
} elseif ($v->update['my_chat_member']) {
	# Here you will receive my_chat_member query
} elseif ($v->update['chat_member']) {
	# Here you will receive chat_member query
} elseif ($v->update['chat_join_request']) {
	# Here you will receive chat_join_request query
} else {
	# Unsupported update types
	die(200);
}

?>
