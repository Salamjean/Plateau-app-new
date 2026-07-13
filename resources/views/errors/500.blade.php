@extends('errors::minimal')

@section('title', 'Erreur Serveur')

@section('code', '500')

@section('icon')
    <i class="fas fa-server"></i>
@endsection

@section('message', 'Erreur interne du serveur.')

@section('description', 'Une erreur inattendue s\'est produite sur nos serveurs. Nos ingénieurs travaillent déjà à la résolution du problème.')
