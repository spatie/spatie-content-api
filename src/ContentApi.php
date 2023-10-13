<?php

namespace Spatie\ContentApi;

use Illuminate\Cache\Repository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\ContentApi\Data\Post;

class ContentApi
{
    const BASE_URL = 'https://content.spatie.be/api';

    private static function getCache(): Repository
    {
        $cache = Cache::store();

        if ($cache->supportsTags()) {
            $cache = $cache->tags(['content_api']);
        }

        return $cache;
    }

    public static function getPosts(string $product, int $page = 1, int $perPage = 20, string $sort = '-date'): Paginator
    {
        return static::getCache()
            ->remember(
                key: "posts-{$product}-{$page}-{$perPage}",
                ttl: now()->addHour(),
                callback: function () use ($sort, $page, $perPage, $product) {
                    $response = Http::get(static::BASE_URL.'/collections/posts/entries', [
                        'filter' => [
                            'product' => $product,
                        ],
                        'limit' => $perPage,
                        'page' => $page,
                        'sort' => $sort,
                    ])->json();

                    return new Paginator(
                        items: collect($response['data'])->map(function (array $post) {
                            return Post::fromResponse($post);
                        }),
                        perPage: $perPage,
                        currentPage: $page,
                    );
                }
            );
    }

    public static function getPost(string $product, string $slug): ?Post
    {
        return static::getCache()
            ->remember(
                key: "post-{$product}-{$slug}",
                ttl: now()->addHour(),
                callback: function () use ($slug, $product) {
                    $post = Http::get(self::BASE_URL.'/collections/posts/entries', [
                        'filter' => [
                            'product' => $product,
                            'slug' => $slug,
                        ],
                    ])->json('data.0');

                    if (! $post) {
                        return null;
                    }

                    return Post::fromResponse($post);
                }
            );
    }
}
