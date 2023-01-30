<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Tests\Functional;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestContent;
use Symfony\Component\Validator\Constraints as Assert;

class MappedRequestAttributesTest extends AbstractWebTestCase
{
    /**
     * @dataProvider mapQueryStringProvider
     */
    public function testMapQueryString(array $query, string $expectedResponse, int $expectedStatusCode)
    {
        $client = self::createClient(['test_case' => 'MappedRequestAttributes']);

        $client->request('GET', '/map-query-string', $query);

        $response = $client->getResponse();
        self::assertJsonStringEqualsJsonString($expectedResponse, $response->getContent());
        self::assertSame($expectedStatusCode, $response->getStatusCode());
    }

    public static function mapQueryStringProvider(): iterable
    {
        yield 'valid' => [
            'query' => ['filter' => ['status' => 'approved', 'quantity' => '4']],
            'expectedResponse' => 'filter.status=approved,filter.quantity=4',
            'expectedResponse' => <<<'JSON'
{
    "filter": {
        "status": "approved",
        "quantity": 4
    }
}
JSON,
            'expectedStatusCode' => 200,
        ];

        yield 'invalid' => [
            'query' => ['filter' => ['status' => 'approved', 'quantity' => '200']],
            'expectedResponse' => <<<'JSON'
{
    "type": "https:\/\/symfony.com\/errors\/validation",
    "title": "Validation Failed",
    "detail": "filter.quantity: This value should be less than 10.",
    "violations": [
        {
            "propertyPath": "filter.quantity",
            "title": "This value should be less than 10.",
            "parameters": {
                "{{ value }}": "200",
                "{{ compared_value }}": "10",
                "{{ compared_value_type }}": "int"
            },
            "type": "urn:uuid:079d7420-2d13-460c-8756-de810eeb37d2"
        }
    ]
}
JSON,
            'expectedStatusCode' => 400,
        ];
    }

    /**
     * @dataProvider mapRequestContentProvider
     */
    public function testMapRequestContent(
        string $format,
        string $content,
        string $expectedResponse,
        int $expectedStatusCode
    ) {
        $client = self::createClient(['test_case' => 'MappedRequestAttributes']);

        $client->request(
            'POST',
            "/map-request-content.$format",
            [],
            [],
            ['HTTP_ACCEPT' => 'xml' === $format ? 'text/xml' : 'application/json'],
            $content
        );

        $response = $client->getResponse();
        if ('xml' === $format) {
            self::assertXmlStringEqualsXmlString($expectedResponse, $response->getContent());
        } else {
            self::assertJsonStringEqualsJsonString($expectedResponse, $response->getContent());
        }

        self::assertSame($expectedStatusCode, $response->getStatusCode());
    }

    public static function mapRequestContentProvider(): iterable
    {
        yield 'valid json' => [
            'format' => 'json',
            'content' => <<<'JSON'
{
    "comment": "Hello everyone!",
    "approved": false
}
JSON,
            'expectedResponse' => <<<'JSON'
{
    "comment": "Hello everyone!",
    "approved": false
}
JSON,
            'expectedStatusCode' => 200,
        ];

        yield 'valid xml' => [
            'format' => 'xml',
            'content' => <<<'XML'
<request>
    <comment>Hello everyone!</comment>
    <approved>true</approved>
</request>
XML,
            'expectedResponse' => <<<'XML'
<response>
    <comment>Hello everyone!</comment>
    <approved>1</approved>
</response>
XML,
            'expectedStatusCode' => 200,
        ];

        yield 'missing property' => [
            'format' => 'json',
            'content' => <<<'JSON'
{
    "comment": "Hello everyone!"
}
JSON,
            'expectedResponse' => <<<'JSON'
{
    "type": "https:\/\/symfony.com\/errors\/validation",
    "title": "Validation Failed",
    "detail": "The type must be one of \"unknown\" (\"array\" given).",
    "violations": [
        {
            "propertyPath": "",
            "title": "The type must be one of \"unknown\" (\"array\" given).",
            "parameters": {
                "hint": "Failed to create object because the class misses the \"approved\" property."
            }
        }
    ]
}
JSON,
            'expectedStatusCode' => 400,
        ];

        yield 'validation error json' => [
            'format' => 'json',
            'content' => <<<'JSON'
{
    "comment": "",
    "approved": true
}
JSON,
            'expectedResponse' => <<<'JSON'
{
    "type": "https:\/\/symfony.com\/errors\/validation",
    "title": "Validation Failed",
    "detail": "comment: This value should not be blank.\ncomment: This value is too short. It should have 10 characters or more.",
    "violations": [
        {
            "propertyPath": "comment",
            "title": "This value should not be blank.",
            "parameters": {
                "{{ value }}": "\"\""
            },
            "type": "urn:uuid:c1051bb4-d103-4f74-8988-acbcafc7fdc3"
        },
        {
            "propertyPath": "comment",
            "title": "This value is too short. It should have 10 characters or more.",
            "parameters": {
                "{{ value }}": "\"\"",
                "{{ limit }}": "10"
            },
            "type": "urn:uuid:9ff3fdc4-b214-49db-8718-39c315e33d45"
        }
    ]
}
JSON,
            'expectedStatusCode' => 400,
        ];

        yield 'validation error xml' => [
            'format' => 'xml',
            'content' => <<<'XML'
<request>
    <comment>H</comment>
    <approved>false</approved>
</request>
XML,
            'expectedResponse' => <<<'XML'
<?xml version="1.0"?>
<response>
    <type>https://symfony.com/errors/validation</type>
    <title>Validation Failed</title>
    <detail>comment: This value is too short. It should have 10 characters or more.</detail>
    <violations>
        <propertyPath>comment</propertyPath>
        <title>This value is too short. It should have 10 characters or more.</title>
        <parameters>
            <item key="{{ value }}">"H"</item>
            <item key="{{ limit }}">10</item>
        </parameters>
        <type>urn:uuid:9ff3fdc4-b214-49db-8718-39c315e33d45</type>
    </violations>
</response>
XML,
            'expectedStatusCode' => 400,
        ];
    }
}

class WithMapQueryStringController
{
    public function __invoke(#[MapQueryString] QueryString $query): Response
    {
        return new JsonResponse(
            ['filter' => ['status' => $query->filter->status, 'quantity' => $query->filter->quantity]],
        );
    }
}

class WithMapRequestContentFromJsonController
{
    public function __invoke(#[MapRequestContent] RequestContent $content): Response
    {
        return new JsonResponse(['comment' => $content->comment, 'approved' => $content->approved]);
    }
}

class WithMapRequestContentFromXmlController
{
    public function __invoke(#[MapRequestContent(format: 'xml')] RequestContent $content): Response
    {
        return new Response(
            <<<XML
<response>
    <comment>{$content->comment}</comment>
    <approved>{$content->approved}</approved>
</response>
XML
        );
    }
}

class QueryString
{
    public function __construct(
        #[Assert\Valid]
        public readonly Filter $filter,
    ) {
    }
}

class Filter
{
    public function __construct(
        public readonly string $status,
        #[Assert\LessThan(10)]
        public readonly int $quantity,
    ) {
    }
}

class RequestContent
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 10)]
        public readonly string $comment,
        public readonly bool $approved,
    ) {
    }
}
