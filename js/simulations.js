// Configuration de base
const API_BASE_URL = 'http://localhost/WebFinal/ws';
let allSimulations = [];
let selectedSimulationIds = new Set();

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

// Initialisation de la page
document.addEventListener('DOMContentLoaded', function() {
    // Charger la sidebar
    // fetch('../views/sidebar.html')
    //     .then(response => response.text())
    //     .then(data => {
    //         document.getElementById('sidebar-container').innerHTML = data;
    //         // Mettre à jour le lien actif dans la sidebar
    //         const sidebarLinks = document.querySelectorAll('.sidebar-link');
    //         sidebarLinks.forEach(link => {
    //             if (link.getAttribute('href') === 'simulations.html') {
    //                 link.classList.add('active');
    //             } else {
    //                 link.classList.remove('active');
    //             }
    //         });
    //     });
    
    // Charger les clients pour le filtre
    loadClients();
    
    // Charger toutes les simulations
    loadSimulations();
    
    // Ajouter les écouteurs d'événements
    document.getElementById('client-filter').addEventListener('change', filterSimulations);
    document.getElementById('date-filter').addEventListener('change', filterSimulations);
    document.getElementById('select-all-btn').addEventListener('click', selectAllSimulations);
    document.getElementById('deselect-all-btn').addEventListener('click', deselectAllSimulations);
    document.getElementById('compare-btn').addEventListener('click', compareSelectedSimulations);
});

// Charger la liste des clients pour le filtre
function loadClients() {
    fetch(`${API_BASE_URL}/clients`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('client-filter');
            
            data.forEach(client => {
                const option = document.createElement('option');
                option.value = client.id;
                option.textContent = client.nom;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des clients:', error);
        });
}

// Charger toutes les simulations
function loadSimulations() {
    fetch(`${API_BASE_URL}/simulations`)
        .then(response => response.json())
        .then(data => {
            allSimulations = data;
            displaySimulations(data);
        })
        .catch(error => {
            console.error('Erreur lors du chargement des simulations:', error);
            document.getElementById('no-simulations').style.display = 'block';
        });
}

// Afficher les simulations dans la liste
function displaySimulations(simulations) {
    const container = document.getElementById('simulations-list');
    
    // Vider le contenu précédent (sauf le message d'absence de simulations)
    const noSimulations = document.getElementById('no-simulations');
    container.innerHTML = '';
    container.appendChild(noSimulations);
    
    if (simulations.length === 0) {
        noSimulations.style.display = 'block';
        return;
    }
    
    noSimulations.style.display = 'none';
    
    // Afficher chaque simulation
    simulations.forEach(simulation => {
        const simulationElement = document.createElement('div');
        simulationElement.className = 'simulation-item';
        simulationElement.innerHTML = `
            <input type="checkbox" id="sim-${simulation.id}" class="simulation-checkbox" 
                   data-id="${simulation.id}" ${selectedSimulationIds.has(simulation.id.toString()) ? 'checked' : ''}>
            <div class="simulation-info">
                <div class="simulation-name">${simulation.nom}</div>
                <div class="simulation-details">
                    Client: ${simulation.client_nom} | 
                    Montant: ${formatCurrency(simulation.montant_pret)} | 
                    Taux: ${formatPercent(simulation.taux)} | 
                    Mensualité: ${formatCurrency(simulation.mensualite)} | 
                    Durée: ${simulation.duree} mois | 
                    Date: ${formatDate(simulation.date_debut)}
                </div>
            </div>
        `;
        
        container.appendChild(simulationElement);
        
        // Ajouter l'écouteur d'événement pour la case à cocher
        const checkbox = simulationElement.querySelector(`#sim-${simulation.id}`);
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedSimulationIds.add(this.dataset.id);
            } else {
                selectedSimulationIds.delete(this.dataset.id);
            }
            updateCompareButtonState();
        });
    });
    
    updateCompareButtonState();
}

// Filtrer les simulations en fonction des critères
function filterSimulations() {
    const clientId = document.getElementById('client-filter').value;
    const dateFilter = document.getElementById('date-filter').value;
    
    let filteredSimulations = [...allSimulations];
    
    // Filtrer par client
    if (clientId) {
        filteredSimulations = filteredSimulations.filter(sim => sim.id_client == clientId);
    }
    
    // Filtrer par date
    if (dateFilter) {
        const today = new Date();
        const cutoffDate = new Date(today);
        cutoffDate.setDate(today.getDate() - parseInt(dateFilter));
        
        filteredSimulations = filteredSimulations.filter(sim => {
            const simDate = new Date(sim.date_debut);
            return simDate >= cutoffDate;
        });
    }
    
    displaySimulations(filteredSimulations);
}

