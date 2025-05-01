<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    protected $table = 'personal_access_tokens';  // Убедись, что имя таблицы правильно указано
    protected $guarded = [];
}
