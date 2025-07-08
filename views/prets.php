<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Prêts</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    
    <script>
        // Configuration de base
const API_BASE_URL = 'http://localhost/WebFinal/ws';
let currentClientId = null;

// Fonctions utilitaires
function formatDate(dateString) {
    if (!dateString) return '--';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function formatCurrency(amount) {
    if (amount === undefined || amount === null) return '--';
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
}

function formatPercent(value) {
    if (value === undefined || value === null) return '--';
    return new Intl.NumberFormat('fr-FR', { style: 'percent', minimumFractionDigits: 2 }).format(value / 100);
}

// Chargement de la sidebar
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialiser la date du jour dans le formulaire
    const today = new Date();
    const formattedDate = today.toISOString().substr(0, 10);
    document.getElementById('date-pret').value = formattedDate;
    
    // Charger les clients
    loadClients();
    
    // Charger les types de prêts
    loadLoanTypes();
    
    // Gérer les onglets
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Désactiver tous les onglets
            tabButtons.forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            
            // Activer l'onglet cliqué
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Gérer la sélection de client
    document.getElementById('client-select').addEventListener('change', function() {
        currentClientId = this.value;
        if (currentClientId) {
            loadClientLoans(currentClientId);
        } else {
            // Vider la table des prêts
            document.querySelector('#loans-table tbody').innerHTML = '';
            document.getElementById('no-loans-message').style.display = 'block';
        }
    });
    
    // Gérer la simulation de prêt
    document.getElementById('simulate-btn').addEventListener('click', simulateLoan);
    
    // Gérer la création de prêt
    document.getElementById('loan-form').addEventListener('submit', function(e) {
        e.preventDefault();
        createLoan();
    });
});

// Charger la liste des clients
function loadClients() {
    fetch(`${API_BASE_URL}/clients`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('client-select');
            select.innerHTML = '<option value="">Sélectionnez un client</option>';
            
            data.forEach(client => {
                const option = document.createElement('option');
                option.value = client.id;
                option.textContent = `${client.nom} `;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des clients:', error);
            // En cas d'échec, utiliser des données de test
            const select = document.getElementById('client-select');
            select.innerHTML = '<option value="">Sélectionnez un client</option>';
            select.innerHTML += '<option value="1">Client Test 1</option>';
            select.innerHTML += '<option value="2">Client Test 2</option>';
        });
}

// Charger les types de prêts
function loadLoanTypes() {
    fetch(`${API_BASE_URL}/types-pret`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('type-pret');
            select.innerHTML = '<option value="">Sélectionnez un type</option>';
            
            data.forEach(type => {
                const option = document.createElement('option');
                option.value = type.id;
                option.textContent = `${type.libelle} (Taux: ${type.taux}%, Durée max: ${type.duree_max} mois)`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des types de prêt:', error);
            // En cas d'échec, utiliser des données de test
            const select = document.getElementById('type-pret');
            select.innerHTML = '<option value="">Sélectionnez un type</option>';
            select.innerHTML += '<option value="1">Prêt Immobilier (Taux: 2.5%, Durée max: 300 mois)</option>';
            select.innerHTML += '<option value="2">Prêt Personnel (Taux: 5.0%, Durée max: 60 mois)</option>';
            select.innerHTML += '<option value="3">Prêt Auto (Taux: 3.75%, Durée max: 84 mois)</option>';
        });
}

// Charger les prêts d'un client
function loadClientLoans(clientId) {
    fetch(`${API_BASE_URL}/clients/${clientId}/prets`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#loans-table tbody');
            tbody.innerHTML = '';
            
            if (data.length === 0) {
                document.getElementById('no-loans-message').style.display = 'block';
                return;
            }
            
            document.getElementById('no-loans-message').style.display = 'none';
            
            data.forEach(loan => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${loan.id}</td>
                    <td>${loan.type_pret}</td>
                    <td>${formatCurrency(loan.montant_pret)}</td>
                    <td>${formatCurrency(loan.montant_mensuel)}</td>
                    <td>${formatDate(loan.date_pret)}</td>
                    <td>${formatDate(loan.date_fin)}</td>
                    <td>
                        <button class="btn btn-secondary btn-sm view-details" data-id="${loan.id}">
                            <i class="fas fa-eye"></i> Détails
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            // Ajouter des écouteurs d'événements pour les boutons de détails
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const loanId = this.getAttribute('data-id');
                    viewLoanDetails(loanId);
                });
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des prêts:', error);
            document.getElementById('no-loans-message').style.display = 'block';
            document.getElementById('no-loans-message').textContent = 'Erreur lors du chargement des prêts.';
        });
}

// Afficher les détails d'un prêt
function viewLoanDetails(loanId) {
    fetch(`${API_BASE_URL}/prets/${loanId}`)
        .then(response => response.json())
        .then(data => {
            // TODO: Implémenter l'affichage des détails du prêt dans une modale
            console.log('Détails du prêt:', data);
            alert(`Détails du prêt ${loanId} chargés. Voir la console pour plus d'informations.`);
        })
        .catch(error => {
            console.error('Erreur lors du chargement des détails du prêt:', error);
            alert('Erreur lors du chargement des détails du prêt.');
        });
}

