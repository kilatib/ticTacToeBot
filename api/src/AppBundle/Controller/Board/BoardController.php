<?php

namespace AppBundle\Controller\Board;

use PHPUnit\Runner\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\ {
    Controller\FOSRestController,
    Routing\ClassResourceInterface
};
use JMS\Serializer\SerializationContext;

use AppBundle\Model\Board\ {
    Board,
    BoardException,
    Field
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
     * Returns next step
     *
     * **Response Format**
     *      {
     *          x:1,
     *          y:1,
     *          value: 'X'
     *      }
     *
     *  @Route("/api/board/make-move", name="make_move")
     */
    public function postAction(Request $request)
    {
        $view = $this->view();

        $data = json_decode($request->getContent(), true);

        try {
            $form = $this
                ->createForm( BoardForm::class, new RequestBoard(), [ 'method' => Request::METHOD_POST ])
                ->submit($data);

            if(!$form->isValid()) {
                throw new BoardException(BoardException::INVALID_BOARD);
            }

            /** @var Board $boardModel */
            $boardModel = $form->getData()->getBoard();

            error_log(print_r($boardModel, true));

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
            $view->setStatusCode(Response::HTTP_BAD_REQUEST);
            $view->setData([
                [
                    'code'   => $e->getCode(),
                    'detail' => $e->getMessage()
                ]
            ]);
        }

        return $this->handleView($view);
    }
}
