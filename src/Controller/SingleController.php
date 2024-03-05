<?php

namespace App\Controller;

use App\Service\MultiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SingleController extends AbstractController
{
    #[Route('/single', name: 'app_multi')]
    public function multiPlayerPage(Request $request, MultiService $multiService): Response
    {
        $data = $request->request->all();
        $selectedCell = $data['cell'] ?? null;

        try {
            if (is_array($selectedCell)) {
                $rowKeys = array_keys($selectedCell);
                $row = array_shift($rowKeys);

                $cellKeys = array_keys($data['cell'][$row]);
                $col = array_shift($cellKeys);

                $multiService->getPlayerMove($row, $col);
                $multiService->setBotMoves($request);
            }
        } catch ( \Exception $exception) {
            throw new \Error($exception);
        }

        $gameResult = $multiService->getBoard();
        $announce = $multiService->renderWinner();

        return $this->render('single/single-player.html.twig', [
           'gameBoard' => $gameResult,
           'announce' => $announce
        ]);
    }
}