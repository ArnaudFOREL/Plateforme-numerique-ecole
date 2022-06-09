<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Entity\Works;
use App\Entity\Notifications;
use App\Events\WorkReadEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Notifies post's author about new comments.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class WorkReadNotificationSubscriber implements EventSubscriberInterface
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkReadEvent::class => 'onWorkRead',
        ];
    }

    public function onWorkRead(WorkReadEvent $event): void
    {
        /** @var Works $work */
        $work = $event->getWork();
        $user = $work->getUser();

        $notif = new Notifications();

        $notif->addUser($user);
        $notif->addWork($work);
        $notif->setPostAt(new \Datetime);
        $notif->setIsRead(0);
        $notif->setType(3);

        $this->em->persist($notif);
    
        $this->em->flush();
    }
}
