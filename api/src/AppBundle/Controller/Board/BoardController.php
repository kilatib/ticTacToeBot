<?php

namespace AppBundle\Controller\Board;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};

use FOS\RestBundle\ {
    Controller\FOSRestController,
    Routing\ClassResourceInterface
};

use AppBundle\Model\Board\ {
    Board,
    BoardException,
    Field,
    FieldInterface
};

use AppBundle\Model\Request\Board as RequestBoard;

use AppBundle\TicTacToe\ {
    Factory\Strategy        as StrategyFactory,
    Strategy\MoveInterface
};

use AppBundle\Form\Board as BoardForm;

use AppBundle\AppBundleException;

class BoardController extends FOSRestController implements ClassResourceInterface
{
    /**
     * This method will be reused for two action: newtMove, boardState
     *
     * @param  Request $request
     * @return Board
     * @throws AppBundleException
     * @throws BoardException
     */
    private function createBoardFromRequest(Request $request)
    {
        $content = $request->getContent();
        if (empty($content)) {
            throw new AppBundleException(AppBundleException::NO_CONTENT, Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);

        $form = $this
            ->createForm( BoardForm::class, new RequestBoard(), [ 'method' => Request::METHOD_POST ])
            ->submit($data);

        if(!$form->isValid()) {
            throw new BoardException(BoardException::INVALID_BOARD, Response::HTTP_BAD_REQUEST);
        }

        /** @var Board $boardModel */
        $boardModel = $form->getData()->getBoard();

        /** if board invalid will be thrown one of the exceptions **/
        $boardModel->validate();

        return $boardModel;
    }

    /**
     * Returns next step
     *
     * **Response Format**
     *      {
     *          x:1,
     *          y:1,
     *          value: 'X'
     *      }
     *
     * @Route("/api/board/make-move", name="make_move")
     *
     */
    public function postMakeMoveAction(Request $request)
    {
        $view = $this->view();
        try {
            $boardModel = $this->createBoardFromRequest($request);

            /** @var StrategyFactory $factory */
            $factory = $this->get('factory.tactactoe');

            /** @var MoveInterface $strategyService */
            $strategyService = $factory->getStrategy();

            $nextMove = $strategyService->makeMove($boardModel->toArray(), $boardModel->nextUnit());
            $nextMoveField = new Field();
            $nextMoveField->populate(...$nextMove);

            $view->setStatusCode(Response::HTTP_OK);
            $view->setData($nextMoveField);

        } catch (AppBundleException $e) {
            $view->setStatusCode($e->getCode() ?? Response::HTTP_BAD_REQUEST);
            $view->setData([
                [
                    'code'   => $e->getCode(),
                    'detail' => $e->getMessage()
                ]
            ]);
        }

        return $result = $this->handleView($view);
    }

    /**
     * Return symbol of winner or empty if game continue
     *
     * **Response Format**
     *      {
     *         winner: 'X' | null
     *      }
     *
     * @Route("/api/board/game-winner", name="game_winner")
     *
     */
    public function postGameWinner(Request $request)
    {
        $view = $this->view();
        try {
            $boardModel = $this->createBoardFromRequest($request);

            /** @var StrategyFactory $factory */
            $factory = $this->get('factory.tactactoe');

            /** @var MoveInterface $strategyService */
            $strategyService = $factory->getStrategy();

            $winnerSymbol = '';
            if ($strategyService->isWinnerCombinationPresent($boardModel->toArray())) {
                $winnerSymbol = $strategyService->getWinnerSymbolIfPresent($boardModel->toArray());
            }

            $view->setStatusCode(Response::HTTP_OK);
            $view->setData([
                'winner' => $winnerSymbol
            ]);

        } catch (AppBundleException $e) {
            $view->setStatusCode($e->getCode() ?? Response::HTTP_BAD_REQUEST);
            $view->setData([
                [
                    'code'   => $e->getCode(),
                    'detail' => $e->getMessage()
                ]
            ]);
        }

        return $result = $this->handleView($view);
    }

    /**
     * Returns next step
     *
     * **Response Format**
     *      [ 'X', 'O' ]
     *
     *  @Route("/api/board/symbols", name="symbols")
     */
    public function getSymbolsAction()
    {
        $view = $this->view();
        $view->setData(
            [
                FieldInterface::PRIMARY_PLAYER_SYMBOL,
                FieldInterface::SECONDARY_PLAYER_SYMBOL
            ]
        );

        return $view;
    }
}
