@extends('user.layouts.template')

@section('content')
<div class="container-fluid">

    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">Profil de l'utilisateur</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p><strong>Oups ! Une ou plusieurs erreurs se sont produites :</strong></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORMULAIRE UNIQUE POUR TOUTES LES MISES À JOUR --}}
            <form id="profile-form" action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Section Informations générales --}}
                <div class="text-center mb-4">
                    {{-- MODIFICATION 1 : Ajout de l'ID ici --}}
                    <img id="profile-image-preview" src="{{ optional(auth()->user())->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('assets/images/profiles/useriii.jpeg') }}"
                         alt="Photo de profil" class="rounded-circle" width="150" height="150">
                    <div class="mt-3">
                        <label for="profile_picture" class="btn btn-sm btn-info">Changer la photo</label>
                        {{-- L'attribut 'accept' est un plus pour n'autoriser que les images --}}
                        <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
                        
                        @if(auth()->user()->profile_picture)
                            <button type="submit" form="delete-picture-form" class="btn btn-sm btn-danger">Supprimer la photo</button>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', auth()->user()->name) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom', auth()->user()->prenom) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', auth()->user()->email) }}">
                </div>
                
                <div class="row">
                    <div class="col-md-2 form-group">
                        <label for="indicatif">Indicatif</label>
                        <input type="text" class="form-control" id="indicatif" name="indicatif" value="{{ old('indicatif', auth()->user()->indicatif) }}" placeholder="+225">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="contact">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="{{ old('contact', auth()->user()->contact) }}">
                    </div>
                     <div class="col-md-6 form-group">
                        <label for="CMU">Numéro CMU (Optionnel)</label>
                        <input type="text" class="form-control" id="CMU" name="CMU" value="{{ old('CMU', auth()->user()->CMU) }}">
                    </div>
                </div>

                <hr>
                
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="diaspora" name="diaspora" value="1" {{ old('diaspora', auth()->user()->diaspora) ? 'checked' : '' }}>
                        <label class="form-check-label" for="diaspora">
                            Je suis de la diaspora (je réside à l'étranger)
                        </label>
                    </div>
                </div>

                <div id="diaspora-fields" style="{{ old('diaspora', auth()->user()->diaspora) ? '' : 'display: none;' }}">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="pays_residence">Pays de résidence</label>
                            <input type="text" class="form-control" id="pays_residence" name="pays_residence" value="{{ old('pays_residence', auth()->user()->pays_residence) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="ville_residence">Ville de résidence</label>
                            <input type="text" class="form-control" id="ville_residence" name="ville_residence" value="{{ old('ville_residence', auth()->user()->ville_residence) }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="adresse_etrangere">Adresse à l'étranger</label>
                        <input type="text" class="form-control" id="adresse_etrangere" name="adresse_etrangere" value="{{ old('adresse_etrangere', auth()->user()->adresse_etrangere) }}">
                    </div>
                </div>

                <hr>

                {{-- Section de changement de mot de passe (maintenant intégrée) --}}
                <h4 class="card-title mt-4">Changer le mot de passe (laisser vide pour ne pas modifier)</h4>
                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirmer le nouveau mot de passe</label>
                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                </div>
                
                <hr>

                {{-- BOUTON UNIQUE DE MISE À JOUR --}}
                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#passwordConfirmationModal">
                    Mettre à jour le profil
                </button>
            </form> {{-- Fin du formulaire unique --}}

            {{-- Formulaire dédié à la suppression de la photo --}}
            @if(auth()->user()->profile_picture)
            <form id="delete-picture-form" action="{{ route('user.profile.picture.delete') }}" method="POST" class="d-none" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?');">
                @csrf
                @method('DELETE')
            </form>
            @endif
        </div>
    </div>

    {{-- Modale de confirmation (inchangée) --}}
    <div class="modal fade" id="passwordConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Confirmer les modifications</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Pour enregistrer les modifications, veuillez saisir votre mot de passe actuel.</p>
                    <div class="form-group">
                        <label for="password">Mot de passe actuel</label>
                        <input type="password" class="form-control" name="password" required form="profile-form">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" form="profile-form">Confirmer et Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Code existant pour la diaspora
    const diasporaCheckbox = document.getElementById('diaspora');
    const diasporaFields = document.getElementById('diaspora-fields');

    diasporaCheckbox.addEventListener('change', function () {
        if (this.checked) {
            diasporaFields.style.display = 'block';
        } else {
            diasporaFields.style.display = 'none';
        }
    });

    // MODIFICATION 2 : Ajout du script pour l'aperçu de l'image
    const profilePictureInput = document.getElementById('profile_picture');
    const profileImagePreview = document.getElementById('profile-image-preview');

    if (profilePictureInput && profileImagePreview) {
        profilePictureInput.addEventListener('change', function(event) {
            // S'assurer qu'un fichier a bien été sélectionné
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                
                // Lorsque le fichier est lu, mettre à jour l'attribut 'src' de l'image
                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                };
                
                // Lire le fichier comme une URL de données (Data URL)
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }
});
</script>
@endpush