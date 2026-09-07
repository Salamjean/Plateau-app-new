@extends('errors::minimal')

@section('title', 'Page Expirée')

@section('code', '419')

@section('icon')
    <i class="fas fa-hourglass-end"></i>
@endsection

@section('message', 'Votre session a expiré.')

@section('description', 'La page a expiré car vous êtes resté inactif trop longtemps. Veuillez retourner à l\'accueil ou actualiser la page.')
