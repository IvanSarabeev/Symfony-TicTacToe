<?php

namespace App\Controller;

use App\Service\MultiPlayerRepository;
use App\Service\SinglePlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    private MultiPlayerRepository $multiPlayerRepository;
    private SinglePlayerRepository $singlePlayerRepository;
    public function __construct()
    {
        $this->multiPlayerRepository = new MultiPlayerRepository();
        $this->singlePlayerRepository = new SinglePlayerRepository();
    }

    #[Route('/', name: 'app_homepage')]
    public function homePage(): Response
    {
        return $this->render('views/homepage.html.twig');
    }

    #[Route('/single', name: 'app_multi')]
    public function multiPlayerPage(): Response
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

        } catch(\Exception $exception) {
            throw new \Error($exception);

        }
        // TODO: Keep track on the players move
        $gameResult = $this->multiPlayerRepository->getBoard();

        return $this->render('views/single-player.html.twig', [
            'gameBoard' => $gameResult,
        ]);
    }

    #[Route('/multi', name: 'app_single')]
    public function singlePlayerPage(): Response
    {
        $selectedCell = $_POST['cell'] ?? null;

        try {
            if (is_array($selectedCell)) {
                $rowKeys = array_keys($selectedCell);
                $row = array_shift($rowKeys);

                $cellKeys = array_keys($_POST['cell'][$row]);
                $col = array_shift($cellKeys);

                $this->singlePlayerRepository->setPlayerMoves($row, $col);
                $this->singlePlayerRepository->checkGameResult();

                // TODO: Keep track on the players move
            }

        } catch(\Exception $exception) {
            throw new \Error($exception);
        }

        return $this->render('views/multi-player.html.twig', [
            'gameBoard' => $this->singlePlayerRepository->getBoard(),
        ]);
    }
}
