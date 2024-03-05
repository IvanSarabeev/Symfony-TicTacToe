<?php

namespace App\Controller;

use App\Service\MultiPlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SingleController extends AbstractController
{
    #[Route('/single', name: 'app_multi')]
    public function multiPlayerPage(Request $request, MultiPlayerRepository $playerRepository): Response
    {
        $data = $request->request->all();
        $selectedCell = $data['cell'] ?? null;

        try {
            if (is_array($selectedCell)) {
                $rowKeys = array_keys($selectedCell);
                $row = array_shift($rowKeys);

                $cellKeys = array_keys($data['cell'][$row]);
                $col = array_shift($cellKeys);

                $playerRepository->getPlayerMove($row, $col);
                $playerRepository->setBotMoves($request);
            }
        } catch ( \Exception $exception) {
            throw new \Error($exception);
        }

        $gameResult = $playerRepository->getBoard();
        $announce = $playerRepository->renderWinner();

        return $this->render('single/single-player.html.twig', [
           'gameBoard' => $gameResult,
           'announce' => $announce
        ]);
    }
}