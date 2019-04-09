<?php
function kinerja_arsip_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
			
				//drupal_set_message('filter');
				//drupal_set_message(arg(5));
				
				$kodeuk = arg(2);
				$sudah = arg(3);
				$katakunci = arg(4);

				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {

		$kodeuk = '##';
		$sudah = '##';
		$katakunci = '';
		
		//$kodeuk = $_SESSION["kinerja_arsip_kodeuk"];
		//if ($kodeuk=='') $kodeuk = '##';
		
		//$sudah = $_SESSION["kinerja_arsip_sudah"];
		//if ($sudah=='') $sudah = '##';
		
	}

	if (isUserSKPD()) $kodeuk = apbd_getuseruk();
	
	$output_form = drupal_get_form('kinerja_arsip_main_form');
	
	if (isUserSKPD())
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Kinerja', 'valign'=>'top'),
			array('data' => 'Hasil', 'valign'=>'top'),
			array('data' => 'Anggaran', 'width' => '90px', 'field'=> 'totalp',  'valign'=>'top'),
			array('data' => 'Realisasi', 'width' => '90px', 'field'=> 'realisasi',  'valign'=>'top'),
			array('data' => 'Prsn', 'width' => '40px', 'field'=> 'persen', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
	else
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kinerja', 'valign'=>'top'),
			array('data' => 'Hasil', 'valign'=>'top'),
			array('data' => 'Anggaran', 'width' => '90px', 'field'=> 'totalp',  'valign'=>'top'),
			array('data' => 'Realisasi', 'width' => '90px', 'field'=> 'realisasi',  'valign'=>'top'),
			array('data' => 'Prsn', 'width' => '40px', 'field'=> 'persen', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
	

	$query = db_select('kegiatanperubahan', 'k')->extend('PagerDefault')->extend('TableSort');
	$query->join('unitkerja', 'u', 'k.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('k', array('kodekeg', 'kegiatan', 'totalp', 'realisasi', 'hasilsasaran', 'hasiltarget', 'hasilrealisasi', 'kodeuk', 'sudah', 'keterangan'));
	$query->fields('u', array('namasingkat'));
	$query->addExpression('(realisasi/totalp)*100', 'persen');
	$query->condition('k.inaktif', 0, '=');
	$query->condition('k.totalp', 0, '>');
	
	if ($kodeuk !='##') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($sudah !='##') $query->condition('k.sudah', $sudah, '=');
	if ($katakunci!='') $query->condition('k.kegiatan', '%' . db_like($katakunci) . '%', 'LIKE');
	
	$query->orderByHeader($header);
	$query->orderBy('k.kegiatan', 'ASC');
	$query->limit($limit);
		
	//dpq($query);
	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 

	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		
		//icon
		if($data->sudah=='1')
			$proses = apbd_icon_sudah();
		else
			$proses = apbd_icon_belum();
		
		$editlink = apbd_button_jurnal('kinerja/edit/' . $data->kodekeg);
		//$persen = apbd_hitungpersen($data->totalp, $data->realisasi);
		
		if ($data->persen<80) {
			$str_persen = '<p style="color:red">' . apbd_fn2($data->persen) . '</p>';
			$kegiatan = '<p style="color:red">' . $data->kegiatan . '</p>';
			
			$kinerja = '<p style="color:red">' . $data->hasilsasaran . ' <em>(' . $data->hasiltarget . ')</p>';
			$hasil = '<p style="color:red">' . $data->hasilrealisasi . '</em></br><smaller>' . $data->keterangan . '</smaller></p>';
			
			$realisasi = '<p style="color:red">' . apbd_fn($data->realisasi) . '</p>';
			
		} else {
			$str_persen = apbd_fn2($data->persen);
			$kegiatan = $data->kegiatan;

			$kinerja = $data->hasilsasaran . ' <em>(' . $data->hasiltarget . ')</em>';
			$hasil = $data->hasilrealisasi;
			
			$realisasi =  apbd_fn($data->realisasi);
		}
		
		if (isUserSKPD())
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses,'align' => 'right', 'valign'=>'top'),
						array('data' => $kegiatan,'align' => 'left', 'valign'=>'top'),
						array('data' => $kinerja,'align' => 'left', 'valign'=>'top'),						
						array('data' => $hasil,'align' => 'left', 'valign'=>'top'),						
						array('data' => apbd_fn($data->totalp),'align' => 'right', 'valign'=>'top'),
						array('data' => $realisasi,'align' => 'right', 'valign'=>'top'),
						array('data' => $str_persen,'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
		else
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses,'align' => 'right', 'valign'=>'top'),
						array('data' => $kegiatan,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->namasingkat,'align' => 'left', 'valign'=>'top'),
						array('data' => $kinerja,'align' => 'left', 'valign'=>'top'),						
						array('data' => $hasil,'align' => 'left', 'valign'=>'top'),						
						array('data' => apbd_fn($data->totalp),'align' => 'right', 'valign'=>'top'),
						array('data' => $realisasi,'align' => 'right', 'valign'=>'top'),
						array('data' => $str_persen,'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	
	//BUTTON
	if ($kodeuk =='##') 
		$btn = '';
	else
		$btn = apbd_button_print('/kinerjaprint/' . $kodeuk );
	//$btn .= "&nbsp;" . apbd_button_excel('');	
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(3)=='pdf'){
		//$output=getData($kodeuk,$bulan,$jenisdokumen,$keyword);
		//print_pdf_l($output);

		$output = getLaporankinerja($kodeuk);
		//$output2= footer();
		apbd_ExportPDF('L', 'F4', $output, 'lap-kinerja-2016', 1, 30);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
}


function getData($kodeuk,$bulan,$jenisdokumen,$keyword){
	
}

function kinerja_arsip_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$sudah = $form_state['values']['sudah'];
	$katakunci = $form_state['values']['katakunci'];
	//if ($katakunci='') $katakunci = 'no
	
	$_SESSION["kinerja_arsip_kodeuk"] = $kodeuk;
	$_SESSION["kinerja_arsip_sudah"] = $sudah;
	$_SESSION["start"] = 10;
	
	$uri = 'kinerjaarsip/filter/' . $kodeuk . '/' . $sudah . '/' . $katakunci ;
	drupal_goto($uri);
	
}


function kinerja_arsip_main_form($form, &$form_state) {

	if(arg(2)!=null){
		
		$kodeuk =arg(2);
		$sudah =arg(3);
		$katakunci =arg(4);

	} else {
		
		$kodeuk = '##';
		$sudah = '##';
		$katakunci = '';
		/*
		$kodeuk = $_SESSION["kinerja_arsip_kodeuk"];
		if ($kodeuk=='') $kodeuk = '##';

		$sudah = $_SESSION["kinerja_arsip_sudah"];
		if ($sudah=='') $sudah = '##';
		*/
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		//'#title'=>  'PILIHAN DATA',
		'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right">' . get_label_data($kodeuk, $sudah) . '</small></em>',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['formdata']['kodeuk'] = array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);				
	} else {
		//SKPD
		$query = db_select('unitkerja', 'p');
		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');
		# execute the query
		$results = $query->execute();
		# build the table fields
		$option_skpd['##'] = 'SELURUH SKPD';
		if($results){
			foreach($results as $data) {
			  $option_skpd[$data->kodeuk] = $data->namasingkat; 
			}
		}		
		$form['formdata']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			'#description'  => 'Pilih salah satu SKPD yang ditampilkan. Bila ingin menampilkan semua SKPD, pilih Seluruh SKPD', 
			'#options' => $option_skpd,
			'#default_value' => $kodeuk,
		);				
	} 
	$opt_jurnal['##'] ='SEMUA';
	$opt_jurnal['0'] = 'BELUM DIISI';
	$opt_jurnal['1'] = 'SUDAH DIISI';	
	$form['formdata']['sudah'] = array(
		'#type' => 'select',
		'#title' =>  t('Status'),
		'#description'  => 'Bisa memilih semua kegiatan, atau hanya kegiatan yang belum/sudah diisi kinerjanya', 
		'#options' => $opt_jurnal,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sudah,
	);	 

	$form['formdata']['katakunci']= array(
		'#type'         => 'textfield', 
		'#title' =>  t('Kata Kunci'),
		'#description'  => 'Isikan kata kunci untuk menampilkan kegiatan-kegiatan tertentu', 
		//'#required' => TRUE,
		'#default_value'=> $katakunci, 
	);				
	

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}

