<?php

namespace App\Controller;

use App\Service\MultiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SingleController extends AbstractController
{
    /** This Controller class is responsible for rendering the single game mode.
     * It also has protected route feat. Its main duty is t get the name: "cell",
     * then get every row & column by its corresponding array index.
     * Then watch for the player's move after that the bot move.
     * Inside the render pass the following params:
     * gameBoard for rendering the 3x3 array and announce the winner
     * @param Request $request
     * @param MultiService $multiService
     * @return Response
     */
    #[Route('/single', name: 'app_multi')]
    public function multiPlayerPage(Request $request, MultiService $multiService): Response
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