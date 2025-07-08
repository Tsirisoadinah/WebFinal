<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Financière - Tableau de Bord</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include('sidebar.html'); ?>

    <div class="content">
        <div class="container">
            <h1>Liste des Prêts</h1>
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Type de Prêt</th>
                        <th>Montant (Ar)</th>
                        <th>Taux (%)</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="loanTableBody">
                    <tr>
                        <td colspan="8" class="no-data">Chargement des données...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost/WebFinal/ws";

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    callback(JSON.parse(xhr.responseText));
                }
            };
            xhr.send(data);
        }

        // Fonction pour formater les dates
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function genererPDF(pretId) {
    // Open the PDF generation URL in a new tab/window
    window.open(`http://localhost/WebFinal/ws/create-pdf?pretId=${pretId}`, '_blank');
}

        // Charger la liste des prêts
        function loadLoans() {
            ajax("GET", "/stats/liste-prets", null, function(response) {
                const tbody = document.getElementById('loanTableBody');
                tbody.innerHTML = '';

                if (response.length > 0) {
                    response.forEach(loan => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${loan.client_nom}</td>
                            <td>${loan.type_pret}</td>
                            <td class="montant">${loan.montant_pret.toLocaleString('fr-FR')}</td>
                            <td class="montant">${loan.taux}</td>
                            <td class="date">${formatDate(loan.date_pret)}</td>
                            <td class="date">${formatDate(loan.date_fin)}</td>
                            <td class="btn"><button class="generate-pdf-btn" data-pret-id="${loan.pret_id}">Générer PDF</button></td>
                        `;
                        tbody.appendChild(tr);
                    });

                    // Ajouter des écouteurs pour les boutons "Générer PDF"
                    document.querySelectorAll('.generate-pdf-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const pretId = this.getAttribute('data-pret-id');
                            genererPDF(pretId);
                        });
                    });
                } else {
                    const tr = document.createElement('tr');
                    tr.innerHTML = '<td colspan="8" class="no-data">Aucune donnée disponible</td>';
                    tbody.appendChild(tr);
                }
            });
        }

        // Charger les prêts au démarrage
        document.addEventListener('DOMContentLoaded', loadLoans);
    </script>
    

</body>
</html>