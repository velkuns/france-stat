<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Web\Unemployment;

use Application\Controller\Common\AbstractWebController;
use Application\Domain\Unemployment\Repository\UnemploymentRepositoryInterface;
use Application\Enum\UnemploymentGroup;
use Application\VO\Graph;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UnemploymentController extends AbstractWebController
{
    public function __construct(
        private readonly UnemploymentRepositoryInterface $unemploymentRepository,
    ) {}

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function view(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $unemployments = $this->unemploymentRepository->findAllGroupedByGraphIds();

        $graphs = [];
        foreach ($unemployments as $unemployment) {
            $graphs[] = new Graph(
                $unemployment->getGraphId(),
                $unemployment->getGraphName(),
                $unemployment->getGraphValueName(),
            );
        }

        $this->getContext()
            ->add('graphs', $graphs)
        ;

        return $this->getResponse($this->render('@app/Unemployment/Stats.twig'));
    }
}
