<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchAdapter;

/**
 * @group pager
 */
class LocationSearchAdapterTest extends LocationSearchHitAdapterTest
{
    protected function getAdapter(LocationQuery $query)
    {
        return new LocationSearchAdapter($query, $this->findService);
    }

    protected function getExpectedSlice($hits)
    {
        $expectedResult = [];

        /** @var \eZ\Publish\API\Repository\Values\Content\Search\SearchHit[] $hits */
        foreach ($hits as $hit) {
            $expectedResult[] = $hit->valueObject;
        }

        return $expectedResult;
    }
}
