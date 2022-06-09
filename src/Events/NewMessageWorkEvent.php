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

class NewMessageWorkEvent extends Event
{
    protected $work;


    public function __construct(Works $work)
    {
        $this->work = $work;
    }

    public function getWork(): Works
    {
        return $this->work;
    }

}
