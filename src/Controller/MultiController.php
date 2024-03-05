<?php

namespace App\Controller;

use App\Service\SingleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MultiController extends AbstractController
{

    #[Route('/multi', name: 'app_single')]
    public function singlePlayerPage(Request $request, SingleService $singleService): Response
    {
        $data = $request->request->all();
        $selectedCell = $data['cell'] ?? null;

        try {
            if (is_array($selectedCell)) {
                $rowKeys = array_keys($selectedCell);
                $row = array_shift($rowKeys);

                $cellKeys = array_keys($data['cell'][$row]);
                $col = array_shift($cellKeys);

                $singleService->setPlayerMoves($row, $col);
            }
        } catch (\Exception $exception) {
            throw new \Error($exception);
        }

        $gameResult = $singleService->getBoard();
        $announce = $singleService->renderWinner();
        $showStatus = $singleService->gameStatus();

        return $this->render('multi/multi-player.html.twig', [
            'gameBoard' => $gameResult,
            'announce' => $announce,
            'gameStatus' => $showStatus
        ]);
    }
}