<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fuzzy C-Means</title>
  <style>
    table {
      display: block;
      max-width: -moz-fit-content;
      max-width: fit-content;
      margin: 0 10px;
      overflow-x: auto;
      white-space: nowrap;
      border-radius: 10px;

      border-collapse: collapse;
      font-size: 0.9em;
      font-family: sans-serif;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    table.data-x {
      border-collapse: collapse;
      font-size: 0.9em;
      font-family: sans-serif;
      border: 2px solid #ddd;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    table.data-uik {
      /* make table center */
      margin: 0 auto;
    }

    thead tr {
      background-color: #009879;
      color: #ffffff;
      text-align: left;
    }

    th, td {
      padding: 12px 15px;
    }
    
    /* center item in table */
    td {
      text-align: center;
    }

    tbody tr {
      border-bottom: 1px solid #dddddd;
    }

    tbody tr:nth-of-type(even) {
      background-color: #f3f3f3;
    }

    tbody tr:last-of-type {
      border-bottom: 2px solid #009879;
    }

    tbody tr:hover {
      font-weight: bold;
      color: #009879;
      background-color: #f3f3f3;
    }

    h1, h2, h3 {
      text-align: center;
      font-size: 1.5em;
      font-family: sans-serif;
      margin: 0 0 7px 0;
    }

    h1 {
      margin-top: 3rem;
    }
  </style>
</head>
<body>
  <?php
    echo "<h1>Program FCM Clustering</h1>";
    echo "<h2>Matakuliah : Text Processing Metode FCM Clustering</h2>";
    echo "<h2>Mengunakan Jarak Mahattan</h2>";
    echo "</br>";
    echo "<h2>Muhammad Dzalhaqi - 3321600023</h2>";
    /* Metode jarak lainnya:
      1. Mahalanobis
      2. Hamming Distance
      3. Cossine Similarity
      5. Dice
      6. Overlap
      7. Minkowski, 
      8. Jaccard 
    */

    require_once 'C:\Users\admin\vendor\autoload.php';
    use Phpoffice\PhpSpreadsheet\Spreadsheet;
    use Phpoffice\phpspreadsheet\Writer\Xlsx;

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('Cluster.xlsx');
    $worksheet = $spreadsheet -> getActiveSheet();
    $data = $worksheet -> toArray();

    $spreadsheet_data_u = \PhpOffice\PhpSpreadsheet\IOFactory::load('data_u.xlsx');
    $worksheet_u = $spreadsheet_data_u -> getActiveSheet();
    $data_u = $worksheet_u -> toArray();

    $DataX = array();
    for ($i = 1; $i < count($data); $i++) {
        $DataX[] = $data[$i];
    }

    $DataU = array();
    for ($i = 0; $i < count($data_u); $i++) {
        $DataU[] = $data_u[$i];
    }

    $barisX=count($DataX);
    $kolomX=count($DataX[0]);

    echo "<br>";
    echo "<br>";
    echo "<h3>Matriks Data X: </h3> <br>";
    echo "<table class='data-x'>";
    // cetak setiap baris dan kolom ke dalam bentuk table
    echo "<tbody>";
    for ($i = 0; $i < count($DataX); $i++) {
        echo "<tr>";
        for ($j = 0; $j < count($DataX[$i]); $j++) {
            echo "<td>".$DataX[$i][$j]."</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";

    echo "<br>";
    echo "<br>";
    echo "<h3>Transpose Matriks Data X: </h3> <br>";
    echo "<table class='data-x'>";
    // cetak setiap baris dan kolom ke dalam bentuk table
    echo "<tbody>";
    for ($j = 0; $j < count($DataX[1]); $j++) {
        echo "<tr>";
        for ($i = 0; $i < count($DataX); $i++) {
            echo "<td>".$DataX[$i][$j]."</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";

    echo "<br>";

    echo "<br>";
    echo "<br>";
    echo "<h1>Matriks Data Uik: </h1><br>";
    echo "<br>";
    echo "<table class='data-uik'>";
    // cetak setiap baris dan kolom ke dalam bentuk table
    echo "<tbody>";
    for ($i = 0; $i < count($DataU); $i++) {
        echo "<tr>";
        for ($j = 0; $j < count($DataU[$i]); $j++) {
            echo "<td>".$DataU[$i][$j]."</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";

    echo "<br>";
    echo "<br>";

    //Proses looping FCM
    $error=0.0001;
    $P[0]=0;
    $t=1;
    $Pobj=2*$error;
    $JumlahKelas=count($DataU[1]);

    do{
      echo "Iterasi ke=".$t."<br>";
      echo "---------------<br>";
        echo "Matrik Transpose U <br>";
        for($j=0;$j<3;$j++)
        {
          $sigma2U[$j]=0;
        }

        for($j=0;$j<$JumlahKelas;$j++)
        {
          for($i=0;$i<$barisX;$i++)
          {
          $sigma2U[$j]+=pow($DataU[$i][$j],2);
          echo pow($DataU[$i][$j],2)." ";
          }
          
          echo "=".$sigma2U[$j]."<br>";
        }

        echo "<br>";
        echo "<br>";	

          
          
          //matrik nilai awal untuk menjumlah matrik transpose 
          for($u=0;$u<$JumlahKelas;$u++)
          {
            for($i=0;$i<$barisX;$i++)
            {
            for($j=0;$j<$kolomX;$j++)
              $SigmaU2X[$u][$j][$i]=0;
            }
          }
          
            
          //Mencari nilai sigma U^2Xij
          //Vkj=sgma(uij*Xij)/sigma U2

          for($u=0;$u<$JumlahKelas;$u++)
          {
            for($i=0;$i<$barisX;$i++)
            {
            for($j=0;$j<$kolomX;$j++)
            {
              
              
              echo number_format(pow($DataU[$i][$u],2)*$DataX[$i][$j],3)." ";
              
            }
            echo  "<br>";
            }
            echo  "<br>";
          }
          
          echo "<br>";
          
          echo "Nilai awal untuk mtrik U<br>";
          for($i=0;$i<$barisX;$i++)
          {
            for($j=0;$j<$kolomX;$j++)
            {
              $sigma2[$i][$j]=0;
            }
          }
          echo "Matrik Sigma Transpose<br>";
          
          $z=1;
          for($u=0;$u<$JumlahKelas;$u++)
          {
            for($j=0;$j<$kolomX;$j++)
            {
            echo "(".$z.") ";
            for($i=0;$i<$barisX;$i++)
            {
              $SigmaU2X[$u][$j][$i]+=pow($DataU[$i][$u],2)*$DataX[$i][$j];
              echo number_format($SigmaU2X[$u][$j][$i],3)." ";
              $sigma2[$u][$j]+=$SigmaU2X[$u][$j][$i];
            }
            echo "<br>";
            $z+=1;
            }
            echo "<br>";
            $z=1;
          }
          
          echo "<br>";

        //Hasil Sigma U^2Xij dan mencari Vkj 
        for($u=0;$u<$JumlahKelas;$u++)
        {
          echo "Sigma dari perkalian U2X Kelompok=".($u+1)."<br>";
          
          for($x=0;$x<$kolomX;$x++)
          {
          echo number_format($sigma2[$u][$x],3)." ";

          }
          echo "<br>";
        }
        
        //mencari Vkj
        for($k=0;$k<3;$k++)
          {
            for($j=0;$j<$kolomX;$j++)
              $V[$k][$j]=0;
            
          }

        echo "Mencari Vkj Pusat Cluster<br>";
        $z=1;
        for($k=0;$k<3;$k++)
        {
          echo "V".$z."=";
          for($j=0;$j<$kolomX;$j++)
          {
            $V[$k][$j]=$sigma2[$k][$j]/$sigma2U[$k];
          echo number_format($V[$k][$j],3)." ";

          }
          $z+=1;
          echo "<br>";
        }
        
        //Perhitungan Cluster C1 jarak antara Xij dengan Vkj
        //𝑽𝒌j=Xij*Vkj
        echo "Perhitungan  jarak antara Xij dengan Vkj<br>";
        $z=1;
        //Ci
        for($k=0;$k<$JumlahKelas;$k++)
        {
          for($i=0;$i<$barisX;$i++)
            {
              $C[$u][$i]=0;
            }
        }
          
        //(Xij-Vkj)^2 berdasarkan ecluidean
          
        for($k=0;$k<$JumlahKelas;$k++)
        {
            //$SigmaP[$k]=0;
            
            echo "Perhitungan Cluster C".$z."<br>";
            for($i=0;$i<$barisX;$i++)
            {
              $C[$k][$i]=0;
              $SigmaCU2[$k][$i]=0;
              for($j=0;$j<$kolomX;$j++)
              {
                //Cara Stabdart FCM=total (Xij-Vkj)^2 jika menggunakan ecluidean
                
                //$C[$k][$i]+=pow(($DataX[$i][$j]-$V[$k][$j]),2);
                // echo number_format(pow(($DataX[$i][$j]-$V[$k][$j]),2),5)."  ";
                
              //Cara Lain:Jika Mahattan
                $C[$k][$i]+=abs($DataX[$i][$j]-$V[$k][$j]);
                
                echo number_format(abs($DataX[$i][$j]-$V[$k][$j]),5)."  ";
              }
              $SigmaCU2[$k][$i]=$C[$k][$i]*pow($DataU[$i][$k],2);
              echo "=".number_format($C[$k][$i],5)."=>".number_format($SigmaCU2[$k][$i],5)."<br>";
              //$sigmaP[$k]+= $SgmaCU2;
              //$SgmaCU2=0;
            }
            echo "<br>";
            //$SgmaCU2=0;
            $z+=1;
          
        }
        //Mengitung Funsgi Objektiv Perhitungan
        echo "Perhitungan Funsgi Obyektive P<br>";
        $sigmaP=0;
        $sigmaPtot=0;
        for($i=0;$i<$barisX;$i++)	
        {
          $sigmaP=0;
          for($k=0;$k<$JumlahKelas;$k++)
          {
            $sigmaP+=$SigmaCU2[$k][$i];
            //$sigmaPtot+=$sigmaP;
          }
          $sigmaPtot+=$sigmaP;
          echo number_format($sigmaP,5)."<br>";
          
        }
        echo "Sigma Total P=".number_format($sigmaPtot,5)."<br>";
            
        $P[$t]=$sigmaPtot;
        //Mengupdate matrik U baru
        // U1=SigmaCi1/(SigmaCi1+SigmaCi2)
        
        for($k=0;$k<$JumlahKelas;$k++)
        {
          for($i=0;$i<$barisX;$i++)
          {
            if($k!=$JumlahKelas)
              $DataU[$k][$i]=$C[$k][$i]/($C[$k][$i]+$C[$k+1][$i]);
            if($k==($JumlahKelas-1))
              $DataU[$k][$i]=$C[$k][$i]/($C[$k][$i]+$C[$k-1][$i]);
            echo number_format($DataU[$k][$i],3)." ";
          }
            echo "<br>";
        }
        //Matrik U di transpose menjadi Uik
        
        echo "Matrik Transpose Uik:<br>";
        for($i=0;$i<$barisX;$i++)
        {
          for($k=0;$k<$JumlahKelas;$k++)
            echo number_format($DataU[$k][$i],3)." ";
            echo "<br>";
        }
        echo "Check Fungsi Objective =<br>";
        echo "Nilai P t-1=".$P[$t-1]."<br>";
        echo "Nilai P t=".$P[$t]."<br>";
        $Pobj=abs($P[$t-1]-$P[$t]);
        echo "Nilai Pobj=".$Pobj."<br>";
        $t+=1;
        echo "###############################<br>";
        
    }while(abs($Pobj)>=$error);

    echo "Hasil FCM anggota Cluster:<br>";
    echo "____________________________<br>";
    $max[0]=0;

    for($i=0;$i<$barisX;$i++)
    {
      $anggotaCluster=1;
      $max=$DataU[0][$i];
      for($k=0;$k<$JumlahKelas;$k++)
      {
        echo number_format($DataU[$k][$i],3)." ";
        if($max<$DataU[$k][$i])
        {
          $max=$DataU[$k][$i];
          $anggotaCluster=$k+1;
        }
        //echo "Cluster=".$k."";
        
      }
      
      echo "Cluster=".$anggotaCluster;
      echo "<br>";
    }

    ?>
    
</body>
</html>