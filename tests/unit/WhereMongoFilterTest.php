<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Capsule\Manager as Capsule;
use Nordsec\EloquentMongoSearch\Factories\Filters\FilterFactory;
use Nordsec\EloquentMongoSearch\WhereMongoFilter;
use PHPUnit\Framework\TestCase;
use Tests\Models\Payer;
use Tests\Models\Payment;

class WhereMongoFilterTest extends TestCase
{
    private $whereMongoFilter;

    public function setUp(): void
    {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => '',
            'database' => '',
            'username' => '',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ]);
        $capsule->bootEloquent();
        $this->whereMongoFilter = new WhereMongoFilter();
    }

    /**
     * @dataProvider whereFilterDataProvider
     */
    public function testScopeWhereFilter(
        array $conditions,
        string $sql,
        ?array $bindings,
        ?array $allowedColumns = null,
        ?array $allowedRelations = []
    ): void {
        $filterFactory = new FilterFactory();
        $filter = $filterFactory->create($conditions, $allowedColumns, $allowedRelations);

        $data = $this->whereMongoFilter->scopeWhereFilter(Payment::query(), $filter);
        $this->assertEquals($sql, $data->toSql());
        $this->assertEquals($data->getBindings(), $bindings);
    }

    public function whereFilterDataProvider(): array
    {
        return
            [
                [
                    ['payments.id' => 15],
                    'select * from `payments` where `payments`.`id` = ?',
                    [15],
                ],
                [
                    ['payments.id' => '15'],
                    'select * from `payments` where `payments`.`id` = ?',
                    ['15'],
                ],
                [
                    ['payments.id' => 15, 'payments.status' => 'bar'],
                    'select * from `payments` where `payments`.`status` = ?',
                    ['bar'],
                    ['payments.status'],
                ],
                [
                    ['payments.id' => ['$gt' => 15]],
                    'select * from `payments` where `payments`.`id` > ?',
                    [15],
                ],
                [
                    ['payments.id' => ['$gte' => 15]],
                    'select * from `payments` where `payments`.`id` >= ?',
                    [15],
                ],
                [
                    ['payments.id' => ['$lt' => 15]],
                    'select * from `payments` where `payments`.`id` < ?',
                    [15],
                ],
                [
                    ['payments.id' => ['$lte' => 15]],
                    'select * from `payments` where `payments`.`id` <= ?',
                    [15],
                ],
                [
                    ['payments.id' => ['$ne' => 15]],
                    'select * from `payments` where `payments`.`id` != ?',
                    [15],
                ],
                [
                    [
                        'payments.id' => [
                            ['$lte' => 15],
                            ['$gte' => 20],
                        ],
                    ],
                    'select * from `payments` where `payments`.`id` <= ? and `payments`.`id` >= ?',
                    [15, 20],
                ],
                [
                    [
                        '$or' => [
                            ['payments.id' => ['$lte' => 15]],
                            ['payments.id' => ['$gte' => 20]],
                        ],
                    ],
                    'select * from `payments` where (`payments`.`id` <= ? or `payments`.`id` >= ?)',
                    [15, 20],
                ],
                [
                    [
                        'payments.id' => ['$ne' => 17],
                        '$or' => [
                            ['payments.id' => null],
                            ['payments.id' => ['$gte' => 20]],
                        ],
                    ],
                    'select * from `payments` where `payments`.`id` != ? and (`payments`.`id` is null or `payments`.`id` >= ?)',
                    [17, 20],
                ],
                [
                    ['payments.id' => ['$in' => [15, 16]]],
                    'select * from `payments` where `payments`.`id` in (?, ?)',
                    [15, 16],
                ],
                [
                    ['payments.id' => ['$nin' => [15, 16]]],
                    'select * from `payments` where `payments`.`id` not in (?, ?)',
                    [15, 16],
                ],
                [
                    [
                        '$or' => [
                            ['payments.id' => ['$in' => [15, 16]]],
                            ['payments.id' => ['$nin' => [17, 18]]],
                        ],
                    ],
                    'select * from `payments` where (`payments`.`id` in (?, ?) or `payments`.`id` not in (?, ?))',
                    [15, 16, 17, 18],
                ],
            ];
    }

    /**
     * @dataProvider manyToOneDataProvider
     */
    public function testScopeWhereFilterManyToOne(
        array $conditions,
        string $sql,
        ?array $bindings,
        ?array $allowedColumns = null,
        ?array $allowedRelations = []
    ): void {
        $filterFactory = new FilterFactory();
        $filter = $filterFactory->create($conditions, $allowedColumns, $allowedRelations);

        $data = $this->whereMongoFilter->scopeWhereFilter(Payment::query(), $filter);
        $this->assertEquals($sql, $data->toSql());
        $this->assertEquals($data->getBindings(), $bindings);
    }

    public function manyToOneDataProvider(): array
    {
        return
            [
                [
                    [
                        'payer' => [
                            ['user_id' => 2],
                        ],
                    ],
                    'select * from `payments` where `payments`.`payer_id` in (select `id` from `payers` where `user_id` = ?)',
                    [2],
                    null,
                    null,
                ],
                [
                    [
                        'payer' => [
                            ['user_id' => 2],
                            ['country' => null],
                        ],
                    ],
                    'select * from `payments` where `payments`.`payer_id` in (select `id` from `payers` where `user_id` = ? and `country` is null)',
                    [2],
                    null,
                    null,
                ],
                [
                    [
                        'payer' => [
                            'payers.user_id' => '2',
                        ],
                    ],
                    'select * from `payments` where `payments`.`payer_id` in (select `id` from `payers` where `payers`.`user_id` = ?)',
                    ['2'],
                    null,
                    null,
                ],
                [
                    [
                        'account_metadata' => [],
                        'payer' => [
                            'metadata' => [],
                            'payers.id' => 10,
                            'payers.country' => null,
                        ],
                    ],
                    'select * from `payments` where `payments`.`payer_id` in (select `id` from `payers` where `payers`.`id` = ? and `payers`.`country` is null)',
                    [10],
                    null,
                    null,
                ],
                [
                    [
                        'account_metadata' => [],
                        'payer' => [
                            'metadata' => ['value' => 123],
                            'payers.id' => 10,
                            'payers.country' => null,
                        ],
                    ],
                    'select * from `payments` where `payments`.`payer_id` in (select `id` from `payers` where `payers`.`id` in (select `payers_metadata`.`payer_id` from `payers_metadata` where `value` = ?) and `payers`.`id` = ? and `payers`.`country` is null)',
                    [123, 10],
                    null,
                    null,
                ],
                [
                    [
                        'payer' => [
                            'payers.id' => 10,
                            'payers.country' => null,
                        ],
                    ],
                    'select * from `payments` where `payments`.`payer_id` in (select `id` from `payers` where `payers`.`id` = ? and `payers`.`country` is null)',
                    [10],
                    null,
                    null,
                ],
                [
                    [
                        'status' => [
                            '$in' => [
                                'active',
                                'cancelled',
                                'inactive',
                                'trial'
                            ]
                        ],
                        '$and' => [
                            'payer' => [
                                'user_id' => 20,
                            ]
                        ],
                    ],
                    'select * from `payments` where `status` in (?, ?, ?, ?) and (`payments`.`payer_id` in (select `id` from `payers` where `user_id` = ?))',
                    [
                        'active',
                        'cancelled',
                        'inactive',
                        'trial',
                        20,
                    ],
                    null,
                    null,
                ],
                [
                    [
                        'payments.id' => 10,
                        'payer' => [
                            'payers.id' => 10,
                            'payers.country' => null,
                        ],
                    ],
                    'select * from `payments` where `payments`.`id` = ? and `payments`.`payer_id` in (select `id` from `payers` where `payers`.`id` = ? and `payers`.`country` is null)',
                    [10, 10],
                    null,
                    null,
                ],
                [
                    [
                        '$or' => [
                            ['payments.id' => 1],
                            ['payer' => [
                                'payers.id' => 10,
                            ]],
                            ['payer' => [
                                'payers.country' => null,
                            ]],
                        ],
                    ],
                    'select * from `payments` where (`payments`.`id` = ? or `payments`.`payer_id` in (select `id` from `payers` where `payers`.`id` = ?) or `payments`.`payer_id` in (select `id` from `payers` where `payers`.`country` is null))',
                    [1, 10],
                    null,
                    ['payer'],
                ],
                [
                    [
                        'payer' => [
                            'payers.id' => 10,
                            'payers.country' => null,
                        ],
                    ],
                    'select * from `payments` where `payments`.`payer_id` in (select `id` from `payers` where `payers`.`id` = ? and `payers`.`country` is null)',
                    [10],
                    null,
                    ['payer'],
                ],
                [
                    [
                        'payer' => [
                            'invoices' => [
                                'amounts' => [
                                    'value' => '10',
                                    'currency' => 'USD',
                                ]
                            ]
                        ],
                    ],
                    'select * from `payments` where `payments`.`payer_id` in (select `id` from `payers` where `payers`.`id` in (select `invoices`.`payer_id` from `invoices` where `invoices`.`id` in (select `invoices_amounts`.`invoice_id` from `invoices_amounts` where `value` = ? and `currency` = ?)))',
                    [
                        '10',
                        'USD'
                    ],
                    null,
                    [
                        'payer',
                        'invoices',
                        'payments',
                        'amounts'
                    ],
                ],
            ];
    }

    /**
     * @dataProvider oneToManyDataProvider
     */
    public function testScopeWhereFilterOneToMany(
        array $conditions,
        string $sql,
        ?array $bindings,
        ?array $allowedColumns = null,
        ?array $allowedRelations = []
    ): void {
        $filterFactory = new FilterFactory();
        $filter = $filterFactory->create($conditions, $allowedColumns, $allowedRelations);

        $data = $this->whereMongoFilter->scopeWhereFilter(Payer::query(), $filter);
        $this->assertEquals($sql, $data->toSql());
        $this->assertEquals($data->getBindings(), $bindings);
    }

    public function oneToManyDataProvider(): array
    {
        return
            [
                [
                    [
                        'payments' => [
                            'payment.status' => null,
                        ],
                    ],
                    'select * from `payers` where `payers`.`id` in (select `payments`.`payer_id` from `payments` where `payment`.`status` is null)',
                    [],
                    null,
                    ['payments'],
                ],
            ];
    }

    /**
     * @dataProvider manyToManyDataProvider
     */
    public function testScopeWhereFilterManyToMany(
        array $conditions,
        string $sql,
        ?array $bindings,
        ?array $allowedColumns = null,
        ?array $allowedRelations = []
    ): void {

        $filterFactory = new FilterFactory();
        $filter = $filterFactory->create($conditions, $allowedColumns, $allowedRelations);

        $data = $this->whereMongoFilter->scopeWhereFilter(Payer::query(), $filter);
        $this->assertEquals($sql, $data->toSql());
        $this->assertEquals($data->getBindings(), $bindings);
    }

    public function manyToManyDataProvider(): array
    {
        return
            [
                [
                    [
                        'invoices' => [
                            'invoice.status' => null,
                        ],
                    ],
                    'select * from `payers` where `payers`.`id` in (select `invoices`.`payer_id` from `invoices` where `invoice`.`status` is null)',
                    [],
                    null,
                    ['invoices'],
                ],
            ];
    }
}
