<?php

namespace App\Controller;

use App\Service\SingleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MultiController extends AbstractController
{

    /** This Controller is responsible for rendering the multiplayer game mode.
     * It's also a protected route. Its main duty is to get the name: "cell"
     * then get every row & column by its corresponding index from [0][0] to [2][2]
     * Then render the multiplayer view and pass the following params:
     * gameBoard for rendering the 3x3 array and announce the winner.
     * @param Request $request
     * @param SingleService $singleService
     * @return Response
     */
    #[Route('/multi', name: 'app_single')]
    public function singlePlayerPage(Request $request, SingleService $singleService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without Signing in.');

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

        return $this->render('multi/multi-player.html.twig', [
            'gameBoard' => $gameResult,
            'announce' => $announce,
        ]);
    }
}