<?php

/*

This file is part of Mustard.

Mustard is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Mustard is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Mustard.  If not, see <http://www.gnu.org/licenses/>.

*/

namespace Hamjoint\Mustard;

trait CanNotify
{
    /**
     * The array of available notification options, mapped to their language
     * keys.
     *
     * @var array
     */
    protected $notificationOptions = [
        'new_bids'     => 1,
        'new_watchers' => 2,
        'ending_items' => 4,
    ];

    /**
     * Return whether a notification is enabled.
     *
     * @param $option string
     *
     * @return bool
     */
    public function canNotify($option)
    {
        return (bool) ($this->notificationOptions[$option] & $this->notifications);
    }

    /**
     * Set a notification.
     *
     * @param $option string
     * @param $enable bool
     */
    public function setNotification($option, $enable)
    {
        $this->notifications = $enable
            ? ($this->notifications | $option)
            : ($this->notifications ^ $option);
    }

    /**
     * Return an array of enabled notifications.
     *
     * @return array
     */
    public function getNotificationOptions()
    {
        return array_filter($this->notificationOptions, function ($notification) {
            return $notification & $this->notifications;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Return the full notification options array.
     *
     * @return array
     */
    public function getAllNotificationOptions()
    {
        return $this->notificationOptions;
    }
}
