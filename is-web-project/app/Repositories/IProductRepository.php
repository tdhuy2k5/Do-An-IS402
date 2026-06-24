<?php

namespace App\Repositories;

interface IProductRepository
{
    public function getTitleRecomendedProducts(int $limit, int $last_id = 0);

    public function getTitleProductsByCategory(string $slug, int $limit, int $last_id = 0);

    public function getProductByID(int $product_id);

    public function calculatePriceForListProductVariants(array $products);

    public function searchMobile(
        ?string $slug = null,
        ?string $keyword = null,
        ?array $color = null,
        ?array $ram = null,
        ?array $storage = null,
        ?array $processor = null,
        ?array $display = null,
        ?float $min_price = null,
        ?float $max_price = null,
        int $last_id = 0,
        int $limit = 20,
        string $sort = 'desc'
    );

    public function searchTV(
        ?string $slug = null,
        ?string $keyword = null,
        ?array $resolution = null,
        ?array $technology = null,
        ?array $processor = null,
        ?array $screenSize = null,
        ?float $min_price = null,
        ?float $max_price = null,
        int $last_id = 0,
        int $limit = 20,
        string $sort = 'desc'
    );

    public function searchComputing(
        ?string $slug = null,
        ?string $keyword = null,
        ?array $battery = null,
        ?array $graphics = null,
        ?array $ram = null,
        ?array $storage = null,
        ?array $processor = null,
        ?array $color = null,
        ?float $min_price = null,
        ?float $max_price = null,
        int $last_id = 0,
        int $limit = 20,
        string $sort = 'desc'
    );

    public function searchAll(
        ?string $keyword = null,
        ?float $min_price = null,
        ?float $max_price = null,
        int $last_id = 0,
        int $limit = 20,
        string $sort = 'none',
        ?array $child_slugs = null
    );

    public function getProductImages(int $product_id): array;
}
