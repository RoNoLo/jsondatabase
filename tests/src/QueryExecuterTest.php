<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use RoNoLo\JsonQuery\JsonQuery;

class QueryExecuterTest extends TestBase
{
    public function testRequestingDocumentsVerySimple()
    {
        $conditions = (new QueryExecuter())->parse([
            "age" => 20,
        ]);

        $expected = function (JsonQuery $jsonQuery) {
            return $jsonQuery->get("age") == 20;
        };

        $this->assertEquals($expected, $conditions->getSelectors());
    }

    public function testRequestingDocumentsVerySimpleArray()
    {
        $conditions = (new QueryExecuter())->parse([
            "age" => 20,
            "phone" => "1234567",
            "name" => "Thomas"
        ]);

        $expected = function (JsonQuery $jsonQuery) {
            $list = [];
            $list[] = function (JsonQuery $jsonQuery) {
                return $jsonQuery->get("age") == 20;
            };
            $list[] = function (JsonQuery $jsonQuery) {
                return $jsonQuery->get("phone") == "1234567";
            };
            $list[] = function (JsonQuery $jsonQuery) {
                return $jsonQuery->get("name") == "Thomas";
            };

            foreach ($list as $condition) {
                $result = $condition($jsonQuery);

                if (!$result) {
                    return false;
                }
            }

            return true;
        };

        $expected = [
            ['$eq', 'age', 20],
            ['$eq', 'phone', "1234567"],
            ['$eq', 'name', "Thomas"],
        ];

        $this->assertEquals($expected, $conditions);
    }

    public function testRequestingDocumentsSimpleWithNot()
    {
        $conditions = (new QueryExecuter())->parse([
            '$not' => [
                "age" => [
                    '$gt' => 20,
                    '$lt' => 40,
                ],
                "phone" => [
                    '$ne' => true
                ],
            ]
        ]);

        $expected = [
            [Query::LOGIC_NOT => [
                ['$gt', 'age', 20],
                ['$lt', 'age', 40],
                ['$ne', 'phone', true]
            ]]
        ];

        $this->assertEquals($expected, $conditions);
    }

    public function testRequestingDocumentsSimple()
    {
        $conditions = (new QueryExecuter())->parse([
            "age" => [
                '$gt' => 20,
                '$lt' => 40,
            ],
            "phone" => [
                '$ne' => true
            ],
        ]);

        $expected = [
            ['$gt', 'age', 20],
            ['$lt', 'age', 40],
            ['$ne', 'phone', true]
        ];

        $this->assertEquals($expected, $conditions);
    }

    public function testRequestingDocumentsSimpleOnlyOr()
    {
        $conditions = (new QueryExecuter())->parse([
            '$or' => [
                [
                    "age" => [
                        '$eq' => 20,
                    ],
                    "phone" => [
                        '$eq' => "12345",
                    ]
                ],
                [
                    "age" => [
                        '$eq' => 40
                    ]
                ]
            ]
        ]);

        $expected = [
            [Query::LOGIC_OR => [
                [
                    ['$eq', 'age', 20],
                    ['$eq', 'phone', '12345']
                ],
                [
                    ['$eq', 'age', 40]
                ],
            ]]
        ];

        $this->assertEquals($expected, $conditions);
    }

    public function testRequestingDocumentsSimpleDeepOr()
    {
        $conditions = (new QueryExecuter())->parse([
            "name" => [
                '$eq' => "Thomas"
            ],
            '$or' => [
                [
                    "age" => [
                        '$eq' => 20,
                    ]
                ],
                [
                    "age" => [
                        '$eq' => 40
                    ]
                ]
            ]
        ]);

        $expected = [
            ['$eq', 'name', "Thomas"],
            [Query::LOGIC_OR => [
                [['$eq', 'age', 20]],
                [['$eq', 'age', 40]],
            ]]
        ];

        $this->assertEquals($expected, $conditions);
    }

//    public function testRequestingDocumentsSimpleDeepAnd()
//    {
//        $conditions = (new QueryExecuter())->parse([
//            '$and' => [
//                [
//                    "age" => [
//                        '$gt' => 20,
//                    ]
//                ],
//                [
//                    "age" => [
//                        '$lt' => 40
//                    ]
//                ]
//            ],
//        ]);
//
//        $expected = [
//            Query::LOGIC_AND => [
//                ['$gt', 'age', 20],
//                ['$lt', 'age', 40],
//            ]
//        ];
//
//        $this->assertEquals($expected, $conditions);
//    }

//    public function testRequestingDocumentsWithOrQuery()
//    {
//        $config = new Config();
//        $repo = new Repository('test', $config, $this->datastoreAdapter);
//        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');
//
//        $query = $repo->query();
//        $result = $query
//            ->find([
//                "selector" => [
//                    [
//                        "or" => [
//                            [
//                                "age" => [
//                                    "gt" => 10,
//                                    "lt" => 20,
//                                ],
//                                "phone" => [
//                                    "ne" => true
//                                ],
//                            ],
//                            [
//                                "age" => [
//                                    "gt" => 60
//                                ]
//                            ]
//                        ],
//                    ]
//                ],
//            ])
//            ->execute()
//        ;
//
//        $this->assertEquals(2, count($result));
//    }

}