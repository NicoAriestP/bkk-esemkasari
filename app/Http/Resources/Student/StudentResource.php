<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nisn' => $this->nisn ?? null,
            'gender' => $this->gender ?? null,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'address' => $this->address ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
