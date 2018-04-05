<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\EzPlatformSiteApi\Core\Site\FilterService;
use Netgen\EzPlatformSiteApi\Core\Site\FindService;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchFilterAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchFilterAdapter;
use Pagerfanta\Pagerfanta;
use RuntimeException;

/**
 * @internal
 *
 * QueryExecutor resolves the Query from the given QueryDefinition, executes it and returns the result.
 */
final class QueryExecutor
{
    /**
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\FilterService
     */
    private $filterService;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\FindService
     */
    private $findService;

    /**
     * @param \eZ\Publish\Core\QueryType\QueryTypeRegistry $queryTypeRegistry
     * @param \Netgen\EzPlatformSiteApi\Core\Site\FilterService $filterService
     * @param \Netgen\EzPlatformSiteApi\Core\Site\FindService $findService
     */
    public function __construct(
        QueryTypeRegistry $queryTypeRegistry,
        FilterService $filterService,
        FindService $findService
    ) {
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->filterService = $filterService;
        $this->findService = $findService;
    }

    /**
     * Execute the Query with the given $name and return the result.
     *
     * @throws \Pagerfanta\Exception\Exception
     * @throws \RuntimeException
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     * @param bool $usePager
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|\Pagerfanta\Pagerfanta
     */
    public function execute(QueryDefinition $queryDefinition, $usePager = true)
    {
        $queryType = $this->queryTypeRegistry->getQueryType($queryDefinition->name);
        $query = $queryType->getQuery($queryDefinition->parameters);

        if ($query instanceof LocationQuery) {
            return $this->getLocationResult($query, $queryDefinition, $usePager);
        }

        if ($query instanceof Query) {
            return $this->getContentResult($query, $queryDefinition, $usePager);
        }

        throw new RuntimeException('Could not handle given query');
    }

    /**
     * Return search result by the given parameters.
     *
     * @throws \Pagerfanta\Exception\Exception
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     * @param bool $usePager
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|\Pagerfanta\Pagerfanta
     */
    private function getLocationResult(LocationQuery $query, QueryDefinition $queryDefinition, $usePager)
    {
        if ($usePager) {
            if ($queryDefinition->useFilter) {
                $adapter = new LocationSearchFilterAdapter($query, $this->filterService);
            } else {
                $adapter = new LocationSearchAdapter($query, $this->findService);
            }

            $pager = new Pagerfanta($adapter);

            $pager->setNormalizeOutOfRangePages(true);
            $pager->setMaxPerPage($queryDefinition->maxPerPage);
            $pager->setCurrentPage($queryDefinition->page);

            return $pager;
        }

        return $this->filterService->filterLocations($query);
    }

    /**
     * Return search result by the given parameters.
     *
     * @throws \Pagerfanta\Exception\Exception
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     * @param bool $usePager
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|\Pagerfanta\Pagerfanta
     */
    private function getContentResult(Query $query, QueryDefinition $queryDefinition, $usePager)
    {
        if ($usePager) {
            if ($queryDefinition->useFilter) {
                $adapter = new ContentSearchFilterAdapter($query, $this->filterService);
            } else {
                $adapter = new ContentSearchAdapter($query, $this->findService);
            }

            $pager = new Pagerfanta($adapter);

            $pager->setNormalizeOutOfRangePages(true);
            $pager->setMaxPerPage($queryDefinition->maxPerPage);
            $pager->setCurrentPage($queryDefinition->page);

            return $pager;
        }

        return $this->filterService->filterContent($query);
    }
}
