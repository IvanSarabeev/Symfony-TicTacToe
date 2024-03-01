<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\MultiPlayerRepository;
use App\Service\SinglePlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties] class MainController extends AbstractController
{
    private MultiPlayerRepository $multiPlayerRepository;
    private SinglePlayerRepository $singlePlayerRepository;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        $this->multiPlayerRepository = new MultiPlayerRepository($requestStack);
        $this->singlePlayerRepository = new SinglePlayerRepository($requestStack);
    }

    #[Route('/', name: 'app_homepage')]
    public function homePage(): Response
    {
        return $this->render('views/homepage.html.twig');
    }

    #[Route('/single', name: 'app_multi')]
    public function multiPlayerPage(Request $request): Response
    {
        $data = $request->request->all();
        $selectedCell = $data['cell'] ?? null;

        try {
            if (is_array($selectedCell)) {
                $rowKeys = array_keys($selectedCell);
                $row = array_shift($rowKeys);

                $cellKeys = array_keys($data['cell'][$row]);
                $col = array_shift($cellKeys);

                $this->multiPlayerRepository->getPlayerMove($row, $col);
                $this->multiPlayerRepository->setBotMoves($request);
            }
        } catch (\Exception $exception) {
            throw new \Error($exception);
        }

        $gameResult = $this->multiPlayerRepository->getBoard();
        $announcement = $this->multiPlayerRepository->renderWinner();

        if (!$gameResult) {
            throw $this->createNotFoundException('The page does\'t exist');
        }

        return $this->render('views/single-player.html.twig', [
            'gameBoard' => $gameResult,
            'announce' => $announcement,
        ]);
    }

    #[Route('/multi', name: 'app_single')]
    public function singlePlayerPage(Request $request): Response
    {
        $data = $request->request->all();
        $selectedCell = $data['cell'] ?? null;

        try {
            if (is_array($selectedCell)) {
                $rowKeys = array_keys($selectedCell);
                $row = array_shift($rowKeys);

                $cellKeys = array_keys($selectedCell[$row]);
                $col = array_shift($cellKeys);

                $this->singlePlayerRepository->setPlayerMoves($row, $col);
            }
        } catch (\Exception $exception) {
            throw new \Error($exception);
        }

        $gameResult = $this->singlePlayerRepository->getBoard();
        $announcement = $this->singlePlayerRepository->renderWinner();
        $showStatus = $this->singlePlayerRepository->gameStatus();

        if (!$gameResult) {
            throw $this->createNotFoundException('The page does\'t exist');
        }

        return $this->render('views/multi-player.html.twig', [
            'gameBoard' => $gameResult,
            'announce' => $announcement,
            'gameStatus' => $showStatus,
        ]);
    }

    #[Route('/remove-session', name: 'remove-game-session')]
    public function removeGameSession(): Response
    {
        if ($this->requestStack->getSession()->isStarted() && $this->requestStack->getSession()->has('gameBoard')) {
            $this->singlePlayerRepository->removeGameSession();
        } elseif ($this->requestStack->getSession()->isStarted() && $this->requestStack->getSession()->has('gameBot')) {
            $this->multiPlayerRepository->removeGameSession();
        } else {
            throw new \Error('Session cound\'t be removed');
        }

        return $this->redirectToRoute('app_homepage');
    }
}
