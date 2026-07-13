@extends('errors::minimal')

@section('title', 'Non Authentifié')

@section('code', '401')

@section('icon')
    <i class="fas fa-user-lock"></i>
@endsection

@section('message', 'Authentification requise.')

@section('description', 'Vous devez être connecté à votre compte pour accéder à cette page.')
