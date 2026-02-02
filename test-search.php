<?php
/**
 * سكريبت اختبار البحث السريع
 * يمكن تشغيله من المتصفح: /test-search.php?q=الصلاة
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use Elasticsearch\ClientBuilder;

$elasticsearchHost = env('ELASTICSEARCH_HOST', 'http://145.223.98.97:9201');
$query = request()->input('q', 'الصلاة');
$page = request()->input('page', 1);
$size = request()->input('size', 10);

try {
    $client = ClientBuilder::create()
        ->setHosts([$elasticsearchHost])
        ->setConnectionPool('\\Elasticsearch\\ConnectionPool\\StaticNoPingConnectionPool')
        ->setRetries(1)
        ->setSSLVerification(false)
        ->build();

    // Check if index exists
    $indexExists = $client->indices()->exists(['index' => 'pages']);
    
    if (!$indexExists) {
        return response()->json([
            'error' => 'Index pages does not exist',
            'status' => 'error'
        ]);
    }

    // Get index count
    $countResult = $client->count(['index' => 'pages']);
    
    // Search
    $params = [
        'index' => 'pages',
        'body' => [
            'query' => [
                'match' => [
                    'content' => [
                        'query' => $query,
                        'operator' => 'and',
                        'fuzziness' => 'AUTO'
                    ]
                ]
            ],
            'from' => ($page - 1) * $size,
            'size' => $size,
            '_source' => ['id', 'page_number', 'book_title', 'content'],
            'highlight' => [
                'fields' => [
                    'content' => [
                        'fragment_size' => 150,
                        'number_of_fragments' => 1,
                        'pre_tags' => ['<mark>'],
                        'post_tags' => ['</mark>']
                    ]
                ]
            ]
        ]
    ];

    $result = $client->search($params);

    return response()->json([
        'status' => 'success',
        'query' => $query,
        'index_total' => $countResult['count'],
        'total' => $result['hits']['total']['value'] ?? 0,
        'took' => $result['took'],
        'current_page' => $page,
        'per_page' => $size,
        'results' => array_map(function($hit) {
            return [
                'id' => $hit['_source']['id'],
                'book_title' => $hit['_source']['book_title'] ?? 'Unknown',
                'page_number' => $hit['_source']['page_number'],
                'score' => $hit['_score'],
                'content' => $hit['_source']['content'],
                'highlight' => $hit['highlight']['content'][0] ?? null
            ];
        }, $result['hits']['hits'] ?? [])
    ]);

} catch (\Exception $e) {
    return response()->json([
        'error' => $e->getMessage(),
        'status' => 'error',
        'trace' => $e->getTraceAsString()
    ]);
}
