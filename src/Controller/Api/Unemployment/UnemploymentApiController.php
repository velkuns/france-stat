<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Api\Unemployment;

use Application\Controller\Common\AbstractApiController;
use Application\Domain\Unemployment\Repository\UnemploymentRepositoryInterface;
use Application\Enum\Gender;
use Application\VO\Dataset;
use Eureka\Component\Orm\Exception\OrmException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UnemploymentApiController extends AbstractApiController
{
    public function __construct(
        private readonly UnemploymentRepositoryInterface $unemploymentRepository,
    ) {}

    /**
     * @throws OrmException
     */
    public function graph(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $graphId = (int) $serverRequest->getAttribute('graphId');

        $unemployments = $this->unemploymentRepository->findAllByGraphId($graphId);

        $data = ['labels' => [], 'datasets' => []];

        foreach ($unemployments as $unemployment) {

            $dataset = $unemployment->getDataset();
            $data['datasets'][] = $dataset;

            if (empty($data['labels'])) {
                $data['labels'] = $unemployment->getLabels();
            }
        }

        return $this->getResponseJson(['data' => $data]);
    }
}
