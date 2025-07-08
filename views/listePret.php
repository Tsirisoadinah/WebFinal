<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Intérêts</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            max-width: 960px;
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
    <div id="sidebar-placeholder"></div>

    <div class="content">
        <div class="container">
            <h1>Statistiques Intérêts</h1>

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
                        <th>Reste Non Emprunté (Ar)</th>
                        <th>Total Intérêts (Ar)</th>
                        <th>Total Capital (Ar)</th>
                        <th>Montant Total (Ar)</th>
                    </tr>
                </thead>
                <tbody id="interestTableBody">
                    <tr>
                        <td colspan="6" class="no-data">Aucune donnée disponible</td>
                    </tr>
                </tbody>
            </table>

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

        // Générer la liste des mois
        function generateMonthList(startDate, endDate) {
            const months = [];
            let current = new Date(startDate);
            current.setDate(1);
            const end = new Date(endDate);
            end.setDate(1);

            while (current <= end) {
                months.push(new Date(current));
                current.setMonth(current.getMonth() + 1);
            }
            return months;
        }

        // Formater les mois en français
        function formatMonth(date) {
            return date.toLocaleString('fr-FR', {
                month: 'short',
                year: 'numeric'
            });
        }

        // Charger les données des remboursements et reste non emprunté
        function loadData() {
                    document.getElementById('interetForm').addEventListener('submit', function(event) {
                        event.preventDefault();

                function genererPDF(pretId) {
            // Open the PDF generation URL in a new tab/window
            window.open(`http://localhost/WebFinal/ws/create-pdf?pretId=${pretId}`, '_blank');
        }

        function genererPDF(pretId) {
            const url = `${apiBase}/create-pdf?pretId=${pretId}`;
            const xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.responseType = "blob"; // Pour recevoir le PDF
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const blob = new Blob([xhr.response], { type: "application/pdf" });
                    const link = document.createElement("a");
                    link.href = window.URL.createObjectURL(blob);
                    link.download = `recapitulatif_pret_${pretId}.pdf`;
                    link.click();
                } else {
                    //alert("Erreur lors de la génération du PDF.");
                }
            };
            xhr.send();
        }

        // Charger la liste des prêts
        function loadLoans() {
            ajax("GET", "/stats/liste-prets", null, function(response) {
                const tbody = document.getElementById('loanTableBody');
                tbody.innerHTML = '';

                const dateDebut = document.getElementById('dateDebut').value;
                const dateFin = document.getElementById('dateFin').value;

                ajax("GET", `/stats/somme-interets?dateDebut=${encodeURIComponent(dateDebut)}&dateFin=${encodeURIComponent(dateFin)}`, null, function(response) {
                    const tbody = document.getElementById('interestTableBody');
                    tbody.innerHTML = '';

                    if (response.length > 0) {
                        response.forEach(row => {
                            const totalMontant = parseFloat(row.total_interets || 0) + parseFloat(row.total_capital || 0);
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.annee}</td>
                                <td>${row.mois}</td>
                                <td>${parseFloat(row.reste_non_emprunte || 0).toLocaleString('fr-FR')} Ar</td>
                                <td>${parseFloat(row.total_interets || 0).toLocaleString('fr-FR')} Ar</td>
                                <td>${parseFloat(row.total_capital || 0).toLocaleString('fr-FR')} Ar</td>
                                <td>${totalMontant.toLocaleString('fr-FR')} Ar</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        const tr = document.createElement('tr');
                        tr.innerHTML = '<td colspan="6" class="no-data">Aucune donnée disponible</td>';
                        tbody.appendChild(tr);
                    }
                });
            })
        };

        // Charger la sidebar dynamiquement
        function loadSidebar() {
            ajax("GET", "../sidebar.html", null, function(response) {
                document.getElementById('sidebar-placeholder').innerHTML = response;
                setupSidebar();
            });
        }

        // Configurer la navigation de la sidebar
        function setupSidebar() {
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Ajouter la logique de navigation si nécessaire
                });
            });
        }
        // Charger les prêts au démarrage
        document.addEventListener('DOMContentLoaded', loadLoans);
    </script>

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadSidebar();
            loadData();
        });
    </script>
</body>

</html>