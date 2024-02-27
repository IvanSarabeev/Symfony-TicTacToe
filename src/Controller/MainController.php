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

                $cellKeys = array_keys($_POST['cell'][$row]);
                $col = array_shift($cellKeys);

                $this->multiPlayerRepository->getPlayerMove($row, $col);
                $this->multiPlayerRepository->setBotMoves();
            }
        } catch (\Exception $exception) {
            throw new \Error($exception);
        }

        $gameResult = $this->multiPlayerRepository->getBoard();
        $announcement = $this->multiPlayerRepository->renderWinner();
//        $removeSession = $this->multiPlayerRepository->removeGameSession();

        if (!$gameResult) {
            throw $this->createNotFoundException('The page does\'t exist');
        }

        return $this->render('views/single-player.html.twig', [
            'gameBoard' => $gameResult,
            'announce' => $announcement,
//            'remove' => $removeSession,
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
//        $removeSession = $this->singlePlayerRepository->removeGameSession();
        $showStatus = $this->singlePlayerRepository->gameStatus();

        if (!$gameResult) {
            throw $this->createNotFoundException('The page does\'t exist');
        }

        return $this->render('views/multi-player.html.twig', [
            'gameBoard' => $gameResult,
            'announce' => $announcement,
//            'remove' => $removeSession,
            'status' => $showStatus,
        ]);
    }
}
