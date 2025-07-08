<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Prêts</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div id="sidebar-container"></div>
    <div class="content">
        <div class="container">
            <h1>Gestion des Prêts</h1>
            
            <!-- Client Selection -->
            <div class="form-section">
                <h3>Sélection du Client</h3>
                <div class="form-group">
                    <label for="client-select">Client</label>
                    <select id="client-select">
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
                <h3>Prêts du Client</h3>
                <div class="table-container">
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
            
            <!-- New Loan Tab -->
            <div id="nouveau-pret" class="tab-content">
                <h3>Création d'un Nouveau Prêt</h3>
                
                <div id="loan-alerts">
                    <div class="alert alert-success" id="success-alert" style="display: none;">
                        <i class="fas fa-check-circle"></i> Prêt créé avec succès !
                    </div>
                    <div class="alert alert-danger" id="error-alert" style="display: none;">
                        <i class="fas fa-exclamation-circle"></i> <span id="error-message">Une erreur est survenue.</span>
                    </div>
                </div>
                
                <form id="loan-form" class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="type-pret">Type de Prêt</label>
                            <select id="type-pret" required>
                                <option value="">Sélectionnez un type</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="montant-pret">Montant du Prêt (Ar)</label>
                            <input type="number" id="montant-pret" min="1000" step="100" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date-pret">Date du Prêt</label>
                            <input type="date" id="date-pret" required>
                        </div>
                        <div class="form-group">
                            <label for="assurance">Assurance (%)</label>
                            <input type="number" id="assurance" min="0" step="0.1" value="0">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="duree_prevue">Durée prévue (mois)</label>
                            <input type="number" id="duree_prevue" min="0" step="1" value="0" placeholder="Ex : 12 mois">
                        </div>
                        <div class="form-group">
                            <label for="delai">Délai avant premier remboursement (mois)</label>
                            <input type="number" id="delai" min="0" max="12" step="1" value="0">
                        </div>
                    </div>
                    
                    <div class="button-group">
                        <button type="button" id="simulate-btn" class="secondary">
                            <i class="fas fa-calculator"></i> Simuler
                        </button>
                        <button type="submit" id="submit-btn">
                            <i class="fas fa-check"></i> Créer le Prêt
                        </button>
                    </div>
                </form>
            
                <!-- Simulation Results -->
                <div id="simulation-result" class="form-section" style="display: none;">
                    <h3>Résultats de la Simulation</h3>
                    
                    <div id="simulation-alerts">
                        <div class="alert alert-warning" id="warning-alert" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i> <span id="warning-message"></span>
                        </div>
                    </div>
                    
                    <div class="result-summary">
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
                    
                    <div class="result-summary">
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
                    
                    <h3>Tableau d'Amortissement</h3>
                    <div class="table-container">
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
            
            <!-- Message area -->
            <div id="message"></div>
        </div>
    </div>
    
    <!-- Loan Details Modal -->
    <div id="loan-details-modal" class="modal" style="display: none;">
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