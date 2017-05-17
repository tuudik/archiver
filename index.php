<?php
/** This script is for HTTP logs archiving **/
/** Created by Marko Valing / Tuudik       **/
/** https://github.com/tuudik/archiver     **/

// Configure directory where to upload the files
$output_dir = "upload/";

$phpFileUploadErrors = array(
    0 => ' X - There is no error, the file uploaded with success',
    1 => ' X - The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => ' X - The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => ' X - The uploaded file was only partially uploaded',
    4 => ' X - No file was uploaded',
    6 => ' X - Missing a temporary folder',
    7 => ' X - Failed to write file to disk',
    8 => ' X - A PHP extension stopped the file upload',
    9 => ' X - Logs that are being sent are not compressed. Enable compressing',
    10 => ' X - Function "updateFolders" failed',
    11 => ' X - Failure! Check nginx error.log',
);

function archiveLog($fileName) {
  global $output_dir;
  echo "Archiving logs", PHP_EOL;
  return move_uploaded_file($_FILES["fail"]["tmp_name"],$output_dir.$fileName);
}

function checkMime($tmpFileName) {
  $fileInfo = finfo_open( FILEINFO_MIME_TYPE );
  $mimeType = finfo_file( $fileInfo, $tmpFileName );
  if($mimeType == ( "application/x-gzip" ) ) {
    return true;
  }
  else {
    return false;
  }
finfo_close( $fileInfo );
}

function errorCall($errCode) {
  global $phpFileUploadErrors;
  echo $phpFileUploadErrors[$errCode];
  http500();
}

function http500(){
  http_response_code(500);
  exit(1);
}

function updateFolders($fileName) {
  global $output_dir;

  echo "Checking if directory is writable: ", PHP_EOL;
  if (!is_writable($output_dir)) {
    echo "\Issue with directory: ".$output_dir, PHP_EOL;
    errorCall(7);
  } else {
    echo " * Check!", PHP_EOL;
  }

  $split = explode("-", $fileName);
  $host = $split[0];
  $year = date('Y');
  $month = date('m');
  $output_dir = $output_dir.$host."/".$year."/".$month."/";

  echo "Checking if directory with date exists(".$output_dir.")", PHP_EOL;
  if (!is_dir($output_dir)) {
    echo "Creating directory with date", PHP_EOL;
    if(!mkdir($output_dir, 0755, true)) { errorCall(7); }
    echo "Directory created -> ";
    echo $output_dir, PHP_EOL;
  } else {
    echo " * Check!", PHP_EOL;
  }
  return true;
}

echo "PHP errors with file uploads: ", PHP_EOL;
if($_FILES['fail']['error']==0 && isset($_FILES["fail"])) {
  echo " * Check!", PHP_EOL;
  $fileName = $_FILES["fail"]["name"];

  echo "Checking if logs compressed:", PHP_EOL;
  if(checkMime($_FILES['fail']['tmp_name'])) {
    echo " * Check!", PHP_EOL;
  } else {
    errorCall(9);
  }

  if(updateFolders($fileName) && archiveLog($fileName)) {
    echo "Logs uploaded successfully!", PHP_EOL;
    http_response_code(200);
    exit;
  } else {
    errorCall(11);
  }

} else {
    echo $phpFileUploadErrors[$_FILES['fail']['error']], PHP_EOL;
}

errorCall(11);

?>
