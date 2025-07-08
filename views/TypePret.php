<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Type Prêt</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include('sidebar.html'); ?>
    
    <div class="content">
        <div class="container">
            <h1>Gestion de Type Prêt</h1>
            
            <div class="form-section">
                <h3>Ajouter / Modifier un type de prêt</h3>
                <input type="hidden" id="id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="libelle">Libellé :</label>
                        <input type="text" id="libelle" name="libelle" placeholder="Ex : Prêt Étudiant" required>
                    </div>
                    <div class="form-group">
                        <label for="taux">Taux (%) :</label>
                        <input type="number" id="taux" name="taux" placeholder="Ex: 18.3" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="duree_max">Durée maximale (mois) :</label>
                        <input type="number" id="duree_max" name="duree_max" placeholder="Durée en mois" min="1" required>
                    </div>
                </div>
                
                <div class="button-group">
                    <button onclick="ajouterOuModifier()" id="actionButton">Ajouter</button>
                    <button onclick="resetForm()" id="resetButton" class="secondary">Annuler</button>
                </div>
            </div>
            
            <div class="table-container">
                <table id="table-typepret">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Libellé</th>
                            <th>Taux (%)</th>
                            <th>Durée maximale (mois)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <div id="message"></div>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost/WebFinal/ws";
        
function ajax(method, url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, apiBase + url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 || xhr.status === 201) { // Ajoutez le code 201 ici
                callback(JSON.parse(xhr.responseText));
            } else {
                console.error('Erreur:', xhr.status, xhr.statusText);
                afficherMessage("Erreur lors de la requête", true);
            }
        }
    };
    xhr.send(data);
}

        function chargerTypePret() {
            ajax("GET", "/typepret", null, (data) => {
                const tbody = document.querySelector("#table-typepret tbody");
                tbody.innerHTML = "";
                data.forEach(e => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${e.id}</td>
                        <td>${e.libelle}</td>
                        <td>${e.taux}%</td>
                        <td>${e.duree_max}</td>
                        <td>
                            <div class="actions">
                                <button onclick='remplirFormulaire(${JSON.stringify(e)})' class="edit">Modifier</button>
                                <button onclick='supprimerTypePret(${e.id})' class="danger">Supprimer</button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
        }

        function remplirFormulaire(data) {
            document.getElementById("id").value = data.id;
            document.getElementById("libelle").value = data.libelle;
            document.getElementById("taux").value = data.taux;
            document.getElementById("duree_max").value = data.duree_max;
            document.getElementById("actionButton").textContent = "Modifier";
        }

        function ajouterOuModifier() {
            const id = document.getElementById("id").value;
            const libelle = document.getElementById("libelle").value.trim();
            const taux = document.getElementById("taux").value;
            const duree_max = document.getElementById("duree_max").value;

            if (!libelle || !taux || !duree_max) {
                afficherMessage("Veuillez remplir tous les champs", true);
                return;
            }

            if (parseFloat(taux) < 0) {
                afficherMessage("Le taux doit être positif", true);
                return;
            }

            if (parseInt(duree_max) < 1) {
                afficherMessage("La durée maximale doit être d'au moins 1 mois", true);
                return;
            }

            const data = `libelle=${encodeURIComponent(libelle)}&taux=${encodeURIComponent(taux)}&duree_max=${encodeURIComponent(duree_max)}`;

            if (id) {
                ajax("PUT", `/typepret/${id}`, data, () => {
                    afficherMessage("Type de prêt modifié avec succès !");
                    chargerTypePret();
                    resetForm();
                });
            } else {
                ajax("POST", "/typepret", data, () => {
                    afficherMessage("Type de prêt ajouté avec succès !");
                    chargerTypePret();
                    resetForm();
                });
            }
        }

        function supprimerTypePret(id) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce type de prêt ?")) {
                ajax("DELETE", `/typepret/${id}`, null, () => {
                    afficherMessage("Type de prêt supprimé avec succès !");
                    chargerTypePret();
                });
            }
        }

        function resetForm() {
            document.getElementById("id").value = "";
            document.getElementById("libelle").value = "";
            document.getElementById("taux").value = "";
            document.getElementById("duree_max").value = "";
            document.getElementById("actionButton").textContent = "Ajouter";
        }

        function afficherMessage(texte, erreur = false) {
            const message = document.getElementById("message");
            message.style.display = "block";
            message.textContent = texte;
            
            if (erreur) {
                message.classList.add("message-error");
            } else {
                message.classList.remove("message-error");
            }
            
            setTimeout(() => {
                message.style.display = "none";
            }, 3000);
        }
        chargerTypePret();
    </script>
</body>
</html>