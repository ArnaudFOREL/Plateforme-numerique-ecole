<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Events;

use App\Entity\Works;
use Symfony\Contracts\EventDispatcher\Event;

class WorkCreatedEvent extends Event
{
    protected $work;

    protected $type;

    public function __construct(Works $work, int $type)
    {
        $this->work = $work;
        $this->type = $type;
    }

    public function getWork(): Works
    {
        return $this->work;
    }

    public function getType()
    {
    	return $this->type;
    }
}
