<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaSiteApiBundle\Tests\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use InvalidArgumentException;
use Netgen\Bundle\IbexaSiteApiBundle\DependencyInjection\Configuration\Parser\ContentView;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;
use function file_get_contents;
use function preg_quote;

/**
 * @group config
 *
 * @internal
 */
final class ContentViewTest extends AbstractParserTestCase
{
    public function providerForTestValid(): array
    {
        return [
            [
                [
                    'match' => ['config'],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'template' => 'template',
                    'queries' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'template' => 'template',
                    'controller' => 'controller',
                    'queries' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => 'named_query',
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                        ],
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'use_filter' => false,
                        ],
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'max_per_page' => 10,
                        ],
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'max_per_page' => 10,
                            'page' => 2,
                        ],
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'max_per_page' => 10,
                            'page' => 2,
                            'parameters' => [
                                'some' => 'parameters',
                            ],
                        ],
                    ],
                ],
            ],
            [
                [
                    'extends' => 'full/throttle',
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestValid
     */
    public function testValid(array $configurationValues): void
    {
        $this->load([
            'system' => [
                'ibexa_demo_group' => [
                    'ng_content_view' => [
                        'full' => [
                            'throttle' => [
                                'match' => [],
                            ],
                        ],
                        'some_view' => [
                            'some_key' => $configurationValues,
                        ],
                    ],
                ],
            ],
        ]);

        $this->addToAssertionCount(1);
    }

    public function providerForTextExtends(): array
    {
        return [
            [
                [
                    'extends' => 'base/one',
                ],
                [
                    'match' => [],
                    'queries' => [],
                    'params' => ['params'],
                    'extends' => 'base/one',
                ],
            ],
            [
                [
                    'extends' => 'base/two',
                ],
                [
                    'match' => [],
                    'queries' => [
                        'query' => [
                            'query_type' => 'test',
                            'parameters' => [],
                            'use_filter' => true,
                            'max_per_page' => 25,
                            'page' => 1,
                        ],
                    ],
                    'params' => [],
                    'extends' => 'base/two',
                ],
            ],
            [
                [
                    'extends' => 'base/three',
                ],
                [
                    'template' => 'template',
                    'match' => ['match'],
                    'queries' => [],
                    'params' => [],
                    'extends' => 'base/three',
                ],
            ],
            [
                [
                    'match' => ['selen'],
                    'params' => ['petrusimen'],
                    'extends' => 'base/one',
                ],
                [
                    'match' => ['selen'],
                    'queries' => [],
                    'params' => ['petrusimen'],
                    'extends' => 'base/one',
                ],
            ],
            [
                [
                    'queries' => [
                        'query' => [
                            'query_type' => 'blušć',
                        ],
                    ],
                    'extends' => 'base/two',
                ],
                [
                    'match' => [],
                    'queries' => [
                        'query' => [
                            'query_type' => 'blušć',
                            'parameters' => [],
                            'use_filter' => true,
                            'max_per_page' => 25,
                            'page' => 1,
                        ],
                    ],
                    'params' => [],
                    'extends' => 'base/two',
                ],
            ],
            [
                [
                    'template' => 'pazdej',
                    'extends' => 'base/three',
                ],
                [
                    'template' => 'pazdej',
                    'match' => ['match'],
                    'queries' => [],
                    'params' => [],
                    'extends' => 'base/three',
                ],
            ],
        ];
    }

    /**
     * @group xxx
     * @dataProvider providerForTextExtends
     */
    public function testExtends(array $configurationValues, array $expectedValues): void
    {
        $baseConfig = [
            'system' => [
                'ibexa_demo_group' => [
                    'ng_content_view' => [
                        'base' => [
                            'one' => [
                                'match' => [],
                                'params' => ['params'],
                            ],
                            'two' => [
                                'match' => null,
                                'queries' => [
                                    'query' => [
                                        'query_type' => 'test',
                                    ],
                                ],
                            ],
                            'three' => [
                                'template' => 'template',
                                'match' => ['match'],
                            ],
                        ],
                        'tested_view' => [
                            'tested_name' => $configurationValues,
                        ],
                    ],
                ],
            ],
        ];

        $this->load($baseConfig);

        $expectedValues = [
            'base' => [
                'one' => [
                    'match' => [],
                    'queries' => [],
                    'params' => ['params'],
                ],
                'two' => [
                    'match' => [],
                    'queries' => [
                        'query' => [
                            'query_type' => 'test',
                            'parameters' => [],
                            'use_filter' => true,
                            'max_per_page' => 25,
                            'page' => 1,
                        ],
                    ],
                    'params' => [],
                ],
                'three' => [
                    'template' => 'template',
                    'match' => ['match'],
                    'queries' => [],
                    'params' => [],
                ],
            ],
            'tested_view' => [
                'tested_name' => $expectedValues,
            ],
        ];

        $this->assertContainerBuilderHasParameter('ibexa.site_access.config.ibexa_demo_site.ng_content_view', $expectedValues);

        $this->addToAssertionCount(1);
    }

    public function providerForTestInvalid(): array
    {
        return [
            [
                [
                    'match' => ['config'],
                    'queries' => [0 => 'query'],
                ],
                InvalidConfigurationException::class,
                'Query key must be a string conforming to a valid Twig variable name',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => ['123abc' => 'query'],
                ],
                InvalidConfigurationException::class,
                'Query key must be a string conforming to a valid Twig variable name',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'parameters' => [
                                'some' => 'parameters',
                            ],
                        ],
                    ],
                ],
                InvalidConfigurationException::class,
                'One of "named_query" or "query_type" must be set',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'parameters' => 'parameters',
                        ],
                    ],
                ],
                InvalidConfigurationException::class,
                'Expected array, but got string',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'use_filter' => [],
                        ],
                    ],
                ],
                InvalidConfigurationException::class,
                'Expected scalar, but got array',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query_name',
                            'query_type' => 'query_type_name',
                        ],
                    ],
                ],
                InvalidConfigurationException::class,
                'You cannot use both "named_query" and "query_type" at the same time',
            ],
            [
                [
                    'template' => 'ten/plates.json.twig',
                ],
                InvalidArgumentException::class,
                'When view configuration is not extending another, match key is required',
            ],
            [
                [
                    'extends' => 'snowball/earth',
                ],
                InvalidConfigurationException::class,
                'In spare/ribs: extended view configuration "snowball/earth" was not found',
            ],
            [
                [
                    'extends' => 'steaks/everywhere',
                ],
                InvalidConfigurationException::class,
                'In spare/ribs: only one level of view configuration inheritance is allowed, steaks/everywhere already extends potato/chips',
            ],
        ];
    }

    /**
     * @dataProvider providerForTestInvalid
     */
    public function testInvalid(array $configurationValues, string $exceptionClass, string $exceptionMessage): void
    {
        $this->expectException($exceptionClass);
        $exceptionMessage = preg_quote($exceptionMessage, '/');
        self::matchesRegularExpression("/{$exceptionMessage}/");

        $this->load([
            'system' => [
                'ibexa_demo_group' => [
                    'ng_content_view' => [
                        'potato' => [
                            'chips' => [
                                'match' => null,
                            ],
                        ],
                        'steaks' => [
                            'everywhere' => [
                                'extends' => 'potato/chips',
                            ],
                        ],
                        'spare' => [
                            'ribs' => $configurationValues,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function providerForTestDefaultValues(): array
    {
        return [
            [
                [
                    'match' => ['config'],
                ],
                [
                    'match' => ['config'],
                    'queries' => [],
                    'params' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => 'named_query',
                    ],
                ],
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'parameters' => [],
                        ],
                    ],
                    'params' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'max_per_page' => 50,
                        ],
                    ],
                ],
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'max_per_page' => 50,
                            'parameters' => [],
                        ],
                    ],
                    'params' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'parameters' => [
                                'some' => 'value',
                            ],
                        ],
                    ],
                ],
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'parameters' => [
                                'some' => 'value',
                            ],
                        ],
                    ],
                    'params' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type',
                        ],
                    ],
                ],
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type',
                            'parameters' => [],
                            'use_filter' => true,
                            'max_per_page' => 25,
                            'page' => 1,
                        ],
                    ],
                    'params' => [],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestDefaultValues
     */
    public function testDefaultValues(array $configurationValues, array $expectedConfigurationValues): void
    {
        $this->load([
            'system' => [
                'ibexa_demo_group' => [
                    'ng_content_view' => [
                        'some_view' => [
                            'some_key' => $configurationValues,
                        ],
                    ],
                ],
            ],
        ]);
        $expectedConfigurationValues = [
            'some_view' => [
                'some_key' => $expectedConfigurationValues,
            ],
        ];

        $this->assertConfigResolverParameterValue(
            'ng_content_view',
            $expectedConfigurationValues,
            'ibexa_demo_site',
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([
                new ContentView(),
            ]),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../../Fixtures/minimal.yaml'));
    }
}
