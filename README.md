# Eloquent mongo search
Custom eloquent scope that allows mongo-like searching

### Installing

1) Run `composer require nordsec/eloquent-mongo-search`

2) Add the following namespace to your eloquent model:

```
use Nordsec\EloquentMongoSearch\WhereMongoFilter;
```

2) Add a scope method to your eloquent model:

```
    /**
     * @param Builder $query
     * @param array $array
     * @param array $allowedColumns
     * @param array $allowedRelations
     *
     * @return Builder|Model
     */
    public function scopeWhereFilter($query, $array, $allowedColumns = null, $allowedRelations = [])
    {
        $filterFactory = new FilterFactory();
        $filter = $filterFactory->create($conditions, $allowedColumns, $allowedRelations);

        $instance = new WhereMongoFilter();

        return $instance->scopeWhereFilter($query, $filter);
    }
```

### Use cases

#### Search using $in

```php
$client->sendGet([
    'query' => [
        'filters' => [
            'id' => [
                '$in' => [1, 2, 3, 4, 5],
            ]
        ]
    ]
])->getResponse();
```
