<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $appends = ['fa_icon'];

    // HUBUNGAN: Satu Kategori punya banyak Soal
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // ACCESSOR: Mapping ikon legacy (Fi/Gi) ke FontAwesome (fas)
    public function getFaIconAttribute()
    {
        $mapping = [
            // Security & Intel
            'FiKey' => 'fas fa-key',
            'FiShield' => 'fas fa-shield-alt',
            'FiLock' => 'fas fa-lock',
            'GiBrain' => 'fas fa-brain',
            
            // Tech & Science
            'FiCpu' => 'fas fa-microchip',
            'GiDna1' => 'fas fa-dna',
            'FiGlobe' => 'fas fa-globe',
            'FiRadio' => 'fas fa-broadcast-tower',
            'FiServer' => 'fas fa-server',
            'FiCode' => 'fas fa-code',
            
            // Arts & Others
            'GiBookCover' => 'fas fa-book-open',
            'GiMusicalNotes' => 'fas fa-music',
            'GiPalette' => 'fas fa-palette',
            'GiRunningShoe' => 'fas fa-running',
            'GiSmartphone' => 'fas fa-mobile-alt',
            'GiSatelliteCommunication' => 'fas fa-satellite',
        ];

        // Return mapped icon, or original if already FA, or default
        return $mapping[$this->icon_class] ?? ($this->icon_class ?: 'fas fa-book');
    }
}