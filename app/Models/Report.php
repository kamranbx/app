<?php

namespace App\Models;

use App\Policies\ReportPolicy;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(ReportPolicy::class)]
class Report extends Model
{
    protected $guarded = ['id'];
}