// Simuler un prêt
function simulateLoan() {
    // Récupérer les données du formulaire
    const typePretId = document.getElementById('type-pret').value;
    const montantPret = document.getElementById('montant-pret').value;
    const datePret = document.getElementById('date-pret').value;
    const assurance = document.getElementById('assurance').value;
    const delai = document.getElementById('delai').value;
    const clientId = document.getElementById('client-select').value;
    const duree = document.getElementById('duree_prevue').value;
    
    // Vérifier que les données requises sont présentes
    if (!typePretId || !montantPret || !datePret || !clientId) {
        showAlert('error', 'Veuillez remplir tous les champs obligatoires.');
        return;
    }
    
    // Préparer les données pour l'API
    const formData = new URLSearchParams();
    formData.append('type_pret_id', typePretId);
    formData.append('montant_pret', montantPret);
    formData.append('date_pret', datePret);
    formData.append('assurance', assurance);
    formData.append('delai', delai);
    formData.append('client_id', clientId);
    formData.append('duree_prevue', duree);
    
    // Appeler l'API de simulation
    fetch(`${API_BASE_URL}/prets/simuler`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displaySimulationResults(data);
        } else {
            showAlert('error', data.message || 'Erreur lors de la simulation du prêt.');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la simulation du prêt:', error);
        showAlert('error', 'Erreur de communication avec le serveur.');
    });
}

// Afficher les résultats de simulation
function displaySimulationResults(data) {
    // Afficher la section de résultats
    document.getElementById('simulation-result').style.display = 'block';
    
    // Vérifier si le prêt est possible
    const warningAlert = document.getElementById('warning-alert');
    const warningMessage = document.getElementById('warning-message');
    
    if (!data.est_possible) {
        warningAlert.style.display = 'block';
        let message = 'Ce prêt ne peut pas être accordé : ';
        if (data.raisons_rejet.fonds_insuffisants) {
            message += 'Fonds insuffisants. ';
        }
        if (data.raisons_rejet.capacite_depassee) {
            message += 'Capacité d\'emprunt dépassée pour ce client.';
        }
        warningMessage.textContent = message;
    } else {
        warningAlert.style.display = 'none';
    }
    
    // Remplir les informations générales
    document.getElementById('mensualite').textContent = formatCurrency(data.mensualite);
    document.getElementById('montant-total').textContent = formatCurrency(data.montant_pret);
    document.getElementById('cout-credit').textContent = formatCurrency(data.total_interet);
    document.getElementById('taux').textContent = formatPercent(data.taux);
    document.getElementById('debut-remboursement').textContent = formatDate(data.debut_remboursement);
    document.getElementById('fin-remboursement').textContent = formatDate(data.fin_remboursement);
    document.getElementById('duree').textContent = `${data.duree} mois`;
    
    // Remplir le tableau d'amortissement
    const tbody = document.querySelector('#amortization-table tbody');
    tbody.innerHTML = '';
    
    data.tableau_amortissement.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.mois}</td>
            <td>${formatDate(row.date)}</td>
            <td>${formatCurrency(row.mensualite)}</td>
            <td>${formatCurrency(row.capital)}</td>
            <td>${formatCurrency(row.interet)}</td>
            <td>${formatCurrency(row.capital_restant)}</td>
        `;
        tbody.appendChild(tr);
    });
    
    // Faire défiler vers les résultats
    document.getElementById('simulation-result').scrollIntoView({ behavior: 'smooth' });
}

// Créer un prêt
function createLoan() {
    // Récupérer les données du formulaire
    const typePretId = document.getElementById('type-pret').value;
    const montantPret = document.getElementById('montant-pret').value;
    const datePret = document.getElementById('date-pret').value;
    const assurance = document.getElementById('assurance').value;
    const delai = document.getElementById('delai').value;
    const clientId = document.getElementById('client-select').value;
    const duree = document.getElementById('duree_prevue').value;
    
    // Vérifier que les données requises sont présentes
    if (!typePretId || !montantPret || !datePret || !clientId) {
        showAlert('error', 'Veuillez remplir tous les champs obligatoires.');
        return;
    }
    
    // Désactiver le bouton de soumission pendant le traitement
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
    
    // Préparer les données pour l'API
    const formData = new URLSearchParams();
    formData.append('type_pret_id', typePretId);
    formData.append('montant_pret', montantPret);
    formData.append('date_pret', datePret);
    formData.append('assurance', assurance);
    formData.append('delai', delai);
    formData.append('client_id', clientId);
    formData.append('duree_prevue', duree);
  
    
    // Appeler l'API de création
    fetch(`${API_BASE_URL}/prets/create`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Réactiver le bouton de soumission
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Créer le Prêt';
        
        if (data.success) {
            showAlert('success', 'Prêt créé avec succès !');
            // Réinitialiser le formulaire
            document.getElementById('loan-form').reset();
            // Mettre à jour la liste des prêts
            loadClientLoans(clientId);
            // Basculer vers l'onglet de liste des prêts
            document.querySelector('.tab-button[data-tab="list-prets"]').click();
        } else {
            showAlert('error', data.message || 'Erreur lors de la création du prêt.');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la création du prêt:', error);
        showAlert('error', 'Erreur de communication avec le serveur.');
        
        // Réactiver le bouton de soumission
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Créer le Prêt';
    });
}

// Afficher une alerte
function showAlert(type, message) {
    const alertId = type === 'success' ? 'success-alert' : 'error-alert';
    const alert = document.getElementById(alertId);
    
    if (type === 'error') {
        document.getElementById('error-message').textContent = message;
    }
    
    alert.style.display = 'block';
    
    // Masquer l'alerte après 5 secondes
    setTimeout(() => {
        alert.style.display = 'none';
    }, 5000);
}


    </script>

</body>
</html>