// Sélectionner toutes les simulations visibles
function selectAllSimulations() {
    const checkboxes = document.querySelectorAll('.simulation-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        selectedSimulationIds.add(checkbox.dataset.id);
    });
    updateCompareButtonState();
}

// Désélectionner toutes les simulations
function deselectAllSimulations() {
    const checkboxes = document.querySelectorAll('.simulation-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        selectedSimulationIds.delete(checkbox.dataset.id);
    });
    updateCompareButtonState();
}

// Mettre à jour l'état du bouton de comparaison
function updateCompareButtonState() {
    const compareBtn = document.getElementById('compare-btn');
    if (selectedSimulationIds.size >= 2) {
        compareBtn.disabled = false;
    } else {
        compareBtn.disabled = true;
    }
}

// Comparer les simulations sélectionnées
function compareSelectedSimulations() {
    if (selectedSimulationIds.size < 2) {
        alert('Veuillez sélectionner au moins deux simulations à comparer.');
        return;
    }
    
    // Récupérer les détails complets des simulations sélectionnées
    const promises = Array.from(selectedSimulationIds).map(id => 
        fetch(`${API_BASE_URL}/simulations/${id}`)
            .then(response => response.json())
    );
    
    Promise.all(promises)
        .then(simulationsDetails => {
            displayComparisonResults(simulationsDetails);
        })
        .catch(error => {
            console.error('Erreur lors du chargement des détails des simulations:', error);
            alert('Une erreur est survenue lors du chargement des détails des simulations.');
        });
}

// Afficher les résultats de la comparaison
function displayComparisonResults(simulations) {
    const container = document.getElementById('comparison-container');
    container.innerHTML = '';
    
    // Trouver les meilleures valeurs pour la mise en évidence
    const bestValues = {
        mensualite: Math.min(...simulations.map(sim => parseFloat(sim.mensualite))),
        taux: Math.min(...simulations.map(sim => parseFloat(sim.taux))),
        duree: Math.min(...simulations.map(sim => parseInt(sim.duree)))
    };
    
    // Créer une colonne pour chaque simulation
    simulations.forEach(sim => {
        const column = document.createElement('div');
        column.className = 'comparison-column';
        
        column.innerHTML = `
            <div class="comparison-header">${sim.nom}</div>
            <div class="comparison-body">
                <div class="comparison-item">
                    <div class="comparison-label">Client</div>
                    <div class="comparison-value">${sim.client_nom}</div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Montant du prêt</div>
                    <div class="comparison-value">${formatCurrency(sim.montant_pret)}</div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Mensualité</div>
                    <div class="comparison-value ${parseFloat(sim.mensualite) === bestValues.mensualite ? 'highlight-best' : ''}">
                        ${formatCurrency(sim.mensualite)}
                    </div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Taux d'intérêt</div>
                    <div class="comparison-value ${parseFloat(sim.taux) === bestValues.taux ? 'highlight-best' : ''}">
                        ${formatPercent(sim.taux)}
                    </div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Durée</div>
                    <div class="comparison-value ${parseInt(sim.duree) === bestValues.duree ? 'highlight-best' : ''}">
                        ${sim.duree} mois
                    </div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Assurance</div>
                    <div class="comparison-value">${formatPercent(sim.assurance)}</div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Délai avant 1er remboursement</div>
                    <div class="comparison-value">${sim.delai} mois</div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Date de début</div>
                    <div class="comparison-value">${formatDate(sim.date_debut)}</div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Date de fin</div>
                    <div class="comparison-value">${formatDate(sim.date_fin)}</div>
                </div>
                <div class="comparison-item">
                    <a href="prets.html?sim=${sim.id}" class="btn btn-primary btn-sm" style="width: 100%;">
                        <i class="fas fa-check"></i> Appliquer cette simulation
                    </a>
                </div>
            </div>
        `;
        
        container.appendChild(column);
    });
    
    // Afficher la section des résultats de comparaison
    document.getElementById('comparison-results').style.display = 'block';
    document.getElementById('comparison-results').scrollIntoView({ behavior: 'smooth' });
}
