<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LivraisonsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['livraisons'];
    }

    public function headings(): array
    {
        return [
            'Type Document',
            'Reference',
            'Livreur',
            'Utilisateur',
            'Date Livraison',
            'Montant Livraison',
            'Lieu de Livraison'
        ];
    }

    public function map($livraison): array
    {
        return [
            $livraison->type_document,
            $livraison->reference ?? 'N/A',
            $livraison->livreur ? $livraison->livreur->name . ' ' . $livraison->livreur->prenom : 'Non attribué',
            $livraison->user ? $livraison->user->name . ' ' . $livraison->user->prenom : 'N/A',
            $livraison->updated_at->format('d/m/Y H:i'),
            $livraison->montant_livraison ?? 0,
            $livraison->lieu_livraison ?? 'Non spécifié'
        ];
    }
}