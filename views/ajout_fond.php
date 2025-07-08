<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Établissements Financiers</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include('sidebar.html'); ?>

    <div class="content">
        <div class="container">
            <h2 style="color:#333">Gestion des Établissements Financiers</h2>
            
            <p id="solde">Solde actuel : <span id="solde-valeur">Chargement...</span></p>

            <h3>Ajouter un montant</h3>
            <form action="" method="post" id="formulaire-solde">
                <label for="montant">Montant</label>
                <input type="number" name="montant" id="montant" min="0" step="0.01" placeholder="Entrez le montant" required>
                <button type="submit" id="submit-btn">Valider</button>
            </form>
            <p id="message"></p>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost/WebFinal/ws";

        chargerFonds();

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

        document.getElementById("formulaire-solde").addEventListener("submit", function(event) {
            event.preventDefault();
            ajouterFonds();
        });

        function ajouterFonds() {
            const montantInput = document.getElementById("montant");
            const montant = montantInput.value.trim();
            const submitBtn = document.getElementById("submit-btn");
            const message = document.getElementById("message");

            if (!montant || isNaN(montant) || parseFloat(montant) <= 0) {
                message.style.display = "block";
                message.style.color = "#dc3545";
                message.innerText = "Veuillez entrer un montant valide supérieur à zéro.";
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerText = "En cours...";

            const data = `montant=${encodeURIComponent(montant)}`;
            
            ajax("POST", "/etablissement/fonds", data, function(response) {
                message.style.display = "block";
                message.style.color = "#28a745";
                message.innerText = "Montant ajouté avec succès !";
                montantInput.value = "";
                submitBtn.disabled = false;
                submitBtn.innerText = "Valider";
                chargerFonds();
                setTimeout(() => {
                    message.style.display = "none";
                }, 3000);
            });
        }

        function chargerFonds() { 
            ajax("GET", "/etablissement/fonds", null, (data) => {
                const solde = document.querySelector("#solde-valeur");
                solde.innerText = `${data} MGA`;
            });
        }
    </script>
</body>
</html>