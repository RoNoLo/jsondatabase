<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

class QuerySimpleEqualTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     */
    public function testRequestingDocumentsVerySimple()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                "age" => 20,
            ])
            ->execute()
        ;

        $expected = 51;

        $this->assertEquals($expected, $result->count());
    }
}