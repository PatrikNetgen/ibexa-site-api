<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\SPI\FieldType\Value;
use Pagerfanta\Pagerfanta;

/**
 * Site Content object represents eZ Platform Repository Content object in a current version
 * and specific language.
 *
 * Corresponds to eZ Platform Repository Content object.
 * @see \eZ\Publish\API\Repository\Values\Content\Content
 *
 * @property-read string|int $id
 * @property-read int|string|null $mainLocationId
 * @property-read string $name
 * @property-read string $languageCode
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\ContentInfo $contentInfo
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Fields $fields
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Location|null $mainLocation
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Content|null $owner
 * @property-read \eZ\Publish\API\Repository\Values\User\User|null $innerOwnerUser
 * @property-read \eZ\Publish\API\Repository\Values\Content\Content $innerContent
 * @property-read \eZ\Publish\API\Repository\Values\Content\VersionInfo $innerVersionInfo
 * @property-read \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
 */
abstract class Content extends ValueObject
{
    /**
     * Returns if content has the field with the given field definition $identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    abstract public function hasField(string $identifier): bool;

    /**
     * Return Field object for the given field definition $identifier.
     *
     * @param string $identifier
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Field
     */
    abstract public function getField(string $identifier): Field;

    /**
     * Returns if content has the field with the given field $id.
     *
     * @param string|int $id
     *
     * @return bool
     */
    abstract public function hasFieldById($id): bool;

    /**
     * Return Field object for the given field $id.
     *
     * @param string|int $id
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Field
     */
    abstract public function getFieldById($id): Field;

    /**
     * Returns a field value for the given field definition identifier.
     *
     * @param string $identifier
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    abstract public function getFieldValue(string $identifier): Value;

    /**
     * Returns a field value for the given field $id.
     *
     * @param string|int $id
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    abstract public function getFieldValueById($id): Value;

    /**
     * Return an array of Locations, limited by optional $limit.
     *
     * @param int $limit
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    abstract public function getLocations(int $limit = 25): array;

    /**
     * Return an array of Locations, limited by optional $maxPerPage and $currentPage.
     *
     * @param int $maxPerPage
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta Pagerfanta instance iterating over Site API Locations
     */
    abstract public function filterLocations(int $maxPerPage = 25, int $currentPage = 1): Pagerfanta;

    /**
     * Return single related Content from $fieldDefinitionIdentifier field.
     *
     * @param string $fieldDefinitionIdentifier
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content|null
     */
    abstract public function getFieldRelation(string $fieldDefinitionIdentifier): ?Content;

    /**
     * Return all related Content from $fieldDefinitionIdentifier.
     *
     * @param string $fieldDefinitionIdentifier
     * @param int $limit
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
     */
    abstract public function getFieldRelations(string $fieldDefinitionIdentifier, int $limit = 25): array;

    /**
     * Return related Content from $fieldDefinitionIdentifier field in Content with given $contentId,
     * optionally limited by a list of $contentTypeIdentifiers.
     *
     * @param string $fieldDefinitionIdentifier
     * @param string[] $contentTypeIdentifiers
     * @param int $maxPerPage
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta Pagerfanta instance iterating over Site API Content items
     */
    abstract public function filterFieldRelations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1
    ): Pagerfanta;
}
