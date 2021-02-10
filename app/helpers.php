<?php


 function download_file($file){
        if (!file_exists($file)){
            header("Content-type: text/html; charset=utf-8");
            echo "File not found!";
            exit;
        } 
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
}



function text_random($length = 20) {
    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
}


/*
*    Debug
*/
function st(...$string){
    foreach ($string as $item) {
        echo '<pre>';
        print_r($item);
        echo '</pre>';
    }
}


/*
*    Debug and die
*/
function sv($string){
    st($string);
    exit;
}

/*
*    Debug and die
*/
function dd($here){
   return sv($here);
}


/*
*   Deleting all files in a folder ( and the hidden files also)
*/
function delete_folders_files($path){
    $path = rtrim($path, '/').'/{,.}*';
    $files = glob($path, GLOB_BRACE); // get all file names
    foreach($files as $file){ // iterate files
      if(is_file($file))
        unlink($file); // delete file
    }
}


function cleanify($post) {
    if(is_array($post)){

        $clean = [];
        foreach ($post as $key => $value):

        if(is_array($value)) {
           $clean[$key] = cleanify($value);
        }else {
            $clean[$key] = clean(($value));   
        }
        endforeach;

        return $clean;
    }
    return clean($post);    
} 
    
    
    function clean($data) {
        // Strip HTML Tags
        $clear = strip_tags($data);
        // Clean up things like &amp;
        $clear = html_entity_decode($clear);
        // Strip out any url-encoded stuff
        $clear = urldecode($clear);
        // Replace Multiple spaces with single space
        $clear = preg_replace('/ +/', ' ', $clear);
        // Trim the string of leading/trailing space
        $clear = trim($clear);
        return $clear;
    }



    
/*
if(file_exists('test.zip')){
    unlink('test.zip');
}
*/

function Zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', realpath($file));

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

