<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccJurnal extends Model
{
    use HasFactory;

    protected $table = 'tbl_accjurnal';
    public $timestamps = false;
    protected $guarded = [];
}
