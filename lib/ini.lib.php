<?PHP error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
function get_parse_ini($file)
{

    // if cannot open file, return false
    if (!is_file($file))
        return false;

    $ini = file($file);

    // to hold the categories, and within them the entries
    $cats = array();

    foreach ($ini as $i) {
        if (@preg_match('/\[(.+)\]/', $i, $matches)) {
            $last = $matches[1];
        } elseif (@preg_match('/(.+)=(.+)/', $i, $matches)) {
            $cats[$last][trim($matches[1])] = trim($matches[2]);
        }
    }

    return $cats;

}

function put_ini_file($file, $array, $i = 0){
  $str="";
  foreach ($array as $k => $v){
    if (is_array($v)){
      $str.=str_repeat(" ",$i*2)."[$k]\r\n";
      $str.=put_ini_file("",$v, $i+1);
    }else
      $str.=str_repeat(" ",$i*2)."$k = $v\r\n";
  }
 
  $phpstr = "<?PHP\r\n/*\r\n".$str."*/\r\n?>";
 
 if($file)
    return file_put_contents($file,$phpstr);
  else
    return $str;
}
?>

<?PHP
 function cek_img_tag($text) {
		//membuat auto thumbnails
		preg_match("/src=\"(.+)\"/",$text,$cocok);
		$patern= explode("\"",$cocok['1']) or die ('error');
		$img = str_replace("\"/>","",$patern[0]);
		$img = str_replace("../","",$img);
		$img = str_replace("/>","",$img);
		if($img=="")
		{
		$img="";
		}
		else
		{
		$new='<img src="'.$img.'" class="card-img-top"/>';
		$img=$new;
		}
		
		return $img;
	}?>

<?PHP  function dateindo($tanggal)
{
 
    $format = array(
        'Sun' => 'Minggu',
        'Mon' => 'Senin',
        'Tue' => 'Selasa',
        'Wed' => 'Rabu',
        'Thu' => 'Kamis',
        'Fri' => 'Jumat',
        'Sat' => 'Sabtu',
        'Jan' => 'Januari',
        'Feb' => 'Februari',
        'Mar' => 'Maret',
        'Apr' => 'April',
        'May' => 'Mei',
        'Jun' => 'Juni',
        'Jul' => 'Juli',
        'Aug' => 'Agustus',
        'Sep' => 'September',
        'Oct' => 'Oktober',
        'Nov' => 'November',
        'Dec' => 'Desember'
    );
 
    return strtr($tanggal, $format);
} ?>