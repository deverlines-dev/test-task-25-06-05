<?php

namespace Tests\Unit\Services\Import\ImportUsersXlsxTable;

use App\Services\Import\ImportUsersXlsxTable\ImportUsersXlsxTableValueValidatorService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class ImportUsersXlsxTableValueValidatorServiceTest extends TestCase
{
    private ImportUsersXlsxTableValueValidatorService $importUsersXlsxTableValueValidatorService;

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->importUsersXlsxTableValueValidatorService = $this->app->make(ImportUsersXlsxTableValueValidatorService::class);
    }

    public function testSuccessValidateId(): void
    {
        $errors = $this->importUsersXlsxTableValueValidatorService->validateId('1');
        $this->assertEmpty($errors);

        $errors = $this->importUsersXlsxTableValueValidatorService->validateId('05');
        $this->assertEmpty($errors);
    }

    public function tesFailValidateId(): void
    {
        $errors = $this->importUsersXlsxTableValueValidatorService->validateId('');
        $this->assertNotEmpty($errors);

        $errors = $this->importUsersXlsxTableValueValidatorService->validateId('б');
        $this->assertNotEmpty($errors);
    }

    public function testSuccessValidateName(): void
    {
        $errors = $this->importUsersXlsxTableValueValidatorService->validateName('Dmitry Koshkin');
        $this->assertEmpty($errors);

        $errors = $this->importUsersXlsxTableValueValidatorService->validateName('Tatyana');
        $this->assertEmpty($errors);
    }

    public function testFailValidateName(): void
    {
        $errors = $this->importUsersXlsxTableValueValidatorService->validateName('');
        $this->assertNotEmpty($errors);

        $errors = $this->importUsersXlsxTableValueValidatorService->validateName('12');
        $this->assertNotEmpty($errors);

        $errors = $this->importUsersXlsxTableValueValidatorService->validateName(str_repeat('a', 600));
        $this->assertNotEmpty($errors);
    }

    public function testSuccessValidateDate(): void
    {
        $errors = $this->importUsersXlsxTableValueValidatorService->validateDate('01.01.2000');
        $this->assertEmpty($errors);

        $errors = $this->importUsersXlsxTableValueValidatorService->validateDate('01.01.1000');
        $this->assertEmpty($errors);
    }

    public function testSuccessFailValidateDate(): void
    {
        $errors = $this->importUsersXlsxTableValueValidatorService->validateDate('31.02.2000');
        $this->assertNotEmpty($errors);

        $errors = $this->importUsersXlsxTableValueValidatorService->validateDate('01.01');
        $this->assertNotEmpty($errors);
    }

}
