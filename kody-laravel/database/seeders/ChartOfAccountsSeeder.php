<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clear existing accounts to prevent duplicates (since it's a fresh tenant)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Account::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Define the tree exactly matching the scopes in the Account Model
        $chart = [
            [
                'code' => '1',
                'aname' => 'الأصول',
                'is_basic' => 1,
                'children' => [
                    [
                        'code' => '11',
                        'aname' => 'الأصول الثابتة',
                        'is_basic' => 1,
                        'children' => [
                            ['code' => '111', 'aname' => 'العقارات والمباني', 'is_basic' => 0],
                            ['code' => '112', 'aname' => 'السيارات', 'is_basic' => 0],
                            ['code' => '113', 'aname' => 'الأجهزة والمعدات', 'is_basic' => 0],
                        ]
                    ],
                    [
                        'code' => '12',
                        'aname' => 'الأصول المتداولة',
                        'is_basic' => 1,
                        'children' => [
                            [
                                'code' => '121',
                                'aname' => 'الصناديق (النقدية)',
                                'is_basic' => 1,
                                'children' => [
                                    ['code' => '1211', 'aname' => 'الصندوق الرئيسي', 'is_basic' => 0, 'is_fund' => 1],
                                ]
                            ],
                            [
                                'code' => '122',
                                'aname' => 'العملاء',
                                'is_basic' => 1,
                            ],
                            [
                                'code' => '123',
                                'aname' => 'المخزون والمستودعات',
                                'is_basic' => 1,
                                'children' => [
                                    ['code' => '1231', 'aname' => 'المستودع الرئيسي', 'is_basic' => 0, 'is_stock' => 1],
                                ]
                            ],
                            [
                                'code' => '124',
                                'aname' => 'البنوك',
                                'is_basic' => 1,
                                'children' => [
                                    ['code' => '1241', 'aname' => 'البنك الرئيسي', 'is_basic' => 0],
                                ]
                            ],
                        ]
                    ]
                ]
            ],
            [
                'code' => '2',
                'aname' => 'الخصوم (الالتزامات)',
                'is_basic' => 1,
                'children' => [
                    [
                        'code' => '21',
                        'aname' => 'الالتزامات المتداولة',
                        'is_basic' => 1,
                        'children' => [
                            [
                                'code' => '211',
                                'aname' => 'الموردين',
                                'is_basic' => 1,
                            ],
                            [
                                'code' => '212',
                                'aname' => 'الدائنون المتنوعون',
                                'is_basic' => 1,
                            ],
                            [
                                'code' => '213',
                                'aname' => 'الموظفون (عهد ورواتب)',
                                'is_basic' => 1,
                            ],
                        ]
                    ]
                ]
            ],
            [
                'code' => '3',
                'aname' => 'الإيرادات',
                'is_basic' => 1,
                'children' => [
                    [
                        'code' => '31',
                        'aname' => 'إيرادات المبيعات',
                        'is_basic' => 1,
                        'children' => [
                            ['code' => '311', 'aname' => 'مبيعات نقدية', 'is_basic' => 0],
                            ['code' => '312', 'aname' => 'مبيعات آجلة', 'is_basic' => 0],
                        ]
                    ],
                    [
                        'code' => '32',
                        'aname' => 'إيرادات أخرى',
                        'is_basic' => 1,
                        'children' => [
                            ['code' => '321', 'aname' => 'إيرادات خدمات', 'is_basic' => 0],
                        ]
                    ]
                ]
            ],
            [
                'code' => '4',
                'aname' => 'المصروفات',
                'is_basic' => 1,
                'children' => [
                    [
                        'code' => '41',
                        'aname' => 'تكلفة المبيعات',
                        'is_basic' => 1,
                        'children' => [
                            ['code' => '411', 'aname' => 'تكلفة البضاعة المباعة', 'is_basic' => 0],
                        ]
                    ],
                    [
                        'code' => '42',
                        'aname' => 'المصروفات التشغيلية',
                        'is_basic' => 1,
                    ],
                    [
                        'code' => '43',
                        'aname' => 'المصروفات الإدارية',
                        'is_basic' => 1,
                        'children' => [
                            ['code' => '431', 'aname' => 'رواتب وأجور', 'is_basic' => 0],
                            ['code' => '432', 'aname' => 'إيجارات', 'is_basic' => 0],
                        ]
                    ],
                    [
                        'code' => '44',
                        'aname' => 'المصروفات العمومية',
                        'is_basic' => 1,
                        'children' => [
                            ['code' => '441', 'aname' => 'مصروفات بنكية', 'is_basic' => 0],
                            ['code' => '442', 'aname' => 'مصروفات أخرى', 'is_basic' => 0],
                        ]
                    ]
                ]
            ],
            [
                'code' => '5',
                'aname' => 'حقوق الملكية',
                'is_basic' => 1,
                'children' => [
                    [
                        'code' => '51',
                        'aname' => 'رأس المال',
                        'is_basic' => 0,
                    ],
                    [
                        'code' => '52',
                        'aname' => 'الأرباح المحتجزة',
                        'is_basic' => 0,
                    ]
                ]
            ],
        ];

        // 3. Process the tree recursively
        $this->seedAccounts($chart, 0);
    }

    /**
     * Recursive function to insert accounts and maintain parent_id
     */
    private function seedAccounts(array $accounts, int $parentId = 0)
    {
        foreach ($accounts as $data) {
            // Extract children before creating the account
            $children = $data['children'] ?? [];
            unset($data['children']);

            // Set parent_id and defaults
            $data['parent_id'] = $parentId;
            $data['is_stock'] = $data['is_stock'] ?? 0;
            $data['is_fund'] = $data['is_fund'] ?? 0;
            $data['debit'] = 0;
            $data['credit'] = 0;
            $data['balance'] = 0;
            $data['start_balance'] = 0;
            $data['isdeleted'] = 0;
            $data['editable'] = 1;
            $data['secret'] = 0;
            $data['constant'] = 0;
            $data['crtime'] = now();
            $data['mdtime'] = now();

            // Create the account
            $account = Account::create($data);

            // Recursively seed children
            if (!empty($children)) {
                $this->seedAccounts($children, $account->id);
            }
        }
    }
}
