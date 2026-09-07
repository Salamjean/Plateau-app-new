@extends('errors::minimal')

@section('title', 'Page Introuvable')

@section('code', '404')

@section('icon')
    <i class="fas fa-map-signs"></i>
@endsection

@section('message', 'Oups ! Cette page s\'est perdue en chemin.')

@section('description', 'La page que vous recherchez n\'existe pas, a été supprimée ou est temporairement indisponible.')
