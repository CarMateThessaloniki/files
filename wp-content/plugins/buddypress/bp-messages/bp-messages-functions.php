<?php

/**
 * BuddyPress Messages Functions
 *
 * Business functions are where all the magic happens in BuddyPress. They will
 * handle the actual saving or manipulation of information. Usually they will
 * hand off to a database class for data access, then return
 * true or false on success or failure.
 *
 * @package BuddyPress
 * @subpackage MessagesFunctions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function messages_new_message( $args = '' ) {

	$defaults = array (
		'sender_id'  => bp_loggedin_user_id(),
		'thread_id'  => false, // false for a new message, thread id for a reply to a thread.
		'recipients' => false, // Can be an array of usernames, user_ids or mixed.
		'subject'    => false,
		'content'    => false,
		'date_sent'  => bp_core_current_time()
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( empty( $sender_id ) || empty( $content ) )
		return false;

	// Create a new message object
	$message            = new BP_Messages_Message;
	$message->thread_id = $thread_id;
	$message->sender_id = $sender_id;
	$message->subject   = $subject;
	$message->message   = $content;
	$message->date_sent = $date_sent;

	// If we have a thread ID, use the existing recipients, otherwise use the recipients passed
	if ( !empty( $thread_id ) ) {
		$thread = new BP_Messages_Thread( $thread_id );
		$message->recipients = $thread->get_recipients();

		// Strip the sender from the recipient list if they exist
		if ( isset( $message->recipients[$sender_id] ) )
			unset( $message->recipients[$sender_id] );

		if ( empty( $message->subject ) )
			$message->subject = sprintf( __( 'Re: %s', 'buddypress' ), $thread->messages[0]->subject );

	// No thread ID, so make some adjustments
	} else {
		if ( empty( $recipients ) )
			return false;

		if ( empty( $message->subject ) )
			$message->subject = __( 'No Subject', 'buddypress' );

		$recipient_ids 	    = array();

		// Invalid recipients are added to an array, for future enhancements
		$invalid_recipients = array();

		// Loop the recipients and convert all usernames to user_ids where needed
		foreach( (array) $recipients as $recipient ) {
			$recipient = trim( $recipient );

			if ( empty( $recipient ) )
				continue;

			$recipient_id = false;

			// check user_login / nicename columns first
			// @see http://buddypress.trac.wordpress.org/ticket/5151
			if ( bp_is_username_compatibility_mode() ) {
				$recipient_id = bp_core_get_userid( urldecode( $recipient ) );
			} else {
				$recipient_id = bp_core_get_userid_from_nicename( $recipient );
			}

			// check against user ID column if no match and if passed recipient is numeric
			if ( ! $recipient_id && is_numeric( $recipient ) ) {
				if ( bp_core_get_core_userdata( (int) $recipient ) ) {
					$recipient_id = (int) $recipient;
				}
			}

			if ( ! $recipient_id ) {
				$invalid_recipients[] = $recipient;
			} else {
				$recipient_ids[] = (int) $recipient_id;
			}
		}

		// Strip the sender from the recipient list if they exist
		if ( $key = array_search( $sender_id, (array) $recipient_ids ) )
			unset( $recipient_ids[$key] );

		// Remove duplicates
		$recipient_ids = array_unique( (array) $recipient_ids );

		if ( empty( $recipient_ids ) )
			return false;

		// Format this to match existing recipients
		foreach( (array) $recipient_ids as $i => $recipient_id ) {
			$message->recipients[$i]          = new stdClass;
			$message->recipients[$i]->user_id = $recipient_id;
		}
	}

	if ( $message->send() ) {
		do_action_ref_array( 'messages_message_sent', array( &$message ) );

		return $message->thread_id;
	}

	return false;
}


function messages_send_notice( $subject, $message ) {
	if ( !bp_current_user_can( 'bp_moderate' ) || empty( $subject ) || empty( $message ) ) {
		return false;

	// Has access to send notices, lets do it.
	} else {
		$notice            = new BP_Messages_Notice;
		$notice->subject   = $subject;
		$notice->message   = $message;
		$notice->date_sent = bp_core_current_time();
		$notice->is_active = 1;
		$notice->save(); // send it.

		do_action_ref_array( 'messages_send_notice', array( $subject, $message ) );

		return true;
	}
}

function messages_delete_thread( $thread_ids ) {
	do_action( 'messages_before_delete_thread', $thread_ids );

	if ( is_array( $thread_ids ) ) {
		$error = 0;
		for ( $i = 0, $count = count( $thread_ids ); $i < $count; ++$i ) {
			if ( !$status = BP_Messages_Thread::delete( $thread_ids[$i]) ) {
				$error = 1;
			}
		}

		if ( !empty( $error ) )
			return false;

		do_action( 'messages_delete_thread', $thread_ids );

		return true;
	} else {
		if ( !BP_Messages_Thread::delete( $thread_ids ) )
			return false;

		do_action( 'messages_delete_thread', $thread_ids );

		return true;
	}
}

function messages_check_thread_access( $thread_id, $user_id = 0 ) {
	if ( empty( $user_id ) )
		$user_id = bp_loggedin_user_id();

	return BP_Messages_Thread::check_access( $thread_id, $user_id );
}

function messages_mark_thread_read( $thread_id ) {
	return BP_Messages_Thread::mark_as_read( $thread_id );
}

function messages_mark_thread_unread( $thread_id ) {
	return BP_Messages_Thread::mark_as_unread( $thread_id );
}

function messages_add_callback_values( $recipients, $subject, $content ) {
	@setcookie( 'bp_messages_send_to', $recipients, time() + 60 * 60 * 24, COOKIEPATH );
	@setcookie( 'bp_messages_subject', $subject,    time() + 60 * 60 * 24, COOKIEPATH );
	@setcookie( 'bp_messages_content', $content,    time() + 60 * 60 * 24, COOKIEPATH );
}

function messages_remove_callback_values() {
	@setcookie( 'bp_messages_send_to', false, time() - 1000, COOKIEPATH );
	@setcookie( 'bp_messages_subject', false, time() - 1000, COOKIEPATH );
	@setcookie( 'bp_messages_content', false, time() - 1000, COOKIEPATH );
}

function messages_get_unread_count( $user_id = 0 ) {
	if ( empty( $user_id ) )
		$user_id = bp_loggedin_user_id();

	return BP_Messages_Thread::get_inbox_count( $user_id );
}

function messages_is_user_sender( $user_id, $message_id ) {
	return BP_Messages_Message::is_user_sender( $user_id, $message_id );
}

function messages_get_message_sender( $message_id ) {
	return BP_Messages_Message::get_message_sender( $message_id );
}

function messages_is_valid_thread( $thread_id ) {
	return BP_Messages_Thread::is_valid( $thread_id );
}
