<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OOP in PHP</title>
</head>
<body>
    <?php
        include('simple_html_dom.php');

        interface iRadovi{
            public function create($data);
            public function save($data);
            public function read(); 
        }

        class DiplomskiRadovi implements iRadovi {
            public $naziv_rada=NULL;
            public $tekst_rada=NULL;
            public $link_rada=NULL;
            private $oib_tvrtke=NULL;

            function __construct() {}

            public function create($data){
                $this->naziv_rada = $data['naziv_rada'];
                $this->tekst_rada = $data['tekst_rada'];
                $this->link_rada = $data['link_rada'];
                $this->oib_tvrtke = $data['oib_tvrtke'];
            }

            public function save($data){        
                $this->create($data);
                $server_name = "localhost";
                $user_name = "root";
                $password = "";
                $dbname = "radovi";

                $connection = new mysqli($server_name, $user_name, $password, $dbname);

                if (!$connection) {
                    die("Fail ". mysqli_connect_error());
                }
                echo "Connected.";

                $naziv = $this->naziv_rada;
                $tekst = $this->tekst_rada;
                $link = $this->link_rada;
                $oib = $this->oib_tvrtke;

                $sql = "INSERT INTO diplomski_radovi (naziv_rada, tekst_rada, link_rada, oib_tvrtke) VALUES ('$naziv', '$tekst', '$link', '$oib')";
                $connection->query($sql);
                
                $connection->close();
            }

            public function read(){
                $server_name = "localhost";
                $user_name = "root";
                $password = "";
                $dbname = "radovi";
        
                $connection = new mysqli($server_name, $user_name, $password, $dbname);
                if (!$connection) {
                    die("Fail ". mysqli_connect_error());
                }
                echo "Connected.";
        
                $radovi = array();
        
                $sql = "SELECT * FROM diplomski_radovi";
                $final = $connection->query($sql);
        
                if ($final->num_rows > 0) {
                    while($row = $final->fetch_assoc()) {
        
                        array_push($radovi, $row); 
                    }
                }         
        
                $connection->close();
                return $radovi;
            }



        }

        $url = "https://stup.ferit.hr/index.php/zavrsni-radovi/page/2";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
        $content = curl_exec($curl);
        
        $fp  = fopen($url, 'r');
        $read = fgetcsv($fp);
        $read = file_get_html($url);
        foreach($read->find('article') as $article) {
            foreach($article->find('ul[class=slides] img') as $img) {}
            foreach($article->find('h2[class=entry-title] a') as $link) {
                $html = file_get_html($link->href);
                
                foreach($html->find('.post-content') as $text) {}
            }
            $data = array(
                'naziv_rada' => $link->plaintext,
                'tekst_rada' => $text->plaintext,
                'link_rada' => $link->href,
                'oib_tvrtke' => preg_replace('/[^0-9]/', '', $img->src)
            );

            $rad = new DiplomskiRadovi();
            $rad->save($data);            
        }
        fclose($fp);
        curl_close($curl);

        $diplomskiRad = new DiplomskiRadovi();
        $radovi = $diplomskiRadovi->read();
        foreach($radovi as $key=>$value) {
            echo "<b>Id:</b> {$value['ID']}<br><b> Naziv rada:</b> {$value['naziv_rada']}<br><b>  Tekst rada:</b> {$value['tekst_rada']}<br><b> Link rada:</b> {$value['link_rada']}<br><b> OIB tvrtke:</b> {$value['oib_tvrtke']}<br><br>";
        }

    ?>
</body>
</html> 