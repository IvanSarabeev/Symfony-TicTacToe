<?php

namespace App\Controller;

use App\Service\MultiPlayerRepository;
use App\Service\SinglePlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Service\Attribute\Required;

class MainController extends AbstractController
{
    private SessionInterface $session;
    private MultiPlayerRepository $multiPlayerRepository;
    private SinglePlayerRepository $singlePlayerRepository;

    public function __construct(SessionInterface $session, MultiPlayerRepository $multiPlayerRepository, SinglePlayerRepository $singlePlayerRepository)
    {
        $this->session = $session;
        $this->multiPlayerRepository = $multiPlayerRepository;
        $this->singlePlayerRepository = $singlePlayerRepository;
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

            }

        } catch(\Exception $exception) {
            throw new \Error($exception);
        }

        return $this->render('views/multi-player.html.twig', [
            'gameBoard' => $this->singlePlayerRepository->getBoard(),
        ]);
    }
}
