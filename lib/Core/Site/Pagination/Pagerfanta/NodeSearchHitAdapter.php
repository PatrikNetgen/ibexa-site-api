<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Netgen\EzPlatformSiteApi\API\FindService;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @deprecated since version 2.1, to be removed in 3.0. Use LocationSearchHitAdapter instead.
 *
 * Pagerfanta adapter for Netgen eZ Platform Site Node search.
 * Will return results as SearchHit objects.
 */
class NodeSearchHitAdapter implements AdapterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\LocationQuery
     */
    private $query;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    /**
     * @var int
     */
    private $nbResults;

    public function __construct(LocationQuery $query, FindService $findService)
    {
        @trigger_error(
            'NodeSearchHitAdapter is deprecated since version 2.1 and will be removed in 3.0. Use FindAdapter or FilterAdapter instead.',
            E_USER_DEPRECATED
        );

        $this->query = $query;
        $this->findService = $findService;
    }

    /**
     * Returns the number of results.
     *
     * @return int The number of results
     */
    public function getNbResults()
    {
        if (isset($this->nbResults)) {
            return $this->nbResults;
        }

        $countQuery = clone $this->query;
        $countQuery->limit = 0;

        return $this->nbResults = $this->findService->findNodes($countQuery)->totalCount;
    }

    /**
     * Returns a slice of the results, as SearchHit objects.
     *
     * @param int $offset The offset
     * @param int $length The length
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchHit[]
     */
    public function getSlice($offset, $length)
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $query->performCount = false;

        $searchResult = $this->findService->findNodes($query);

        // Set count for further use if returned by search engine despite !performCount (Solr, ES)
        if (!isset($this->nbResults) && isset($searchResult->totalCount)) {
            $this->nbResults = $searchResult->totalCount;
        }

        return $searchResult->searchHits;
    }
}
