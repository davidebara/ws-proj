document.getElementById('sendFormData').addEventListener('click', function() {
  var urlInput = document.getElementById('countryUrl');
    var url = urlInput.value;

    var urlPattern = /^(ftp|http|https):\/\/[^ "]+$/;

    if (!urlPattern.test(url)) {
      alert('Please enter a valid URL');
      return;
    }
});

function fetchJSONLD() {
  var dateJsonLd;
  $.ajax({
    url: "http://localhost/proiectWS/backend/json-ld.php",
    type: "GET",
    success: function (data) {
      dateJsonLd = data;
    },
  }).done(function () {
    displayJSONLD(dateJsonLd);
  });
}

function displayJSONLD(data) {
  var table = document.getElementById("jsonldTable");
  if (table && table.rows.length > 1) {
    for (var i = table.rows.length - 1; i > 0; i--) {
      table.deleteRow(i);
    }
  }
  if (document.getElementById("eventId").options.length > 0) {
    document.getElementById("eventId").innerHTML = "";
  }
  var data = JSON.parse(data);
  var wars = [];
  data.forEach(function (item) {
    if (!wars.some((war) => war["@id"] === item["eventId"])) {
      wars.push({
        "@id": item["eventId"],
        name: item["event"],
      });
    }
    var row = table.insertRow(-1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);
    cell1.innerHTML = item["event"];
    cell2.innerHTML = item["startDate"];
    cell3.innerHTML = item["endDate"];
    cell4.innerHTML = item["country"];
    cell5.innerHTML =
      '<a href="' + item["countryUrl"] + '">' + item["countryUrl"] + "</a>";
  });

  wars.forEach(function (war) {
    var option = document.createElement("option");
    option.text = war["name"];
    option.value = war["@id"];
    document.getElementById("eventId").add(option);
  });
}

function submitForm() {
  $.ajax({
    type: "POST",
    url: "http://localhost/proiectWS/backend/process.php",
    data: $("#formular").serialize(),
    success: function (data) {
      $("#RezervatAfisare").html("🎖️ Datele din formular și din tabel au fost trimise cu succes către serverul RDF4J");
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
    }
  });
}

function fetchRDF() {
  $.ajax({
    url: "http://localhost/proiectWS/backend/rdf4j.php",
    type: "GET",
    dataType: "json",
    success: function (data) {
      displayBelligerents(data);
    },
    error: function (xhr, status, error) {
      console.error("Error fetching RDF data:", error);
    }
  });
}

function displayBelligerents(data) {
  var tableBody = document.getElementById('belligerentsTable').getElementsByTagName('tbody')[0];

  for (var key in data) {
    if (data.hasOwnProperty(key)) {
      var row = tableBody.insertRow();
      var belligerent = data[key];
      var cell = row.insertCell(0);
      cell.innerHTML = belligerent.eventName.replace(/_/g, ' ');

      cell = row.insertCell(1);
      cell.innerHTML = belligerent.startDate;

      cell = row.insertCell(2);
      cell.innerHTML = belligerent.endDate;

      cell = row.insertCell(3);
      cell.innerHTML = belligerent.countryName;

      cell = row.insertCell(4);
      cell.innerHTML = '<a href="' + belligerent.countryUrl + '">' + belligerent.countryUrl + '</a>';
    }
  }
}

function insertJSON() {
  $('#resttext').html("🔄 Datele sunt în curs de inserare...");
  $.ajax({
    url: "http://localhost/proiectws/backend/insertREST.php",
    type: "GET",
    success: function (data) {
      $('#resttext').html(data);
    }
  })
}

function fetchInsertedJSON() {
  $.ajax({
    url: "http://localhost/proiectws/backend/displayREST.php",
    type: "GET",
    success: function (data) {
      $("#insertedJSONData").html(data);
    }
  })
}

function displayLlmText() {
  var dateJsonLd;
  $("#llmText").html("🔄 Răspunsul este în curs de generare...");
  $.ajax({
    url: "http://localhost/proiectWS/backend/openai-api.php",
    type: "GET",
    success: function (response) {
      $("#llmText").html(response);
    },
  });
}