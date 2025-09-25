@extends('admin.layouts.template')

@section('content')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container-fluid" id="container-wrapper">

        <!-- Total des caisses -->
        <div class="text-center mb-4">
            <h2 class="font-semibold text-xl text-gray-800">
                Informations sur la mairie du plateau <br>
            </h2>
        </div>
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Solde Fourni</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">{{  number_format($soldeActuel, 0, ',', ' ') }} FCFA</div>
                        <i class="fas fa-money-bill fa-2x text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Solde Débité</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">{{ number_format($soldeDebite, 0, ',', ' ') }} FCFA</div>
                        <i class="fas fa-money-bill fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Solde Restant</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">{{ number_format($soldeRestant, 0, ',', ' ') }} FCFA</div>
                        <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Nombre de demande</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $total }}</div>
                        <i class="fas fa-list fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de demandes d'extraits -->
        <div class="text-center mb-4">
            <h2 class="font-semibold text-xl text-gray-800">
                Total de demande d'extrait effectuée
            </h2>
        </div>
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Naissances</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $naissance }}</div>
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Décès</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $deces }}</div>
                        <i class="fas fa-church fa-2x text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Mariages</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $mariage }}</div>
                        <i class="fas fa-ring fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Total Demandes</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">{{ $naissance + $deces + $mariage }}</div>
                        <i class="fas fa-list fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total des Personnels Inscrits dans les Mairies -->
        <div class="text-center mb-4">
            <h2 class="font-semibold text-xl text-gray-800">
                Total des personnels inscrits dans toutes les mairies
            </h2>
        </div>
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Agents d'etat civil</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">0</div>
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">agent financier</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">0</div>
                        <i class="fas fa-cash-register fa-2x text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-xl font-weight-bold text-uppercase mb-4">Agent de déclaration</h5>
                        <div class="h2 mb-0 font-weight-bold text-gray-800">0</div>
                        <i class="fas fa-user-md fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection