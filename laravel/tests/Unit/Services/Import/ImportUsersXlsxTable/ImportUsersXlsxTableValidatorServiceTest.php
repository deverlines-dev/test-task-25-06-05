<?php

namespace Tests\Unit\Services\Import\ImportUsersXlsxTable;

use App\Services\Import\ImportUsersXlsxTable\ImportUsersXlsxTableValidatorService;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class ImportUsersXlsxTableValidatorServiceTest extends TestCase
{
    private ImportUsersXlsxTableValidatorService $importUsersXlsxTableValidatorService;

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->importUsersXlsxTableValidatorService = $this->app->make(ImportUsersXlsxTableValidatorService::class);
    }

    /**
     * @throws Exception
     */
    public function testSuccessValidateTableHeaders(): void
    {
        $this->importUsersXlsxTableValidatorService->validateTableHeaders(
            tableHeaders: ['id', 'name', 'date'],
            requiredHeaders: ['id', 'name', 'date']
        );
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function tesFailValidateTableHeaders(): void
    {
        $hasError = false;

        try {
            $this->importUsersXlsxTableValidatorService->validateTableHeaders(
                tableHeaders: ['id', 'name', 'date'],
                requiredHeaders: ['id', 'name', 'dates']
            );

        } catch (\Exception $e) {
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }
}
