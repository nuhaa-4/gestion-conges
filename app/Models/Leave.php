<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    // On autorise Laravel à remplir ces colonnes depuis un formulaire
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'status',
        'manager_comment',
        'document_path',
    ];

    /**
     * Get the user that owns the leave.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}