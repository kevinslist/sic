<?php

//fix_the_files(getcwd());


function fix_the_files($directory_to_scan = null){
  print 'CHECKING FOR DUPS: ' . $directory_to_scan . "\r\n\r\n";
  $found_dups = false;
  $file_name_array = array();
  $files = scandir($directory_to_scan);

  foreach($files as $f){
    if($f != '.' && $f != '..'){
      $file_name_array[strtolower($f)][] = $f;
    }
  }
  foreach($file_name_array as $file_id => $files){
    if(count($files) > 1){
      $found_dups = true;
	    rename_dup_files($directory_to_scan, $files);
    }
  }
  if($found_dups){
    fix_the_files($directory_to_scan);
  }else{
    $files = scandir($directory_to_scan);
    foreach($files as $f){
      if($f != '.' && $f != '..'){
        $full_path = $directory_to_scan . DIRECTORY_SEPARATOR . $f;
        if(is_dir($full_path)){
          fix_the_files($full_path);
        }else{
          print 'ND: ' . $full_path . "\r\n";
        }
      }
    }
  }
}

function rename_dup_files($directory_to_scan, $files){
  $i = 0;
  foreach($files as $f){
    $i++;
    $full_path = $directory_to_scan . DIRECTORY_SEPARATOR . $f;
    if(is_dir($full_path)){
      $new_name = $full_path . '_' . $i;
      rename($full_path, $new_name);
      print  'ISDIR: ' . $new_name . "\r\n";
    }else{
      $ext = strrchr($f, '.');
      if($ext){
        $old_file_name = strrrchr($f, '.');
        $new_name = $old_file_name . $i . $ext;
        print 'ISFILE: ' . $new_name . "\r\n";
      }else{
        $old_file_name = $f;
        $new_name = $old_file_name . '_' . $i;
        print 'ISFILE: ' . $new_name . "\r\n";
      }
      rename($full_path, $directory_to_scan . DIRECTORY_SEPARATOR . $new_name);
    }
  }
}


function strrrchr($haystack,$needle)
{

    // Returns everything before $needle (inclusive).
    return substr($haystack,0,strrpos($haystack,$needle)+1);
    
}

