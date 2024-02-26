<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\MultiPlayerRepository;
use App\Service\SinglePlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties] class MainController extends AbstractController
{
    private MultiPlayerRepository $multiPlayerRepository;
    private SinglePlayerRepository $singlePlayerRepository;
//    private RequestStack $requestStack;

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
        $selectedCell = $_POST['cell'] ?? null;

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
        // TODO: I need to pass the $session property as an parameter

        $announcement = $this->multiPlayerRepository->renderWinner();

        return $this->render('views/single-player.html.twig', [
//            'gameBoard' => $this->requestStack->getSession()->get("gameBot"),
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

        if (!$gameResult) {
            throw $this->createNotFoundException('The page does\'t exit');
        }

        return $this->render('views/multi-player.html.twig', [
            'gameBoard' => $gameResult,
            'announce' => $announcement,
        ]);
    }
}
