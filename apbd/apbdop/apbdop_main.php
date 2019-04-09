<?php
function apbdop_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 20;
	
	
	//drupal_set_title('PENDAPATAN #' . $bulan);
	
		//$output_form = drupal_get_form('apbdop_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Username',  'field'=> 'username', 'valign'=>'top'), 
		array('data' => 'Nama', 'field'=> 'nama', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
		array('data' => 'Hak Akses', 'field'=> 'rid', 'valign'=>'top'),
		array('data' => 'Akses Terakhir', 'field'=> 'access', 'valign'=>'top'),
		array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);
		
		$query = db_select('apbdop', 'u')->extend('PagerDefault')->extend('TableSort');
		$query->join('unitkerja', 'uk', 'u.kodeuk=uk.kodeuk');
		$query->innerJoin('users', 's', 'u.username=s.name');
		$query->innerJoin('users_roles', 'ur', 's.uid=ur.uid');
		
		# get the desired fields from the database
		$query->fields('u', array('username','nama'));
		$query->fields('uk', array('namasingkat'));
		$query->fields('s', array('access'));
		$query->fields('ur', array('rid'));
		
		if (!isSuperuser()) {
			$kodeuk = apbd_getuseruk();
			$query->condition('uk.kodeuk', $kodeuk, '=');	
			
			/*
			$or = db_or();
			$or->condition('ur.rid', '6', '=');	
			$or->condition('ur.rid', '5', '=');	
			$query->condition($or);			
			*/
		}
		
		$query->orderByHeader($header);
		$query->orderBy('u.username', 'ASC');
		$query->limit($limit);	
		
			
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
			
			//$keterangan = l($data->jumlahrekening . ' Rekening <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>' , 'pendapatanrek/filter/' . $bulan . '/' . $data->kodeskpd . '/' . $kodek . '/5' , array ('html' => true, 'attributes'=> array ('class'=>'text-success pull-right')));
			
			$username = l($data->username, 'operator/edit/' . $data->username , array ('html' => true));
			
			if ($data->access==0)
				$access = 'Belum Pernah';
			else
				$access = gmdate("d M Y", $data->access);
			
			$editlink =  apbd_button_hapus('operator/delete/' . $data->username);
			
			
			if ($data->rid=='3')
				$ha = 'Administrator';
			else if ($data->rid=='4')	
				$ha = 'Superuser';
			else if ($data->rid=='5')	
				$ha = 'SKPD';
			else if ($data->rid=='6')	
				$ha = 'Bidang';
			else
				$ha = '';
			
			$rows[] = array(
							array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
							array('data' => $username, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->nama, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
							array('data' => $ha, 'align' => 'left', 'valign'=>'top'),
							array('data' => $access, 'align' => 'left', 'valign'=>'top'),
							array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
						);
		}

		//BUTTON
		$btn = apbd_button_baru('operator/edit');
		//$btn .= "&nbsp;" . apbd_button_excel('');	
		//$btn .= apbd_button_chart('pendapatan/chart/' . $bulan.'/##/jenis_rb');
		
		
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		$output .= theme('pager');

		//return drupal_render($output_form) . $btn . $output . $btn;
		return $btn . $output . $btn;
}

function apbdop_main_form_submit($form, &$form_state) {
	/*
	$bulan= $form_state['values']['bulan'];
	$kodek = $form_state['values']['kodek'];
	
	$uri = 'pendapatan/filter/' . $bulan.'/' . $kodek;
	drupal_goto($uri);	
	*/
}

function apbdop_main_form($form, &$form_state) {
	
	/*
	$kodeuk = '##';
	$keyword = '';
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('m');
	$kodek = '##';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		
		$kodek = arg(3);
		$keyword = arg(4);
	}
	
	if ($kodek=='41') 
		$kelompok = '|PENDAPATAN ASLI DAERAH';
	else if ($kodek=='42') 
		$kelompok = '|DANA PERIMBANGAN';
	else if ($kodek=='43') 
		$kelompok = '|LAIN-LAIN PENDAPATAN YANG SAH';
	else
		$kelompok = '|SEMUA PENDAPATAN';

	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		//'#title'=> '<p>' . $bulan . $namasingkat . $kelompok . '</p>' . '<p><em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em></p>',
		'#title'=> $bulan . $kelompok . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	

	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => array(	
			 '1' => t('JANUARI'), 	
			 '2' => t('FEBRUARI'),
			 '3' => t('MARET'),	
			 '4' => t('APRIL'),	
			 '5' => t('MEI'),	
			 '6' => t('JUNI'),	
			 '7' => t('JULI'),	
			 '8' => t('AGUSTUS'),	
			 '9' => t('SEPTEMBER'),	
			 '10' => t('OKTOBER'),	
			 '11' => t('NOVEMBER'),	
			 '12' => t('DESEMBER'),	
		   ),
	);

	$form['formdata']['kodek']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kodek,
		
		'#options' => array(	
			 '##' => t('SEMUA'), 	
			 '41' => t('PENDAPATAN ASLI DAERAH'), 	
			 '42' => t('DANA PERIMBANGAN'),
			 '43' => t('LAIN-LAIN PENDAPATAN YANG SAH'),	
		   ),
	);		

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	
	$form['formdata']['submitx'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
	*/
}

?>
