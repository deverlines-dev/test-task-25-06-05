<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;


/**
 * @property-read int $id
 *
 * @property-read int $ext_id
 * @property-read string $name
 * @property-read CarbonImmutable $date
 */
class UserImport extends Model
{
    protected $casts = [
        'ext_id' => 'int',
        'name' => 'string',
        'date' => 'immutable_date'
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getExtId(): int
    {
        return $this->ext_id;
    }

    public function setExtId(int $ext_id): static
    {
        $this->ext_id = $ext_id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): CarbonImmutable
    {
        return $this->date;
    }

    public function setDate(CarbonImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }


}
