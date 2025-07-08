<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparaison de Simulations</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .simulation-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .simulation-item:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .simulation-checkbox {
            margin-right: 1rem;
        }
        .simulation-info {
            flex: 1;
        }
        .simulation-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .simulation-details {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .comparison-container {
            display: flex;
            overflow-x: auto;
            gap: 1.5rem;
            padding-bottom: 1rem;
        }
        .comparison-column {
            min-width: 250px;
            flex: 1;
            border: 1px solid var(--border-color);
            border-radius: var(--card-border-radius);
            background-color: var(--white);
        }
        .comparison-header {
            background-color: var(--primary-light);
            color: var(--primary-color);
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            border-top-left-radius: var(--card-border-radius);
            border-top-right-radius: var(--card-border-radius);
            text-align: center;
        }
        .comparison-body {
            padding: 1rem;
        }
        .comparison-item {
            margin-bottom: 1rem;
        }
        .comparison-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }
        .comparison-value {
            font-weight: 500;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
        }
        .filter-section {
            margin-bottom: 1.5rem;
        }
        .filter-control {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }
        .highlight-best {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            font-weight: 700;
        }
    </style>
</head>
<body>
    <?php include('sidebar.html'); ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Comparaison de Simulations de Prêts</h1>
        </div>
        
        <!-- Filter Section -->
        <div class="card filter-section">
            <div class="card-header">
                <h2>Filtres</h2>
            </div>
            <div class="filter-control">
                <div class="form-group" style="flex: 1;">
                    <label for="client-filter" class="form-label">Client</label>
                    <select id="client-filter" class="form-control">
                        <option value="">Tous les clients</option>
                        <!-- Options will be loaded dynamically -->
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="date-filter" class="form-label">Période</label>
                    <select id="date-filter" class="form-control">
                        <option value="">Toutes les périodes</option>
                        <option value="7">7 derniers jours</option>
                        <option value="30">30 derniers jours</option>
                        <option value="90">3 derniers mois</option>
                        <option value="365">Dernière année</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Simulations List -->
        <div class="card">
            <div class="card-header">
                <h2>Simulations disponibles</h2>
            </div>
            <div class="action-buttons">
                <div>
                    <button id="select-all-btn" class="btn btn-secondary btn-sm">
                        <i class="fas fa-check-square"></i> Tout sélectionner
                    </button>
                    <button id="deselect-all-btn" class="btn btn-secondary btn-sm">
                        <i class="fas fa-square"></i> Tout désélectionner
                    </button>
                </div>
                <button id="compare-btn" class="btn btn-primary">
                    <i class="fas fa-exchange-alt"></i> Comparer les simulations
                </button>
            </div>
            
            <div id="simulations-list">
                <!-- Simulations will be loaded dynamically -->
                <div class="empty-state" id="no-simulations" style="display: none;">
                    <i class="fas fa-search fa-3x"></i>
                    <h3>Aucune simulation trouvée</h3>
                    <p>Aucune simulation n'est disponible pour les critères sélectionnés.</p>
                </div>
            </div>
        </div>
        
        <!-- Comparison Results -->
        <div class="card" id="comparison-results" style="display: none;">
            <div class="card-header">
                <h2>Résultats de la comparaison</h2>
            </div>
            <div id="comparison-container" class="comparison-container">
                <!-- Comparison results will be displayed here -->
            </div>
        </div>
    </div>
    
    <script src="../js/simulations.js"></script>
</body>
</html>
