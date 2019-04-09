<?php

function kinerja_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');

	$kodekeg = arg(2);	
	$print = arg(3);	
	//drupal_set_message($kodekeg);
	if($print=='print') {			  
	
		//$output = printspp_1($kodekeg);
		//apbd_ExportSPP1($output, 'SPP1', $url);
		//print_pdf_p($output);
	
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('kinerja_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function kinerja_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["kinerjalastpage"] = $referer;
	else
		$referer = $_SESSION["kinerjalastpage"];
	//drupal_set_message($referer);
	

	$kodekeg = arg(2);
	$query = db_select('kegiatanperubahan', 'd');
	$query->fields('d', array('kodekeg', 'kodeuk', 'kegiatan', 'totalp', 'realisasi',
			'keluaransasaran', 'keluarantarget', 'keluaranrealisasi', 'hasilsasaran', 'hasiltarget', 'hasilrealisasi', 'keterangan'));
	$query->addExpression('(realisasi/totalp)*100', 'persen');
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.kodekeg', $kodekeg, '=');
	
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = $data->kegiatan . ' (' . apbd_fn2 ($data->persen) . '%)';
		
		$kodekeg = $data->kodekeg;		
		$kodeuk = $data->kodeuk;
		$persen = $data->persen;

		//$kegiatan = $data->kegiatan;
		
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$keluaranrealisasi = $data->keluaranrealisasi;
		if ($keluaranrealisasi=='') $keluaranrealisasi = $keluarantarget;

		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		$hasilrealisasi = $data->hasilrealisasi;
		if ($hasilrealisasi=='') $hasilrealisasi = $hasiltarget;
		
		$totalp = $data->totalp;
		$realisasi = $data->realisasi;
		
		$keterangan = $data->keterangan;
	}
	
	drupal_set_title($title);

		
	$form['referer'] = array(
		'#type' => 'value',
		'#value' => $referer,
	);	
	
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);	
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);
	$form['persen'] = array(
		'#type' => 'value',
		'#value' => $persen,
	);





	//KELUARAN
	$form['formkeluaran'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KELUARAN',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formkeluaran']['keluaransasaran']= array(
			'#type'         => 'item', 
			'#title' =>  t('Sasaran'),
			//'#required' => TRUE,
			'#markup'=> '<p>' . $keluaransasaran . '</p>', 
		);				
		$form['formkeluaran']['keluarantarget']= array(
			'#type'         => 'item', 
			'#title' =>  t('Target'),
			//'#required' => TRUE,
			'#markup'=> '<p>' . $keluarantarget . '</p>', 
		);				
		$form['formkeluaran']['keluaranrealisasi']= array(
			'#type'         => 'textarea', 
			'#title' =>  t('Realisasi'),
			
			'#required' => TRUE,
			'#default_value'=> $keluaranrealisasi, 
		);				
		
	//HASIL
	$form['formhasil'] = array (
		'#type' => 'fieldset',
		'#title'=> 'HASIL',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formhasil']['hasilsasaran']= array(
			'#type'         => 'item', 
			'#title' =>  t('Sasaran'),
			//'#required' => TRUE,
			'#markup'=> '<p>' . $hasilsasaran . '</p>', 
		);				
		$form['formhasil']['hasiltarget']= array(
			'#type'         => 'item', 
			'#title' =>  t('Target'),
			'#required' => TRUE,
			'#markup'=> '<p>' . $hasiltarget . '</p>', 
		);				
		$form['formhasil']['hasilrealisasi']= array(
			'#type'         => 'textarea', 
			'#title' =>  t('Realisasi'),
			'#required' => TRUE,
			'#default_value'=> $hasilrealisasi, 
		);				
	
	//HASIL
	$form['formanggaran'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PENGANGGARAN',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formanggaran']['totalp']= array(
			'#type'         => 'item', 
			'#title' =>  t('Anggaran'),
			//'#required' => TRUE,
			'#markup'=> '<p>' . apbd_fn($totalp) . '</p>', 
		);				
		$form['formanggaran']['realisasi']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Realisasi'),
			//'#required' => TRUE,
			'#default_value'=> $realisasi, 
		);	
		$form['formanggaran']['keterangan']= array(
			'#type'         => 'textarea', 
			'#title' =>  t('Keterangan'),
			//'#required' => TRUE,
			'#default_value'=> $keterangan, 
		);			
	
	//NAVIGATION	
	/*
	$form['formdata']['submitprev']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Sebelum',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	$form['formdata']['submitnext']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Sesudah',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	*/
	
	//FORM SUBMIT DECLARATION
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	
	return $form;
}

function kinerja_edit_main_form_validate($form, &$form_state) {
$persen = $form_state['values']['persen'];
if ($persen<80) {
	
	if ($form_state['values']['keterangan']=='') {
		form_set_error('keterangan', 'Untuk kinerja dibawah 80%, keterangan harus diisi');
	}
}
}

