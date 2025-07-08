<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Montant de l'EF </title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">  
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            color: #1a202c;
            display: flex;
        }

        .content {
            margin-left: 270px;
            padding: 30px;
            width: calc(100% - 270px);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 24px;
        }

        h1 {
            text-align: center;
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 32px;
        }

        .summary-box {
            background-color: #edf2f7;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
        }

        .filter-section {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .filter-section label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .filter-section input {
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            background-color: #f7fafc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .filter-section input:focus {
            outline: none;
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        }

        .filter-section button {
            padding: 10px 24px;
            background-color: #2b6cb0;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .filter-section button:hover {
            background-color: #2c5282;
            transform: translateY(-1px);
        }

        .filter-section button:active {
            transform: translateY(0);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 32px;
        }

        th,
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #edf2f7;
            color: #2d3748;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr:hover {
            background-color: #f7fafc;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .no-data {
            text-align: center;
            padding: 24px;
            color: #718096;
            font-style: italic;
        }

        #interestChart {
            max-width: 100%;
            margin-top: 32px;
            border-radius: 8px;
            padding: 16px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .montant {
            text-align: right;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                width: 100%;
                padding: 16px;
            }

            .container {
                padding: 16px;
            }

            th,
            td {
                font-size: 12px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Inclure la sidebar (supposée dans un fichier séparé) -->
    <?php include('sidebar.html'); ?>


    <div class="content">
        <div class="container">
            <h1>Montant de l'Etablissement Financier </h1>


            <form class="filter-section" id="interetForm" method="POST">
                <div>
                    <label for="dateDebut">Date début</label>
                    <input type="date" id="dateDebut" name="dateDebut" required>
                </div>
                <div>
                    <label for="dateFin">Date fin</label>
                    <input type="date" id="dateFin" name="dateFin" required>
                </div>
                <button type="submit">Filtrer</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Année</th>
                        <th>Mois</th>
                        <th>Fonds initial </th>
                        <th>Emprunté</th>
                        <th>Reste Non Emprunté (Ar)</th>
                        <th>Total Intérêts (Ar)</th>
                        <th>Total Capital (Ar)</th>
                        <th>Montant Total (Ar)</th>
                    </tr>
                </thead>

                <tbody id="interestTableBody">
                    <tr>
                        <td colspan="5" class="no-data">Aucune donnée disponible</td>
                    </tr>
                </tbody>
            </table>

            <canvas id="interestChart"></canvas>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost/WebFinal/ws";
        let chartInstance = null;

        // Fonction AJAX générique
        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        callback(JSON.parse(xhr.responseText));
                    } else {
                        alert("Erreur lors de la requête : " + xhr.status);
                    }
                }
            };
            xhr.send(data);
        }


        // Formater les mois en français
        function formatMonth(date) {
            return date.toLocaleString('fr-FR', {
                month: 'short',
                year: 'numeric'
            });
        }

        // Charger les données des remboursements
        function loadRemboursements() {
            document.getElementById('interetForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const dateDebut = document.getElementById('dateDebut').value;
                const dateFin = document.getElementById('dateFin').value;

                ajax("GET", `/etablissement/etat?dateDebut=${encodeURIComponent(dateDebut)}&dateFin=${encodeURIComponent(dateFin)}`, null, function(response) {
                    const tbody = document.getElementById('interestTableBody');
                    tbody.innerHTML = '';

                    if (response.length > 0) {
                        response.forEach(row => {
                            const totalMontant = parseFloat(row.total_interets) + parseFloat(row.total_capital);
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.annee}</td>
                                <td>${row.mois}</td>
                                <td class="montant">${parseFloat(row.fonds_initial).toLocaleString('fr-FR')}</td>                                 
                                <td class="montant">${parseFloat(row.prets_accordes).toLocaleString('fr-FR')}</td>                                
                                <td class="montant">${parseFloat(row.reste_non_emprunte).toLocaleString('fr-FR')}</td>
                                <td class="montant">${parseFloat(row.total_interet).toLocaleString('fr-FR')}</td>
                                <td class="montant">${parseFloat(row.total_capital).toLocaleString('fr-FR')}</td>
                                <td class="montant">${parseFloat(row.montant_total).toLocaleString('fr-FR')}</td>
                            `;
                            tbody.appendChild(tr); // ✅ AJOUT DU TR AU TABLEAU
                        });

                    } else {
                        const tr = document.createElement('tr');
                        tr.innerHTML = '<td colspan="5" class="no-data">Aucune donnée disponible</td>';
                        tbody.appendChild(tr);
                    }
                });
            })
        };

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadRemboursements();
        });
    </script>
</body>

</html>