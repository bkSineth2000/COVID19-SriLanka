<!DOCTYPE html>
<html lang="en" xmlns:th="http://www.w3.org/1999/xhtml" xmlns:padding-left="http://www.w3.org/1999/xhtml">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <title>COVID19-Srilanka</title>
    <style>
.card {
    background-color: lightgray;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
  max-width: 300px;
  margin: auto;
  text-align: center;
  font-family: arial;
}

.title {
  color: black;
  font-size: 18px;
}


a {
  text-decoration: none;
  font-size: 22px;
  color: black;
}

.centered {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

#hospitals {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 75%;
  
}

#hospitals td, #hospitals th {
  border: 1px solid #ddd;
  padding: 5px;
  text-align:center;
}

#hospitals tr:nth-child(even){background-color: #f2f2f2;}

#hospitals tr:hover {background-color: #ddd;}

#hospitals th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: center;
  background-color: lightblue;
  color: black;
}

</style>
<body>

<div class="container">
    <div class="jumbotron">
        <h1>COVID-19 SriLanka</h1>
        <h5>Get Latest Updates Of Coronavirus In Srilanka.</h5>
    </div>
</div>
<?php
    
    class AvailableHospitalStat
    {
        protected $hospitalName_sinhala;
        protected $hospitalName_english;
        protected $hospitalName_tamil;
        protected $localPatientsOnTreatements;
        protected $foreignPatientsOnTreatements;
        protected $totalPatientsAtHospitalOnTreatements;
    }
    class SrilankaLatestCoronaStat extends AvailableHospitalStat
    {
        private $totalCases;
        private $totalNewCases;
        private $totalActiveCases;
        private $totalRecovered;
        private $totalDeaths;
        private $totalNewDeaths;
        private $lastUpdate;
        public $hospitalStack;

        function __construct()
        {
            $this->hospitalStack = array();
            array_unshift($this->hospitalStack, 23);
        }
        function setLatestUpdatesOfRecentCases($content)
        {
            if($content["success"])
            {
                $index = 0;    
            foreach($content["data"] as $dataKey => $dataVal)
            {
                if($dataKey == "hospital_data")
                {
                    for($i = 0; $i < count($dataVal); $i++)
                    {
                        foreach($dataVal[$i] as $hospitalDataKey=> $hospitalDataVal)
                        {
                            if($hospitalDataKey == "hospital")
                            {
                                foreach($hospitalDataVal as $hospitalIdentityKey=>$hospitalIdentityVal)
                                {
                                    switch($hospitalIdentityKey)
                                    {
                                        case "name":
                                            $this->hospitalStack[$index]->hospitalName_english = $hospitalIdentityVal;
                                        break;

                                        case "name_si":
                                            $this->hospitalStack[$index]->hospitalName_sinhala = $hospitalIdentityVal;
                                        break;

                                        case "name_ta":
                                            $this->hospitalStack[$index++]->hospitalName_tamil = $hospitalIdentityVal;
                                        break;
                                    }
                                }
                            }
                            else{
                                switch($hospitalDataKey)
                                {
                                    case "treatment_local":
                                        $this->hospitalStack[$index] = new AvailableHospitalStat();
                                        $this->hospitalStack[$index]->localPatientsOnTreatements = $hospitalDataVal;
                                    break;

                                    case "treatment_foreign":
                                        $this->hospitalStack[$index]->foreignPatientsOnTreatements = $hospitalDataVal;
                                    break;

                                    case "treatment_total":
                                        $this->hospitalStack[$index]->totalPatientsAtHospitalOnTreatements = $hospitalDataVal;
                                    break;
                                }
                            }
                        }
                    }
                }
                else
                {
                    switch($dataKey)
                    {   
                        case "update_date_time":
                            $this->lastUpdate = $dataVal;
                        break;

                        case "local_new_cases":
                            $this->totalNewCases = $dataVal;
                        break;

                        case "local_total_cases":
                            $this->totalCases = $dataVal;
                        break;

                        case "local_deaths":
                            $this->totalDeaths = $dataVal;
                        break;

                        case"local_new_deaths":
                            $this->totalNewDeaths = $dataVal;
                        break;

                        case "local_recovered":
                            $this->totalRecovered = $dataVal;
                        break;

                        case "local_active_cases":
                            $this->totalActiveCases = $dataVal;
                        break;
                    }
                }
            }
        }
        else
            echo "<br>Sorry! Unfortunately Latest Updates Haven't Received Successfully..";
        }
            
        function getLatestUpdates()
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://hpb.health.gov.lk/api/get-current-statistical",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"
            ),
        ));
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
            
            $response = curl_exec($curl);
            $content = json_decode($response, true);

            $err = curl_error($curl);
            curl_close($curl);

            return $content;
        }
        
        function getTotalCases()
        {
            return $this->totalCases;
        }
        function getTotalNewCases()
        {
            return $this->totalNewCases;
        }
        function getTotalActiveCases()
        {
            return $this->totalActiveCases;
        }
        function getTotalRecovered()
        {
            return $this->totalRecovered;
        }
        function getTotalDeaths()
        {
            return $this->totalDeaths;
        }
        function getTotalNewDeaths()
        {
            return $this->totalNewDeaths;
        }
        function getLastUpdate()
        {
            return $this->lastUpdate;
        }
        function getHospitalNameInSinhala($index)
        {
            return $this->hospitalStack[$index]->hospitalName_sinhala;
        }
        function getHospitalNameInEnglish($index)
        {
            return $this->hospitalStack[$index]->hospitalName_english;
        }
        function getHospitalNameInTamil($index)
        {
            return $this->hospitalStack[$index]->hospitalName_tamil;
        }
        function getLocalPatientsOnTreatements($index)
        {
            return $this->hospitalStack[$index]->localPatientsOnTreatements;
        }
        function getForeighnPatientsOnTreatements($index)
        {
            return $this->hospitalStack[$index]->foreignPatientsOnTreatements;
        }
        function getTotalParentsOnTreatments($index)
        {
            return $this->hospitalStack[$index]->totalPatientsAtHospitalOnTreatements;
        }
    
        function retrieveLatestData()
        {
            echo "<div class='alert alert-primary'>
            <h3>&emsp;&emsp;&emsp;&emsp;Total Cases:&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;".$this->getTotalCases()."<strong class='display-4'></strong></h3>
            <h3>&emsp;&emsp;&emsp;&emsp;New Cases: &emsp;&emsp;&emsp;&emsp;&nbsp; ".$this->getTotalNewCases()."<strong class='display-4'></strong></h3>
    
        </div>";

        echo "<div class='alert alert-warning'>
        <h3>&emsp;&emsp;&emsp;&emsp;Active Cases:  &emsp;&emsp;&emsp;&nbsp;&nbsp; ".$this->getTotalActiveCases()."<strong class='display-4'></strong></h3>
    </div>";

        echo "<div class='alert alert-success'>
        <h3>&emsp;&emsp;&emsp;&emsp;Recovered Cases: &emsp;&nbsp;&nbsp; ".$this->getTotalRecovered()."<strong class='display-4'></strong></h3>
    </div>";
        
    echo "<div class='alert alert-danger'>
    <h3>&emsp;&emsp;&emsp;&emsp;Death Cases: &emsp;&emsp;&emsp;&nbsp;&nbsp; ".$this->getTotalDeaths()."<strong class='display-4'></strong></h3>
    <h3>&emsp;&emsp;&emsp;&emsp;New Deaths: &emsp;&emsp;&emsp;&nbsp;&nbsp; ".$this->getTotalNewDeaths()."<strong class='display-4'></strong></h3>
</div>";
echo "<br><h3 align = 'center'>Quarantine centres currenty on treatments & observations</h3><br>
<table id='hospitals' table align='center'> 
<tr>
  <th>Hospital Name</th>
  <th>Local Patients On Treatements</th>
  <th>Foreign Patients On Treatements</th>
  <th>Total Patients On Treatements</td>
</tr>";
for($i = 0; $i < 23; $i++)
    {
        echo "<tr>";
        echo "<td>".$this->getHospitalNameInSinhala($i)."<br>".
        $this->getHospitalNameInTamil($i)."<br>".
        $this->getHospitalNameInEnglish($i)."</td>";
        echo "<td>".$this->getLocalPatientsOnTreatements($i)."</td>".
        "<td>".$this->getForeighnPatientsOnTreatements($i)."</td>".
        "<td>".$this->getTotalParentsOnTreatments($i)."</td></tr>";
    }
    echo "</table><br>";
echo "<div class='alert alert-success' role='alert'>
<h4 class='alert-heading'>Dear Srilankans! <br>Follow these prevention strategies to avoid COVID-19</h4>
<p>1.Wash your hands frequently</p>
<p>2.Maintain social distancing</p>
<p>3.Avoid touching eyes, nose and mouth</p>
<p>4.Practice respiratory hygiene</p>
<p>5.If you have fever, cough and difficulty in breathing, seek medical care early</p>
<hr>
<b><p class='mb-0'>Let's get together to fight against ghost CORONA with representing out nation and pride!</p></b>
</div>";

echo "<div class='alert alert-info' role='alert'>
<h4>Last update : &emsp;&emsp;".$this->getLastUpdate()."</h4> </h4>
</div>";

echo "<div class='card'>
<h2>Developer Info</h2>
<img src='smallboy.jpg' alt='Photo-missed' style='width:100%'>
<h2>Sineth Sankalpa</h2>
<p class='title'>social partner</p>
<a href='https://www.facebook.com/search/top/?q=sineth%20bogala&epa=SEARCH_BOX' target='_blank'><i class='fa fa-facebook'></i></a>
</div>

<p>API from <a href='https://hpb.health.gov.lk' target='_blank'>Health Promotion Bureau - Srilanka</a></p>";
        }
    }
    
    $srilankaLatestCoronaStat = new SrilankaLatestCoronaStat();
    $responseContent = $srilankaLatestCoronaStat->getLatestUpdates();

    $srilankaLatestCoronaStat->setLatestUpdatesOfRecentCases($responseContent);
    
    $srilankaLatestCoronaStat->retrieveLatestData();
?>