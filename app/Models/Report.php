<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'code',
        'resident_id',
        'report_category_id',
        'title',
        'description',
        'image',
        'latitude',
        'longitude',
        'address',
    ];

    public function resident()
    {
        // One report has one resident
        return $this->belongsTo(Resident::class);
    }

    public function reportCategory()
    {
        // One report has one report category
        return $this->belongsTo(ReportCategory::class);
    }

    public function reportStatuses()
    {
        // One report has many report statuses
        return $this->hasMany(ReportStatus::class);
    }
}
