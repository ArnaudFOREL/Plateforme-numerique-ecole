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
use App\Repository\UserRepository;
use App\Events\NewMessageWorkEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Notifies post's author about new comments.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class MessageNotificationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $translator;
    private $urlGenerator;
    private $manager;
    private $userrepository;

    public function __construct(EntityManagerInterface $manager, MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator, UserRepository $userrepository)
    {
        $this->em = $manager;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->userrepository = $userrepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NewMessageWorkEvent::class => 'onNewMessage',
        ];
    }

    public function onNewMessage(NewMessageWorkEvent $event): void
    {
        /** @var Works $work */
        $work = $event->getWork();

        $user = $work->getUser();

        $notif = new Notifications();

        $notif->addUser($user);
        $notif->addWork($work);
        $notif->setPostAt(new \Datetime);
        $notif->setIsRead(0);
        $notif->setType(1);

        $this->em->persist($notif);
        
    
        $this->em->flush();
    }
}
