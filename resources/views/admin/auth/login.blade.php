<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style.css">
    <title>Connexion d'un Admin</title>
    <style>
        body {
            background-image: url({{ asset('assets/images/profiles/Bguser.jpg') }});
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh; 
            margin: 0; 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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

        .success-message {
            background-color: lightgreen;
            text-align: center;
        }

        .flex-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            background: #f14336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #d12c28;
        }
    </style>
</head>
<body>

<form class="form-control" method="POST" action="{{ route('admin.handleLogin') }}">
    <p class="title">Connexion de Kks-Technologies</p>
    
    @csrf
    @method('post')
    
    @if (Session::get('success'))
        <div class="success-message">{{ Session::get('success') }}</div>
    @endif
    @if (Session::get('error'))
        <div class="error-message">{{ Session::get('error') }}</div>
    @endif

    <div class="input-field">
        <input required class="input" type="email" name="email" value="{{ old('email') }}" />
        <label class="label" for="email">Email</label>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="input-field">
        <input required class="input" type="password" name="password" />
        <label class="label" for="password">Mot de passe</label>
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="submit-btn">Se connecter</button>
</form>

</body>
</html>