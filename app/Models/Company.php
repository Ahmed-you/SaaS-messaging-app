<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'slug', 'contact_email', 'status'])]
class Company extends Model
{
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class)
            ->withPivot('enabled_at')
            ->withTimestamps();
    }

    public function hasModule(string $key): bool
    {
        return $this->modules->contains('key', $key);
    }
}
