<?php

namespace Tests\Feature\UserImports;

use Tests\TestCase;

/**
 * Нужно заполнить БД перед тестом
 */
class UserImportsControllerTest extends TestCase
{
    public function testGroupByDate(): void
    {
        $response = $this->getJson('/rest/user-import/group-by-date');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'date',
                    'items' => [
                        '*' => [
                            'extId',
                            'name',
                        ]
                    ]
                ]
            ]);
    }

    public function testGroupByDatePerPage50(): void
    {
        $query = http_build_query([
            'perPage' => 50
        ]);

        $response = $this->getJson("/rest/user-import/group-by-date?$query");

        $response
            ->assertStatus(200)
            ->assertJsonCount(50);
    }

    public function testGroupByDatePerformance(): void
    {


        $startMemory = memory_get_usage();
        $peakStartMemory = memory_get_peak_usage();

        $query = http_build_query([
            'perPage' => 100
        ]);
        $this->getJson("/rest/user-import/group-by-date?$query");

        $query = http_build_query([
            'page' => 2,
            'perPage' => 100
        ]);
        $this->getJson("/rest/user-import/group-by-date?$query");

        $usedMemory     = (memory_get_usage()      - $startMemory)     / 1024 / 1024;
        $usedPeakMemory = (memory_get_peak_usage() - $peakStartMemory) / 1024 / 1024;

        $this->assertLessThan(2.00, $usedMemory);
        $this->assertLessThan(2.00, $usedPeakMemory);
    }
}
