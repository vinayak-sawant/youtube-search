<?php 

/**
 * This application lists the data fetched by quering youtube search api:
 *
 * 1. Search for youtube video by keywords
 * 2. The results will be displayed using JQuery Datatables
 * 3. All the search, sort operations will be handled by datatable.
 *
 * @author Vinayak Sawant
*/

/**
 * Library Requirements
 *
 * 1. Install composer (https://getcomposer.org)
 * 2. On the command line, change to this directory
 * 3. Require the google/apiclient library
 *    $ composer require google/apiclient:~2.0
*/

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ . '"');
}

require_once __DIR__ . '/vendor/autoload.php';

$tableBody   = '';
$searchQuery = '';

if (isset($_GET['search_query'])) {
    
    $searchQuery = $_GET['search_query'];
    
    // This is personal DEVELOPER_KEY it has some limitations (maximum number of api calls) - Vinayak 
    $DEVELOPER_KEY = 'AIzaSyBVyA580n3pCZo37qrx4jCJVw_CwAOqq6E';
    
    $client = new Google_Client();
    $client->setDeveloperKey($DEVELOPER_KEY);
    
    $youtube = new Google_Service_YouTube($client);
    
    $htmlBody  = '';
    $tableBody = '';
    
    try {

        $searchResponse = $youtube->search->listSearch('id,snippet', array(
            'q' => $searchQuery,
            'maxResults' => 50,
            'type' => 'video'
        ));
        
        foreach ($searchResponse['items'] as $searchResult) {
            $tableBody .= '<tr>';
            
            $tableBody .= '<td><a href="https://www.youtube.com/watch?v=' . $searchResult['id']['videoId'] . '" target="_blank"><img src="' . $searchResult['snippet']['thumbnails']['default']['url'] . '"></a></td>';
            
            $tableBody .= '<td><a href="https://www.youtube.com/watch?v=' . $searchResult['id']['videoId'] . '" target="_blank">' . $searchResult['snippet']['title'] . '</a></td>';
            
            $tableBody .= '<td><a href="https://www.youtube.com/channel/' . $searchResult['snippet']['channelId'] . '" target="_blank">' . $searchResult['snippet']['channelTitle'] . '</td>';
            
            $tableBody .= '<td>' . date("Y-m-d", strtotime($searchResult['snippet']['publishedAt'])) . '</td>';
            
            $tableBody .= '</tr>';
        }
    }
    catch (Google_Service_Exception $e) {
        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
    }
    catch (Google_Exception $e) {
        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>YouTube Search Lister</title>
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
  
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
  <script src="//code.jquery.com/jquery-3.3.1.js"></script>
  <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    
  <style type="text/css">
    body {
      overflow-x: hidden;
      padding: 5%;
    }
    h2 {
      text-align: center;
    }
    #youtube_search 
    {
      table-layout:fixed;
      width: 98% !important; 
    }
    #youtube_search td
    {
        text-align: center; 
    }
    #youtube_search th
    {
        text-align: center; 
    }
    .form-control-borderless {
    border: none;
    }

    .form-control-borderless:hover, .form-control-borderless:active, .form-control-borderless:focus {
        border: none;
        outline: none;
        box-shadow: none;
    }
  </style>
</head>
<body>
<div class="container">
    
    <h2>Youtube Video Lister</h2>

    <br/>
    <form method="GET">
      <div class="row justify-content-center">
                          <div class="col-12 col-md-10 col-lg-8">
                              <form class="card card-sm">
                                  <div class="card-body row no-gutters align-items-center">
                                      <div class="col-auto">
                                          <i class="fas fa-search h4 text-body"></i>
                                      </div>
                                      <div class="col">
                                          <input class="form-control form-control-lg form-control-borderless" type="search" id="search_query" name="search_query" placeholder="Search topics or keywords" value="<?php echo $searchQuery; ?>">
                                      </div>
                                      <div class="col-auto">
                                          <button class="btn btn-lg btn-success" type="submit">Search</button>
                                      </div>
                                  </div>
                              </form>
                          </div>
                      </div>
      </div>
    </form>

    <table id="youtube_search" class="display table table-striped table-bordered">
      <thead>
          <tr>
              <th width="20%">Thumbnail</th>
              <th width="50%">Title</th>
              <th width="20%">Uploaded by</th>
              <th width="10%">Date</th>
          </tr>
      </thead>
      <tbody>
      <?=$tableBody?>
      </tbody>
    </table>
    <script type="text/javascript">
      $(document).ready( function () {
        $('#youtube_search').DataTable();
      } );
    </script>
</div>
</body>
</html>