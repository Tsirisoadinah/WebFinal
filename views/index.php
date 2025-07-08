<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des étudiants</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <?php include('sidebar.html'); ?>
  
  <div class="content">
      <div class="container">
        <button onclick="genererPDF()">Générer PDF</button>

        <h1>Gestion des étudiants</h1>

        <div class="form-section">
          <input type="hidden" id="id">
          <input type="text" id="nom" placeholder="Nom">
          <input type="text" id="prenom" placeholder="Prénom">
          <input type="email" id="email" placeholder="Email">
          <input type="number" id="age" placeholder="Âge">
          <button onclick="ajouterOuModifier()">Ajouter / Modifier</button>
        </div>

        <table id="table-etudiants">
          <thead>
            <tr>
              <th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Âge</th><th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
  </div>

  <script>
    const apiBase = "http://localhost/WebFinal/ws";

    function genererPDF() {
      const url = `${apiBase}/create-pdf`;
      const xhr = new XMLHttpRequest();
      xhr.open("GET", url, true);
      xhr.responseType = "blob"; // Pour recevoir le PDF
      xhr.onload = function() {
        if (xhr.status === 200) {
          const blob = new Blob([xhr.response], { type: "application/pdf" });
          const link = document.createElement("a");
          link.href = window.URL.createObjectURL(blob);
          link.download = "etudiants.pdf";
          link.click();
        } else {
          // alert("Erreur lors de la génération du PDF.");
        }
      };
      xhr.send();
    }

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

    function chargerEtudiants() {
      ajax("GET", "/etudiants", null, (data) => {
        const tbody = document.querySelector("#table-etudiants tbody");
        tbody.innerHTML = "";
        data.forEach(e => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${e.id}</td>
            <td>${e.nom}</td>
            <td>${e.prenom}</td>
            <td>${e.email}</td>
            <td>${e.age}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(e)})'>✏️</button>
              <button onclick='supprimerEtudiant(${e.id})'>🗑️</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      });
    }

    function ajouterOuModifier() {
      const id = document.getElementById("id").value;
      const nom = document.getElementById("nom").value.trim();
      const prenom = document.getElementById("prenom").value.trim();
      const email = document.getElementById("email").value.trim();
      const age = document.getElementById("age").value;

      const data = `nom=${encodeURIComponent(nom)}&prenom=${encodeURIComponent(prenom)}&email=${encodeURIComponent(email)}&age=${age}`;

      if (id) {
        ajax("PUT", `/etudiants/${id}`, data, () => {
          resetForm();
          chargerEtudiants();
        });
      } else {
        ajax("POST", "/etudiants", data, () => {
          resetForm();
          chargerEtudiants();
        });
      }
    }

    function remplirFormulaire(e) {
      document.getElementById("id").value = e.id;
      document.getElementById("nom").value = e.nom;
      document.getElementById("prenom").value = e.prenom;
      document.getElementById("email").value = e.email;
      document.getElementById("age").value = e.age;
    }

    function supprimerEtudiant(id) {
      if (confirm("Supprimer cet étudiant ?")) {
        ajax("DELETE", `/etudiants/${id}`, null, () => {
          chargerEtudiants();
        });
      }
    }

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("nom").value = "";
      document.getElementById("prenom").value = "";
      document.getElementById("email").value = "";
      document.getElementById("age").value = "";
    }

    chargerEtudiants();
  </script>

</body>
</html>