function get_label_data($kodeuk, $sudah) {
if (isUserSKPD())	
	$label= '';
else {
	if ($kodeuk == '##') 
		$label = 'Seluruh SKPD/';
	else {
		$query = db_select('unitkerja', 'p');
		# get the desired fields from the database
		$query->fields('p', array('namasingkat'));
		$query->condition('p.kodeuk', $kodeuk, '=');
		# execute the query
		$results = $query->execute();
		# build the table fields
		if($results){
			foreach($results as $data) {
				$label = $data->namasingkat . '/';
			}
		}		
		
	}
}
	
if ($sudah=='##')
	$label .= 'Semua';
else if ($sudah=='0')	
	$label .= 'Belum Diisi';
else if ($sudah=='1')	
	$label .= 'Sudah Diisi';

$label .= ' (Klik disini untuk memilih kegiatan)';
return $label;
}

function getLaporankinerja($kodeuk){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';

	//SKPD
	$query = db_select('unitkerja', 'p');
	# get the desired fields from the database
	$query->fields('p', array('namauk','kodeuk','kodedinas', 'pimpinannama', 'pimpinanjabatan', 'pimpinannip'));
	$query->condition('p.kodeuk', $kodeuk, '=');
	# execute the query
	$results = $query->execute();
	# build the table fields
	if($results){
		foreach($results as $data) {
			$namauk = $data->namauk; 
			$kodedinas = $data->kodedinas; 
			$pimpinannama = $data->pimpinannama; 
			$pimpinanjabatan = $data->pimpinanjabatan; 
			$pimpinannip = $data->pimpinannip;
		}
	}		
	
	$header=array();
	$rows[]=array(
		array('data' => 'LAPORAN KINERJA SATUAN PERANGKAT DAERAH', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => $namauk, 'width' => '875px','align'=>'center','style'=>'font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN ANGGARAN 2016', 'width' => '875px','align'=>'center','style'=>'font-size:120%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$output = theme_box('', apbd_theme_table($header, $rows));
	//$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$rows=array();
	$header[]=array(
		array('data' => 'KODE', 'rowspan'=>'2', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;border-left:1px solid black;'),
		array('data' => 'PROGRAM KEGIATAN', 'rowspan'=>'2', 'colspan'=>'2', 'width' => '265px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' => 'BELANJA', 'width' => '168px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'HASIL / KELUARAN', 'width' => '292px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'KETERANGAN', 'rowspan'=>'2', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	
	$header[]=array(
		array('data' => 'Anggaran', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Realisasi', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		
		array('data' => 'Rencana', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Realisasi', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
	);
	
	$header[]=array(
		array('data' => '1', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '2', 'width' => '265px','align'=>'center', 'colspan'=>'2',  'style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		
		array('data' => '3', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '4', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		array('data' => '5', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '6', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		array('data' => '8', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	$total_anggaran = 0; $total_realisasi = 0;
	
	$result = db_query('SELECT distinct k.kodepro as pro,(select sum(l.totalp) from kegiatanperubahan as l where l.kodepro=pro and l.kodeuk= :kodeuk) as anggaran,(select sum(l.realisasi) from kegiatanperubahan as l where l.kodepro=pro and l.kodeuk= :kodeuk) as realisasi,p.program FROM kegiatanperubahan as k inner join program as p on k.kodepro=p.kodepro where k.totalp>0 and k.kodeuk= :kodeuk order by k.kodepro', array(':kodeuk' => $kodeuk));
	// Result is returned as a iterable object that returns a stdClass object on each iteration
	foreach ($result as $data) {
		
		$total_anggaran += $data->anggaran; $total_realisasi += $data->realisasi;	
		
		$rows[]=array(
				array('data' => $data->pro, 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => $data->program, 'width' => '265px', 'colspan'=>'2', 'align'=>'left','style'=>'font-weight:bold;border-right:1px solid black;'),
				
				array('data' => apbd_fn($data->anggaran), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;'),
				array('data' => apbd_fn($data->realisasi), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				
				array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
			
			);
		$resultk = db_query('SELECT k.kodekeg ,k.kegiatan, k.nomorkeg, k.hasilsasaran, k.keluaransasaran ,k.hasiltarget, k.hasilrealisasi, k.keluarantarget, k.keluaranrealisasi,k.totalp,k.realisasi,k.keterangan FROM kegiatanperubahan  k where  k.totalp>0 and k.kodepro= :kodepro and kodeuk= :kodeuk order by k.kodekeg', array(':kodepro' => $data->pro,':kodeuk' => $kodeuk));
		// Result is returned as a iterable object that returns a stdClass object on each iteration
		foreach ($resultk as $datak) {
			$rows[]=array(
				array('data' => $data->pro. '.'. $datak->nomorkeg, 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => $datak->kegiatan, 'width' => '265px', 'colspan'=>'2', 'align'=>'left','style'=>'border-right:1px solid black;'),
				
				array('data' => apbd_fn($datak->totalp), 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($datak->realisasi), 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				
				array('data' => $datak->keterangan, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;'),
				
			);
			$rows[]=array(
				array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => '-', 'width' => '15px','align'=>'center','style'=>'border:none;'),
				array('data' => 'Dana APBD Kabupaten Jepara Tahun 2016', 'width' => '250px','align'=>'left','style'=>'font-style:italic; border-right:1px solid black;'),
				
				array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($datak->totalp), 'width' => '146px','align'=>'right','style'=>'font-style:italic;border-right:1px solid black;'),
				array('data' => apbd_fn($datak->realisasi), 'width' => '146px','align'=>'right','style'=>'font-style:italic;border-right:1px solid black;'),
				
				array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
				
			);
			if ($datak->keluaransasaran!='') {
				$rows[]=array(
					array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
					array('data' => '-', 'width' => '15px','align'=>'center','style'=>'border:none;'),
					array('data' => $datak->keluaransasaran, 'width' => '250px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => $datak->keluarantarget, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					array('data' => $datak->keluaranrealisasi, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
					
				);
			}
			if ($datak->hasilsasaran!='') {
				$rows[]=array(
					array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
					array('data' => '-', 'width' => '15px','align'=>'center','style'=>'border:none;'),
					array('data' => $datak->hasilsasaran, 'width' => '250px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => $datak->hasiltarget, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					array('data' => $datak->hasilrealisasi, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
					
				);
			}
		}
	
	}
	
	//TOTAL
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;'),
			array('data' => 'TOTAL', 'width' => '265px', 'colspan'=>'2', 'align'=>'left','style'=>'font-weight:bold;font-weight:bold;border-right:1px solid black;border-top:1px solid black;'),
			
			array('data' => apbd_fn($total_anggaran), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;border-top:1px solid black;'),
			array('data' => apbd_fn($total_realisasi), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-top:1px solid black;'),
			array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-top:1px solid black;'),
			
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;border-top:1px solid black;'),
		
		);
	
	$rows[]=array(
		array('data' => '', 'width' => '875px','align'=>'center','style'=>'border-top:1px solid black;'),
		
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ..................','width' => '440px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => $pimpinanjabatan,'width' => '440px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => $pimpinannama,'width' => '440px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. ' . $pimpinannip,'width' => '440px', 'align'=>'center','style'=>'border:none;'),
	);
	$output .= theme_box('', apbd_theme_table($header, $rows));
	//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

?>
