<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use Netgen\EzPlatformSiteApi\API\Values\ContentInfo as APIContentInfo;

final class ContentInfo extends APIContentInfo
{
    use TranslatableTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $languageCode;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    protected $innerContentInfo;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    protected $innerContentType;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    private $internalLocations;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private $internalContent;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private $internalMainLocation;

    public function __construct(array $properties = [])
    {
        if (array_key_exists('site', $properties)) {
            $this->site = $properties['site'];
            unset($properties['site']);
        }

        parent::__construct($properties);
    }

    /**
     * Magic getter for retrieving convenience properties.
     *
     * @param string $property The name of the property to retrieve
     *
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property) {
            case 'contentTypeIdentifier':
                return $this->innerContentType->identifier;
            case 'contentTypeName':
                return $this->getTranslatedString(
                    $this->languageCode,
                    (array)$this->innerContentType->getNames()
                );
            case 'contentTypeDescription':
                return $this->getTranslatedString(
                    $this->languageCode,
                    (array)$this->innerContentType->getDescriptions()
                );
            case 'locations':
                return $this->getLocations();
            case 'mainLocation':
                return $this->getMainLocation();
            case 'content':
                return $this->getContent();
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (property_exists($this->innerContentInfo, $property)) {
            return $this->innerContentInfo->$property;
        }

        return parent::__get($property);
    }

    /**
     * Magic isset for signaling existence of convenience properties.
     *
     * @param string $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        switch ($property) {
            case 'contentTypeIdentifier':
            case 'contentTypeName':
            case 'contentTypeDescription':
            case 'locations':
            case 'mainLocation':
            case 'content':
                return true;
        }

        if (property_exists($this, $property) || property_exists($this->innerContentInfo, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    private function getLocations()
    {
        if ($this->internalLocations === null) {
            $this->internalLocations = $this->site->getFindService()->findLocations(
                new LocationQuery(
                    [
                        'filter' => new ContentId($this->innerContentInfo->id),
                    ]
                )
            );
        }

        return $this->internalLocations;
    }

    private function getContent()
    {
        if ($this->internalContent === null) {
            $this->internalContent = $this->site->getLoadService()->loadContent($this->innerContentInfo->id);
        }

        return $this->internalContent;
    }

    private function getMainLocation()
    {
        if ($this->internalMainLocation === null && $this->innerContentInfo->mainLocationId !== null) {
            $this->internalMainLocation = $this->site->getLoadService()->loadLocation(
                $this->innerContentInfo->mainLocationId
            );
        }

        return $this->internalMainLocation;
    }
}
