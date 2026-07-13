@extends('errors::minimal')

@section('title', 'Accès Refusé')

@section('code', '403')

@section('icon')
    <i class="fas fa-lock"></i>
@endsection

@section('message', 'Accès non autorisé.')

@section('description', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource ou cette page est strictement réservée.')
