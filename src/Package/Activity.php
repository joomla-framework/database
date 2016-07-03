<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API Activity class for the Joomla Framework.
 *
 * @since  1.0
 *
 * @documentation  http://developer.github.com/v3/activity/
 *
 * @property-read  Activity\Events         $events         GitHub API object for events.
 * @property-read  Activity\Feeds          $feeds          GitHub API object for feeds.
 * @property-read  Activity\Notifications  $notifications  GitHub API object for notifications.
 * @property-read  Activity\Starring       $starring       GitHub API object for starring.
 * @property-read  Activity\Watching       $watching       GitHub API object for watching.
 */
class Activity extends AbstractPackage
{
}
