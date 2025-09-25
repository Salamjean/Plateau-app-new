<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="Style.css">
    <title>Inscription d'un Admin</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-image: url({{ asset('assets/images/profiles/vendorbg.jpg') }});
            background-size: cover;
        }

        .form-control {
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            gap: 15px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            width: 450px;
            border-radius: 20px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.6);
        }

        .title {
            font-size: 28px;
            font-weight: 800;
            text-align: center;
            color: #333;
        }

        .input-field {
            position: relative;
            width: 100%;
        }

        .input {
            width: 100%;
            outline: none;
            border-radius: 8px;
            height: 45px;
            border: 1.5px solid #ecedec;
            background: transparent;
            padding-left: 10px;
            transition: border 0.3s ease;
        }

        .input:focus {
            border: 1.5px solid #007bff;
        }

        .label {
            position: absolute;
            top: 15px;
            left: 10px;
            color: #ccc;
            transition: all 0.3s ease;
            pointer-events: none;
            z-index: 2;
        }

        .input:focus ~ .label,
        .input:valid ~ .label {
            top: -5px;
            left: 5px;
            font-size: 12px;
            color: #007bff;
            background-color: #ffffff;
            padding-left: 5px;
            padding-right: 5px;
        }

        .submit-btn {
            margin-top: 30px;
            height: 55px;
            background: #6777ef;
            border: 0;
            outline: none;
            color: #ffffff;
            font-size: 18px;
            font-weight: 700;
            border-radius: 11px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.15, 0.83, 0.66, 1);
        }

        .submit-btn:hover {
            box-shadow: 0px 0px 0px 2px #ffffff, 0px 0px 0px 4px rgba(0, 0, 0, 0.1);
        }

        .error-message {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<form class="form-control" method="POST" action="{{ route('admin.handleRegister') }}">
  <div class="row" style="width:100%; justify-content:center">
    @if (Session::get('success1')) <!-- Pour la suppression -->
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Suppression réussie',
                text: '{{ Session::get('success1') }}',
                showConfirmButton: true,  // Afficher le bouton OK
                confirmButtonText: 'OK',  // Texte du bouton
                background: '#ffcccc',   // Couleur de fond personnalisée
                color: '#b30000'          // Texte rouge foncé
            });
        </script>
    @endif

    @if (Session::get('success')) <!-- Pour la modification -->
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Action réussie',
                text: '{{ Session::get('success') }}',
                showConfirmButton: true,  // Afficher le bouton OK
                confirmButtonText: 'OK',  // Texte du bouton
                background: '#ccffcc',   // Couleur de fond personnalisée
                color: '#006600'          // Texte vert foncé
            });
        </script>
    @endif

    @if (Session::get('error')) <!-- Pour une erreur générale -->
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: '{{ Session::get('error') }}',
                showConfirmButton: true,  // Afficher le bouton OK
                confirmButtonText: 'OK',  // Texte du bouton
                background: '#f86750',    // Couleur de fond rouge vif
                color: '#ffffff'          // Texte blanc
            });
        </script>
    @endif
</div>
    <p class="title">Kks-Technologies</p>

    @csrf
    @method('post')

    @if (Session::get('success'))
        <div class="success-message">{{ Session::get('success') }}</div>
    @endif

    <div class="input-field">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Script d'initialisation de Select2 -->
    <script>
        $(document).ready(function() {
            $('#name').select2({
                placeholder: "Sélectionnez une commune",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
        <input class="input" type="text" name="name" placeholder="Entrez votre nom & prénoms" value="{{ old('name') }}" required />
        <label class="label" for="name">Nom et Prénoms</label>
        @error('name')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="input-field">
        <input class="input" type="email" name="email" placeholder="Email@exemple.com" value="{{ old('email') }}" required />
        <label class="label" for="email">Email</label>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="input-field">
        <input class="input" type="password" name="password" required />
        <label class="label" for="password">Mot de passe</label>
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="submit-btn">S'inscrire</button>
</form>
<script>
  document.addEventListener('DOMContentLoaded', function() {
      @if (Session::has('success1'))
          Swal.fire({
              icon: 'success',
              title: 'Suppression réussie',
              text: '{{ Session::get('success1') }}',
              confirmButtonText: 'OK'
          });
      @endif

      @if (Session::has('success'))
          Swal.fire({
              icon: 'success',
              title: 'Action réussie',
              text: '{{ Session::get('success') }}',
              confirmButtonText: 'OK'
          });
      @endif

      @if (Session::has('error'))
          Swal.fire({
              icon: 'error',
              title: 'Erreur',
              text: '{{ Session::get('error') }}',
              confirmButtonText: 'OK'
          });
      @endif
  });
</script>

<!-- Scripts pour Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
      $('#name').select2({
          placeholder: "Sélectionnez une commune",
          allowClear: true,
          width: '100%'
      });
  });
</script>
</body>
</html>

