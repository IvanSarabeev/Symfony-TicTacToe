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
    private $requestStack;
    private MultiPlayerRepository $multiPlayerRepository;
    private SinglePlayerRepository $singlePlayerRepository;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        $this->multiPlayerRepository = new MultiPlayerRepository($requestStack);
        $this->singlePlayerRepository = new SinglePlayerRepository();
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

        $session = $request->getSession();

        $gameResult = $this->multiPlayerRepository->getBoard();

        if (!$gameResult) {
            throw $this->createNotFoundException('The page does\'t exit');
        }

        return $this->render('views/single-player.html.twig', [
            'gameBoard' => $gameResult,
        ]);
    }

    #[Route('/multi', name: 'app_single')]
    public function singlePlayerPage(Request $request): Response
    {
        $selectedCell = $_POST['cell'] ?? null;

        try {
            $session = $request->getSession();

            if (is_array($selectedCell)) {
                $rowKeys = array_keys($selectedCell);
                $row = array_shift($rowKeys);

                if ($session->has('gameBoard')) {
                    $gameResult = $session->get('gameBoard');
                    $session->set('gameBoard', $gameResult);
                    $cellKeys = array_keys($_POST['cell'][$row]);
                    $col = array_shift($cellKeys);

                    $this->singlePlayerRepository->setPlayerMoves($row, $col);
                    $this->singlePlayerRepository->checkGameResult();
                    // TODO: Keep track on the players move
                } else {
                    $session->clear();
                }
            }

            $gameResult = $this->singlePlayerRepository->getBoard();

            if (!$gameResult) {
                throw $this->createNotFoundException('The page does\'t exit');
            }
        } catch (\Exception $exception) {
            throw new \Error($exception);
        }

        return $this->render('views/multi-player.html.twig', [
            'gameBoard' => $this->singlePlayerRepository->getBoard(),
        ]);
    }
}
