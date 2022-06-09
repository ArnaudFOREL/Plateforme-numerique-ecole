<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MessageControler extends AbstractController
{
    /**
     * @Route("/message", name="sendMessage", methods={"POST"})
     */
    public function __invoke(MessageBusInterface $bus, Request $request): RedirectResponse
    {
        $update = new Update('https://127.0.0.1:8000/fr/message', json_encode([
            'user' => $request->request->get('user'),
            'message' => $request->request->get('message'),
        ]));

        $bus->dispatch($update);

        return $this->redirectToRoute('home_chat');
    }
}

