<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedContent extends Model
{
    use HasFactory;

    protected $table = 'reported_content';

    protected $fillable = [
        'content_type',
        'content_id',
        'reporter_id',
        'report_reason',
        'report_date',
        'status',
        'review_date',
        'reviewer_id',
        'resolution_notes'
    ];
}