function kinerja_edit_main_form_submit($form, &$form_state) {

	$kodekeg = $form_state['values']['kodekeg'];

	$keluaranrealisasi = $form_state['values']['keluaranrealisasi'];
	$hasilrealisasi = $form_state['values']['hasilrealisasi'];
	$realisasi = $form_state['values']['realisasi'];
	$keterangan = $form_state['values']['keterangan'];
	
	$referer = $form_state['values']['referer'];
	
	$query = db_update('kegiatanperubahan')
				->fields( 
					array(
						'realisasi' => $realisasi,
						'keluaranrealisasi' => $keluaranrealisasi,
						'hasilrealisasi' => $hasilrealisasi,
						'keterangan' => $keterangan,
						'sudah' => 1,
						
					)
				);
	$query->condition('kodekeg', $kodekeg, '=');
	$res = $query->execute();
	drupal_goto($referer);
}

function printspp_1($kodekeg){
	
	//READ UP DATA
	$query = db_select('kegiatanperubahan', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('urusan', 'u', 'uk.kodeu=u.kodeu');
	
	$query->fields('d', array('kodekeg', 'sppno', 'spptgl', 'kodekeg', 'bulan', 'kegiatan', 'totalp',  
			'keluaransasaran', 'keluaranrealisasi', 'hasilsasaran', 'hasiltarget', 'keluarantarget'));
	$query->fields('uk', array('kodeuk', 'kodedinas', 'namauk', 'header1'));
	$query->fields('u', array('kodeu', 'urusan'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.kodekeg', $kodekeg, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$namauk = $data->namauk;
		$unitkerja = $data->kodedinas . '01 - ' . $data->namauk; 
		$alamat = $data->header1;
		$dpa = '...................., .................';
		$bulan = apbd_getbulan($data->bulan);
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$program = $data->kodeu . '.000 - Non Program';
		$kegiatan = $data->kodedinas . '.000.000 - Non Kegiatan';
		
		$totalp = apbd_fn($data->totalp);
		$kegiatan = $data->kegiatan;
		$bendaharanama = $data->keluaransasaran;
		$rekening = $data->hasilsasaran . ' No. Rek . ' . $data->keluaranrealisasi;
		$bendaharanip = $data->keluarantarget;
	}	
	
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Asli', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Pengguna Anggaran/PPK-SKPD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 1', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Kuasa BUD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 2', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 3', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Arsip Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERMINTAAN PEMBAYARAN (SPP)', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => 'NOMOR : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => 'SPP-1', 'width' => '255px','align'=>'left','style'=>'border:none;'),
		array('data' => 'ID : ' . $kodekeg, 'width' => '255px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMBAYARAN PFK [SPP-PFK]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '1.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'SKPD', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $skpd, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Unit Kerja', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $unitkerja, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '3.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Alamat', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '4.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'No. DPA SKPD Tanggal', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $dpa, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '5.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Tahun Anggaran', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => apbd_tahun(), 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '6.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Bulan', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $bulan, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '7.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Urusan Pemerintah', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $urusan, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '8.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Nama Program', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $program, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '9.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Nama Kegiatan', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $kegiatan, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border-top:1px solid black;'),
	);
	$rows[] = array(
		array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
		array('data' => 'Kepada Yth.','width' => '260px', 'align'=>'left','style'=>'border:none;'),
							
	);
	$rows[] = array(
		array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
		array('data' => 'Pengguna Anggaran','width' => '260px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
		array('data' => $namauk, 'width' => '260px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
		array('data' => 'di - ','width' => '260px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '280px', 'align'=>'center','style'=>'border:none;'),
		array('data' => 'Jepara','width' => '230px', 'align'=>'left','style'=>'border:none;'),
	);

	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'Dengan memperhatikan Peraturan Bupati Jepara Nomor 73 Tahun 2016 tentang Penjabaran APBD Kabupaten Jepara, bersama ini kami mengajukan Surat Permintaan Pembayaran sebagai berikut :', 'width' => '510px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	
	$rows[]=array(
		array('data' => 'a.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'totalp Pembayaran yang diminta', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Rp ' . $totalp . ',00', 'width' => '310px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'b.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Untuk kegiatan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $kegiatan, 'width' => '310px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'c.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Nama Bendahara', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $bendaharanama, 'width' => '310px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'd.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Alamat', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '310px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'e.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'No. Rekening Bank', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $rekening, 'width' => '310px','align'=>'left','style'=>'border:none;'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '510px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ' . $spptgl,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Bendahara Pengeluaran','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '510px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '510px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '510px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => $bendaharanama,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. ' . $bendaharanip,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}


?>
