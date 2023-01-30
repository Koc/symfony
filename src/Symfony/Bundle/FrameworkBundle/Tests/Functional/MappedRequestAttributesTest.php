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
    public function testMapQueryString()
    {
        //todo: add data provider, test validation?
        $client = self::createClient(['test_case' => 'MappedRequestAttributes']);

        $client->request('GET', '/map-query-string', ['filter' => ['status' => 'approved', 'quantity' => '4']]);

        self::assertSame('filter.status=approved,filter.quantity=4', $client->getResponse()->getContent());
    }

    /**
     * @dataProvider mapRequestContentProvider
     */
    public function testMapRequestContent(string $content, string $expectedResponse, int $expectedStatusCode)
    {
        $client = self::createClient(['test_case' => 'MappedRequestAttributes']);

        $client->request(
            'POST',
            '/map-request-content',
            [],
            [],
            [],
            $content
        );

        self::assertJsonStringEqualsJsonString($expectedResponse, $client->getResponse()->getContent());
        self::assertSame($expectedStatusCode, $client->getResponse()->getStatusCode());
    }

    public static function mapRequestContentProvider(): iterable
    {
        yield 'valid json' => [
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

        yield 'missing property' => [
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

        yield 'validation error' => [
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
    }
}

class WithMapQueryStringController
{
    public function __invoke(#[MapQueryString] QueryString $query): Response
    {
        return new Response("filter.status={$query->filter->status},filter.quantity={$query->filter->quantity}");
    }
}

class WithMapRequestContentController
{
    public function __invoke(#[MapRequestContent] RequestContent $content): Response
    {
        return new JsonResponse(['comment' => $content->comment, 'approved' => $content->approved]);
    }
}

class QueryString
{
    public function __construct(
        public readonly Filter $filter,
    ) {
    }
}

class Filter
{
    public function __construct(public readonly string $status, public readonly int $quantity)
    {
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
