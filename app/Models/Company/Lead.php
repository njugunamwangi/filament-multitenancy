<?php

namespace App\Models\Company;

use App\Models\Company;
use App\Models\Lead as ModelsLead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'company_leads';

    public function company(): BelongsTo
    {
         return $this->belongsTo(Company::class);
    }

    public function lead(): BelongsTo
    {
         return $this->belongsTo(ModelsLead::class);
    }
}
