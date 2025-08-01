<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Prêts</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('sidebar.html'); ?>
    <div class="main-content">
        <div class="header">
            <h1>Gestion des Prêts</h1>
        </div>
        
        <!-- Client Selection -->
        <div class="card">
            <div class="card-header">
                <h2>Sélection du Client</h2>
            </div>
            <div class="form-group">
                <label for="client-select" class="form-label">Client</label>
                <select id="client-select" class="form-control">
                    <option value="">Sélectionnez un client</option>
                    <!-- Options will be loaded dynamically -->
                </select>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button active" data-tab="list-prets">Liste des Prêts</button>
            <button class="tab-button" data-tab="nouveau-pret">Nouveau Prêt</button>
        </div>
        
        <!-- List of Loans Tab -->
        <div id="list-prets" class="tab-content active">
            <div class="card">
                <div class="card-header">
                    <h2>Prêts du Client</h2>
                </div>
                <div class="table-responsive">
                    <table class="table" id="loans-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Mensualité</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
                <div id="no-loans-message" style="text-align: center; padding: 20px; display: none;">
                    <p>Aucun prêt trouvé pour ce client.</p>
                </div>
            </div>
        </div>
        
        <!-- New Loan Tab -->
        <div id="nouveau-pret" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h2>Création d'un Nouveau Prêt</h2>
                </div>
                
                <div id="loan-alerts">
                    <div class="alert alert-success" id="success-alert">
                        <i class="fas fa-check-circle"></i> Prêt créé avec succès !
                    </div>
                    <div class="alert alert-danger" id="error-alert">
                        <i class="fas fa-exclamation-circle"></i> <span id="error-message">Une erreur est survenue.</span>
                    </div>
                </div>
                
                <form id="loan-form">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="type-pret" class="form-label">Type de Prêt</label>
                                <select id="type-pret" class="form-control" required>
                                    <option value="">Sélectionnez un type</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="montant-pret" class="form-label">Montant du Prêt (Ar)</label>
                                <input type="number" id="montant-pret" class="form-control" min="1000" step="100" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="date-pret" class="form-label">Date du Prêt</label>
                                <input type="date" id="date-pret" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="assurance" class="form-label">Assurance (%)</label>
                                <input type="number" id="assurance" class="form-control" min="0" step="0.1" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="duree_prevue" class="form-label">Duree prevue (mois)</label>
                                <input type="number" id="duree_prevue" class="form-control" min="0" step="1" value="0" placeholder="Ex : 12 mois">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="delai" class="form-label">Délai avant premier remboursement (mois)</label>
                                <input type="number" id="delai" class="form-control" min="0" max="12" step="1" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="text-align: right;">
                        <button type="button" id="simulate-btn" class="btn btn-secondary">
                            <i class="fas fa-calculator"></i> Simuler
                        </button>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <i class="fas fa-check"></i> Créer le Prêt
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Simulation Results -->
            <div id="simulation-result" class="simulation-result card">
                <div class="card-header">
                    <h2>Résultats de la Simulation</h2>
                </div>
                
                <div id="simulation-alerts">
                    <div class="alert alert-warning" id="warning-alert">
                        <i class="fas fa-exclamation-triangle"></i> <span id="warning-message"></span>
                    </div>
                </div>
                
                <div class="result-header">
                    <div class="result-item">
                        <div class="result-label">Mensualité</div>
                        <div class="result-value" id="mensualite">--</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Montant total</div>
                        <div class="result-value" id="montant-total">--</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Coût du crédit</div>
                        <div class="result-value" id="cout-credit">--</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Taux</div>
                        <div class="result-value" id="taux">--</div>
                    </div>
                </div>
                
                <div class="result-header">
                    <div class="result-item">
                        <div class="result-label">Début des remboursements</div>
                        <div class="result-value" id="debut-remboursement">--</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Fin des remboursements</div>
                        <div class="result-value" id="fin-remboursement">--</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Durée</div>
                        <div class="result-value" id="duree">--</div>
                    </div>
                </div>
                
                <!-- Ajout du bouton pour sauvegarder la simulation -->
                <div class="action-buttons" style="text-align: right; margin: 20px 0;">
                    <button id="save-simulation-btn" class="btn btn-primary">
                        <i class="fas fa-save"></i> Sauvegarder la simulation
                    </button>
                </div>
                
                <div class="amortization-table">
                    <h3>Tableau d'Amortissement</h3>
                    <div class="table-responsive">
                        <table class="table" id="amortization-table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Date</th>
                                    <th>Mensualité</th>
                                    <th>Capital</th>
                                    <th>Intérêts</th>
                                    <th>Capital Restant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loan Details Modal -->
    <div id="loan-details-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Détails du Prêt</h2>
            <div id="loan-details-content">
                <!-- Loan details will be loaded here -->
            </div>
        </div>
    </div>
    
    <script src="../js/prets.js"></script>
</body>
</html>
