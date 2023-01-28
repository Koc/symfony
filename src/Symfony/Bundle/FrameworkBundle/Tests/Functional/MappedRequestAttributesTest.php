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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestContent;

class MappedRequestAttributesTest extends AbstractWebTestCase
{
    public function testMapQueryString()
    {
        $client = self::createClient(['test_case' => 'MappedRequestAttributes']);

        $client->request('GET', '/map-query-string', ['filter' => ['status' => 'approved', 'quantity' => 4]]);

        self::assertEquals('filter.status=approved,filter.quantity=4', $client->getResponse()->getContent());
    }

    public function testMapRequestContent()
    {
        $client = self::createClient(['test_case' => 'MappedRequestAttributes']);

        $client->request(
            'POST',
            '/map-request-content',
            [],
            [],
            [],
            <<<'JSON'
{
    "comment": "Hello everyone!"
}
JSON
        );

        self::assertEquals('comment=Hello everyone!', $client->getResponse()->getContent());
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
        return new Response("comment={$content->comment}");
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
    public function __construct(public readonly string $comment)
    {
    }
}
