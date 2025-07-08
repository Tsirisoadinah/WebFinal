<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Interets</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include('sidebar.html'); ?>

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
                <button type="submit">Fetch Data</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Annee</th>
                        <th>Mois</th>
                        <th>Total Interet</th>
                        <th>Total Capital</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="no-data">Aucune donnée disponible</td>
                    </tr>
                </tbody>
            </table>

            <canvas id="interestChart"></canvas>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost/WebFinal/ws";
        let chartInstance = null;

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

        function formatMonth(date) {
            return date.toLocaleString('fr-FR', { month: 'short', year: 'numeric' });
        }

        document.getElementById('interetForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const dateDebut = document.getElementById('dateDebut').value;
            const dateFin = document.getElementById('dateFin').value;

            ajax("GET", `/stats/somme-interets?dateDebut=${encodeURIComponent(dateDebut)}&dateFin=${encodeURIComponent(dateFin)}`, null, function (response) {
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = '';

                if (response.length > 0) {
                    response.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="montant">${row.annee}</td>
                            <td class="montant">${row.mois}</td>
                            <td class="montant">${row.total_interets}</td>
                            <td class="montant">${row.total_capital}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    const tr = document.createElement('tr');
                    tr.innerHTML = '<td colspan="4" class="no-data">Aucune donnée disponible</td>';
                    tbody.appendChild(tr);
                }

                const months = generateMonthList(dateDebut, dateFin);
                const interestData = months.map(month => {
                    const year = month.getFullYear();
                    const monthNum = month.getMonth() + 1;
                    const dataPoint = response.find(row => parseInt(row.annee) === year && parseInt(row.mois) === monthNum);
                    return dataPoint ? parseFloat(dataPoint.total_interets) : null;
                });

                const labels = months.map(formatMonth);

                if (chartInstance) {
                    chartInstance.destroy();
                }

                const ctx = document.getElementById('interestChart').getContext('2d');
                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Intérêts Mensuels (Ar)',
                            data: interestData,
                            backgroundColor: 'rgba(49, 130, 206, 0.7)', // Bleu professionnel
                            borderColor: 'rgba(49, 130, 206, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Intérêts (Ar)',
                                    font: {
                                        size: 14,
                                        weight: '600'
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Mois',
                                    font: {
                                        size: 14,
                                        weight: '600'
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        let value = context.parsed.y;
                                        return value ? `${value} Ar` : 'Aucune donnée';
                                    }
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>