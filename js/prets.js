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
    // Charger la sidebar
    fetch('../views/sidebar.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('sidebar-container').innerHTML = data;
        });
    
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
    
    // Ajouter un événement pour le bouton de sauvegarde de simulation
    document.getElementById('save-simulation-btn').addEventListener('click', saveSimulation);
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

// Fonction pour sauvegarder une simulation
function saveSimulation() {
    // Vérifier qu'une simulation a bien été effectuée
    if (document.getElementById('simulation-result').style.display !== 'block') {
        showAlert('error', 'Veuillez d\'abord effectuer une simulation.');
        return;
    }
    
    // Demander un nom pour la simulation
    const simulationName = prompt('Donnez un nom à cette simulation:', 'Simulation du ' + new Date().toLocaleDateString());
    
    // Si l'utilisateur a annulé, sortir de la fonction
    if (!simulationName) return;
    
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
        showAlert('error', 'Informations manquantes pour sauvegarder la simulation.');
        return;
    }
    
    // Préparer les données pour l'API
    const formData = new URLSearchParams();
    formData.append('nom', simulationName);
    formData.append('type_pret_id', typePretId);
    formData.append('montant_pret', montantPret);
    formData.append('date_pret', datePret);
    formData.append('assurance', assurance);
    formData.append('delai', delai);
    formData.append('client_id', clientId);
    formData.append('duree_prevue', duree);
    
    // Appeler l'API pour sauvegarder la simulation
    fetch(`${API_BASE_URL}/simulations`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Simulation sauvegardée avec succès !');
        } else {
            showAlert('error', data.message || 'Erreur lors de la sauvegarde de la simulation.');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la sauvegarde de la simulation:', error);
        showAlert('error', 'Erreur de communication avec le serveur.');
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
