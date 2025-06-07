<?php

namespace App\Services\Import\ImportUsersXlsxTable;

readonly class ImportUsersXlsxTableValueValidatorService
{
    public function getTableItemValidationErrors(array $tableItem): ?array
    {
        $errors = [
            'id'   => $this->validateId($tableItem['id']),
            'name' => $this->validateName($tableItem['name']),
            'date' => $this->validateDate($tableItem['date']),
        ];

        foreach ($errors as $error) {
            if (!empty($error)) {
                return $errors;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    public function validateId(mixed $id): array
    {
        $errors = [];

        if (!is_string($id)) {
            $errors[] = "не является строкой с целыми числами";

            return $errors;
        }

        if (!ctype_digit($id)) {
            $errors[] = "$id - не является целым числом";
        }

        return $errors;
    }

    /**
     * @return array<int, string>
     */
    public function validateName(mixed $name): array
    {
        $errors = [];

        if (!is_string($name)) {
            $errors[] = "$name не является строкой";

            return $errors;
        }

        $isNamePattern = '/[a-zA-Zа-яА-ЯёЁ]/u';
        if (!preg_match($isNamePattern, $name)) {
            $errors[] = "$name не содержит ни одной буквы";
        }

        if (mb_strlen($name) > 255) {
            $errors[] = "$name содержит более 255 символов";
        }

        return $errors;
    }

    /**
     * @return array<int, string>
     */
    public function validateDate(mixed $date): array
    {
        $errors = [];

        if (!is_string($date)) {
            $errors[] = "$date не является строкой";

            return $errors;
        }

        $isDatePattern = '/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/';

        if (!preg_match($isDatePattern, $date, $matches)) {
            $errors[] = "$date не удалось определить дату";

            return $errors;
        }

        if (!($matches[1] ?? null) || !($matches[2] ?? null) || !($matches[3] ?? null)) {
            $errors[] = "$date заполнена не корректно";

            return $errors;
        }

        if (!checkdate((int)$matches[2], (int)$matches[1], (int)$matches[3])) {
            $errors[] = "$date не является корректной датой";

            return $errors;
        }

        return $errors;
    }
}
