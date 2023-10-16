<?php

namespace Spatie\ContentApi;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\ContentApi\Data\Post;

class ContentApi
{
    const BASE_URL = 'https://content.spatie.be/api';

    public static function getPosts(string $product, int $page = 1, int $perPage = 20, string $sort = '-date'): Paginator
    {
        $response = Http::get(static::BASE_URL.'/collections/posts/entries', [
            'filter' => [
                'product' => $product,
            ],
            'limit' => $perPage,
            'page' => $page,
            'sort' => $sort,
        ]);

        if (! $response->successful()) {
            report($response->toException());

            if ($posts = Cache::get("posts-{$product}-{$page}-{$perPage}-{$sort}")) {
                return $posts;
            }

            return new Paginator([], $perPage, $page);
        }

        $posts = new Paginator(
            items: collect($response['data'])->filter()->map(function (array $post) {
                return Post::fromResponse($post);
            }),
            perPage: $perPage,
            currentPage: $page,
        );

        Cache::put("posts-{$product}-{$page}-{$perPage}-{$sort}", $posts);

        return $posts;
    }

    public static function getPost(string $product, string $slug): ?Post
    {
        $response = Http::get(self::BASE_URL.'/collections/posts/entries', [
            'filter' => [
                'product' => $product,
                'slug' => $slug,
            ],
        ]);

        if ($response->serverError()) {
            report($response->toException());

            return Cache::get("post-{$product}-{$slug}");
        }

        if (! $postData = $response->json('data.0')) {
            return null;
        }

        $post = Post::fromResponse($postData);

        Cache::put("post-{$product}-{$slug}", $post);

        return $post;
    }
}
