<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;


class Message
{
    public static function enqueueMessage($message, $type = 'success')
    {
        $type = str_replace(['notice', 'message'], ['info', 'success'], $type);
        $message = is_array($message) ? implode('<br/>', $message) : $message;

        $handledTypes = ['info', 'warning', 'error', 'success'];

        if (in_array($type, $handledTypes)) {
            $acyapp = Miscellaneous::getGlobal('app');
            $acyapp->enqueueMessage($message, $type);
        }

        return true;
    }

    public static function displayMessages()
    {
        $acyapp = Miscellaneous::getGlobal('app');
        $messages = $acyapp->getMessageQueue(true);
        if (empty($messages)) {
            return;
        }

        $sorted = [];
        foreach ($messages as $oneMessage) {
            $sorted[$oneMessage['type']][] = $oneMessage['message'];
        }

        foreach ($sorted as $type => $message) {
            Message::display($message, $type);
        }
    }

    public static function display($messages, $type = 'success', $close = true)
    {
        if (empty($messages)) return;
        if (!is_array($messages)) $messages = [$messages];

        foreach ($messages as $id => $message) {
            echo '<div class="acyc__message grid-x acyc__message__'.$type.'">';

            if (is_array($message)) $message = implode('</div><div>', $message);

            echo '<div class="cell auto"><div>'.$message.'</div></div>';

            if ($close) {
                echo '<i data-id="'.Security::escape($id).'" class="cell shrink acyc__message__close acycicon-close"></i>';
            }
            echo '</div>';
        }
    }
}
